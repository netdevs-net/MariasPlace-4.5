<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Abstract Cart.
 *
 * Provides a consistent interface for performing tax lookups for a cart.
 * Extended by both SST_Checkout and SST_Order.
 *
 * @author  Simple Sales Tax
 * @package SST
 * @since   5.0
 */
abstract class SST_Abstract_Cart {

	/**
	 * TaxCloud API ID.
	 *
	 * @var string
	 */
	protected $api_id;

	/**
	 * TaxCloud API Key.
	 *
	 * @var string
	 */
	protected $api_key;

	/**
	 * Constructor.
	 *
	 * Sets the TaxCloud API ID and API Key.
	 */
	public function __construct() {
		$this->api_id  = SST_Settings::get( 'tc_id' );
		$this->api_key = SST_Settings::get( 'tc_key' );
	}

	/**
	 * Perform a tax lookup and update the sales tax for all items.
	 *
	 * @return bool True on success, false on error.
	 * @since 5.0
	 */
	public function calculate_taxes() {
		$this->reset_taxes();

		// No API Login ID or API Key? Bail.
		if ( empty( $this->api_id ) || empty( $this->api_key ) ) {
			SST_Logger::add( 'API Login ID or API Key is empty. Skipping lookup.' );

			return false;
		}

		// Perform tax lookup(s).
		foreach ( $this->do_lookup() as $package ) {
			$response = $package['response'];

			if ( ! is_wp_error( $response ) ) {
				$cart_items = current( $package['response'] );

				foreach ( $cart_items as $index => $tax_total ) {
					$info = $package['map'][ $index ];

					switch ( $info['type'] ) {
						case 'shipping':
							$this->set_shipping_tax( $info['cart_id'], $tax_total );
							break;
						case 'line_item':
							$this->set_product_tax( $info['cart_id'], $tax_total );
							break;
						case 'fee':
							$this->set_fee_tax( $info['cart_id'], $tax_total );
					}
				}
			} else {
				$this->handle_error(
					sprintf(
						/* translators: error message from TaxCloud API response. */
						__( 'Failed to calculate sales tax: %s', 'simple-sales-tax' ),
						$response->get_error_message()
					)
				);

				return false;
			}
		}

		// Update tax totals.
		$this->update_taxes();

		return true;
	}

	/**
	 * Perform a tax lookup for each cart shipping package.
	 *
	 * @return array Array of packages.
	 * @since 5.0
	 */
	protected function do_lookup() {
		$packages = array();

		foreach ( $this->create_packages() as $package ) {
			$hash          = $this->get_package_hash( $package );
			$saved_package = $this->get_saved_package( $hash );

			if ( false === $saved_package ) {
				$saved_package = $this->do_package_lookup( $package );

				if ( $saved_package ) {
					$this->save_package( $hash, $saved_package );
				}
			}

			if ( $saved_package ) {
				$packages[] = $saved_package;
			}
		}

		if ( apply_filters( 'wootax_save_packages_for_capture', true ) ) {
			$this->set_packages( $packages );
		}

		return $packages;
	}

	/**
	 * Perform a tax lookup for a shipping package.
	 *
	 * @param array $package Package to perform tax lookup for.
	 *
	 * @return bool|array False if the package is not ready for lookup, or the updated package otherwise.
	 */
	protected function do_package_lookup( $package ) {
		if ( ! $this->ready_for_lookup( $package ) ) {
			return false;
		}

		try {
			$package['request']  = $this->get_lookup_for_package( $package );
			$package['response'] = TaxCloud()->Lookup( $package['request'] );
			$package['cart_id']  = key( $package['response'] );
		} catch ( Exception $ex ) {
			$package['response'] = new WP_Error( 'lookup_error', $ex->getMessage() );
		}

		return $package;
	}

	/**
	 * Generate a Lookup request for a given package.
	 *
	 * @param array $package Package to construct Lookup request for.
	 *
	 * @return TaxCloud\Request\Lookup
	 * @since 5.0
	 */
	protected function get_lookup_for_package( &$package ) {
		$cart_items = array();
		$based_on   = SST_Settings::get( 'tax_based_on' );

		/* Add products */
		foreach ( $package['contents'] as $cart_id => $item ) {

			$line_total       = $item['line_total'];
			$discounted_price = round( $line_total / $item['quantity'], wc_get_price_decimals() );

			/* Set quantity and price according to 'Tax Based On' setting. */
			if ( 'line-subtotal' === $based_on ) {
				$quantity = 1;
				$price    = $line_total;
			} else {
				$quantity = $item['quantity'];
				$price    = $discounted_price;
			}

			/* Give devs a chance to change the taxable product price. */
			$price = apply_filters( 'wootax_product_price', $price, $item['data'] );

			$cart_items[]     = new TaxCloud\CartItem(
				count( $cart_items ),
				$item['variation_id'] ? $item['variation_id'] : $item['product_id'],
				SST_Product::get_tic( $item['product_id'], $item['variation_id'] ),
				$price,
				$quantity
			);
			$package['map'][] = array(
				'type'    => 'line_item',
				'id'      => $item['data']->get_id(),
				'cart_id' => isset( $item['shipping_item_key'] ) ? $item['shipping_item_key'] : $item['key'],
			);
		}

		/* Add fees */
		foreach ( $package['fees'] as $cart_id => $fee ) {
			$cart_items[]     = new TaxCloud\CartItem(
				count( $cart_items ),
				$fee->id,
				apply_filters( 'wootax_fee_tic', SST_DEFAULT_FEE_TIC ),
				apply_filters( 'wootax_fee_price', $fee->amount, $fee ),
				1
			);
			$package['map'][] = array(
				'type'    => 'fee',
				'id'      => $fee->id,
				'cart_id' => $cart_id,
			);
		}

		/* Add shipping */
		$shipping_rate  = $package['shipping'];
		$local_delivery = false;

		if ( ! is_null( $shipping_rate ) ) {
			$local_delivery = SST_Shipping::is_local_delivery( $shipping_rate->method_id );

			$cart_items[]     = new TaxCloud\CartItem(
				count( $cart_items ),
				SST_SHIPPING_ITEM,
				apply_filters( 'wootax_shipping_tic', SST_DEFAULT_SHIPPING_TIC ),
				apply_filters( 'wootax_shipping_price', $shipping_rate->cost, $shipping_rate ),
				1
			);
			$package['map'][] = array(
				'type'    => 'shipping',
				'id'      => SST_SHIPPING_ITEM,
				'cart_id' => $shipping_rate->id,
			);
		}

		/* Build Lookup */
		$request = new TaxCloud\Request\Lookup(
			$this->api_id,
			$this->api_key,
			$package['user']['ID'],
			null,
			$cart_items,
			$package['origin'],
			$package['destination'],
			$local_delivery,
			$package['certificate']
		);

		return $request;
	}

	/**
	 * Split package into one or more subpackages, one for each product
	 * origin address.
	 *
	 * @todo This method is doing too much and should be split into smaller methods.
	 *
	 * @param array $package Package.
	 *
	 * @return array Subpackages (empty on error).
	 * @since 5.0
	 */
	protected function split_package( $package ) {
		$packages = array();

		// Convert destination address to Address object.
		try {
			$destination = new TaxCloud\Address(
				isset( $package['destination']['address_1'] ) ? $package['destination']['address_1'] : $package['destination']['address'],
				$package['destination']['address_2'],
				$package['destination']['city'],
				$package['destination']['state'],
				substr( $package['destination']['postcode'], 0, 5 )
			);

			$package['destination'] = SST_Addresses::verify_address( $destination );
		} catch ( Exception $ex ) {
			return array();
		}

		// If the 'origin' key is already set, we can assume that some upstream
		// code (e.g. a marketplace integration) already split the packages by
		// origin address. In this case, we just make sure all required keys are
		// set and return the package as-is.
		if ( isset( $package['origin'] ) && $package['origin'] instanceof SST_Origin_Address ) {
			$origin_id              = $package['origin']->getID();
			$packages[ $origin_id ] = $this->format_package( $package );

			return $packages;
		}

		// Split package into subpackages.
		foreach ( $package['contents'] as $cart_key => $item ) {
			$origin = $this->get_origin_for_product( $item, $package['destination'] );

			if ( ! $origin ) {
				$this->handle_error(
					sprintf(
						/* translators: WooCommerce product ID. */
						__( 'Failed to calculate sales tax: no origin address for product %d.', 'simple-sales-tax' ),
						$item['product_id']
					)
				);

				return array();
			}

			$origin_id = $origin->getID();

			// Create subpackage for origin if need be and associate any
			// shipping charges with the first subpackage.
			if ( ! array_key_exists( $origin_id, $packages ) ) {
				$subpackage = array(
					'origin'      => $origin,
					'destination' => $package['destination'],
				);

				if ( isset( $package['shipping'] ) ) {
					$subpackage['shipping'] = $package['shipping'];
					unset( $package['shipping'] );
				}

				$packages[ $origin_id ] = $this->format_package( $subpackage );
			}

			// Add item to package.
			$packages[ $origin_id ]['contents'][ $cart_key ] = $item;
		}

		return array_values( $packages );
	}

	/**
	 * Formats a SST cart package, ensuring that all required fields are set
	 * and have the correct type.
	 *
	 * @param array $package Cart package.
	 *
	 * @return array
	 */
	protected function format_package( $package ) {
		if ( ! isset( $package['origin'] ) ) {
			$package['origin'] = SST_Addresses::get_default_address();
		} elseif ( ! ( $package['origin'] instanceof SST_Origin_Address ) ) {
			SST_Logger::add(
				__( 'Origin address for shipping package is invalid. Using default origin address from Simple Sales Tax settings.', 'simple-sales-tax' )
			);
			$package['origin'] = SST_Addresses::get_default_address();
		}

		$package['origin'] = SST_Addresses::to_address( $package['origin'] );

		if ( ! isset( $package['certificate'] ) ) {
			$package['certificate'] = $this->get_certificate();
		}

		if ( ! isset( $package['user'] ) ) {
			$package['user'] = array( 'ID' => get_current_user_id() );
		}

		return sst_create_package( $package );
	}

	/**
	 * Get the hash of a shipping package.
	 *
	 * @param array $package WooCommerce cart shipping package.
	 *
	 * @return string
	 * @since 5.0
	 */
	private function get_package_hash( $package ) {
		// Remove data objects so hashes are consistent.
		foreach ( $package['contents'] as $item_id => $item ) {
			unset( $package['contents'][ $item_id ]['data'] );
		}

		// Convert WC_Shipping_Rate to array (shipping will be excluded from hash o.w.).
		if ( is_a( $package['shipping'], 'WC_Shipping_Rate' ) ) {
			$package['shipping'] = array(
				'id'        => $package['shipping']->id,
				'label'     => $package['shipping']->label,
				'cost'      => $package['shipping']->cost,
				'method_id' => $package['shipping']->method_id,
			);
		}

		// Exclude user ID from hash - does not change calculated tax amount.
		unset( $package['user'] );

		return 'sst_pack_' . md5( wp_json_encode( $package ) . WC_Cache_Helper::get_transient_version( 'shipping' ) );
	}

	/**
	 * Get the origin address to use for a given product.
	 *
	 * By default, we use the following procedure to determine the address
	 * to use:
	 *
	 *  1) If there is only one shipment origin for the product, use it.
	 *  2) If there are multiple shipment origins, use one in the customer's state.
	 *  3) If there are no origins in the customers state, use the first  origin.
	 *
	 * @param array            $item        Associative array with details about product.
	 * @param TaxCloud\Address $destination Shipping destination address.
	 *
	 * @return SST_Origin_Address
	 * @since 5.0
	 */
	protected function get_origin_for_product( $item, $destination ) {
		$origins = SST_Product::get_origin_addresses( $item['product_id'] );
		$origin  = null;

		if ( ! empty( $origins ) ) {
			$origin = current( $origins );

			foreach ( $origins as $candidate ) {
				if ( $candidate->getState() === $destination->getState() ) {
					$origin = $candidate;
					break;
				}
			}
		}

		return apply_filters( 'wootax_origin_address', $origin, $item, $destination );
	}

	/**
	 * Can we perform a lookup for the given package?
	 *
	 * @param array $package WooCommerce shipping package.
	 *
	 * @return bool
	 * @since 5.0
	 */
	protected function ready_for_lookup( $package ) {
		$dest_valid   = isset( $package['destination'] ) && SST_Addresses::is_valid( $package['destination'] );
		$origin_valid = isset( $package['origin'] ) && $package['origin'] instanceof TaxCloud\Address;

		return $origin_valid && $dest_valid;
	}

	/**
	 * Checks whether a cart package has a valid destination for TaxCloud
	 * lookup requests.
	 *
	 * @param array $package Cart package.
	 *
	 * @return bool True if cart package destination is valid, else false.
	 * @since 6.2.2
	 */
	public function is_package_destination_valid( $package ) {
		$valid = true;

		if ( ! isset( $package['destination'], $package['destination']['country'] ) ) {
			$valid = false;
		} else {
			if ( 'US' !== $package['destination']['country'] ) {
				$valid = false;
			}
		}

		return apply_filters( 'sst_is_package_destination_valid', $valid, $package );
	}

	/**
	 * Get saved packages for this cart.
	 *
	 * @return array
	 * @since 5.0
	 */
	abstract protected function get_packages();

	/**
	 * Set saved packages for this cart.
	 *
	 * @param array $packages Packages to save (default: array()).
	 *
	 * @since 5.0
	 */
	abstract protected function set_packages( $packages = array() );

	/**
	 * Get the base packages for the cart. The base packages are split by origin
	 * and otherwise processed to generate the packages sent to TaxCloud.
	 *
	 * The packages returned by this method must satisfy the following criteria.
	 *
	 * Completeness: The keys 'contents', 'user', 'shipping', and 'destination'
	 * must be defined.
	 *
	 * Inclusiveness: Every item in the cart must be included in exactly one
	 * package.
	 *
	 * @return array
	 * @since 5.5
	 */
	abstract protected function get_base_packages();

	/**
	 * Create shipping packages for this cart.
	 *
	 * Every package should have the following structure:
	 *
	 *  array(
	 *      'contents'      => array(
	 *          ...
	 *          array(
	 *              'variation_id' => 123,
	 *              'product_id'   => 456,
	 *              'quantity'     => 1,
	 *              'data'         => WC_Product,
	 *          )
	 *          ...
	 *      ),
	 *      'fees'          => array(
	 *          ...
	 *          object(
	 *              'id'     => 123,
	 *              'amount' => 2.50,
	 *          )
	 *          ...
	 *      ),
	 *      'shipping'      => array(
	 *          'method_id' => 'local_delivery',
	 *          'cost'      => 9.99,
	 *      ),
	 *      'map'           => array(
	 *          ...
	 *          array(
	 *              'type'    => 'cart'|'shipping'|'fee',
	 *              'id'      => 456,
	 *              'cart_id' => 'abc',
	 *          )
	 *          ...
	 *      ),
	 *      'user'          => array(
	 *          'ID' => 1,
	 *      ),
	 *      'request'       => null,
	 *      'response'      => null,
	 *      'origin'        => TaxCloud\Address,
	 *      'destination'   => TaxCloud\Address,
	 *      'certificate'   => TaxCloud\ExemptCertificate
	 *  )
	 *
	 * @return array
	 * @since 5.0
	 */
	abstract protected function create_packages();

	/**
	 * Gets a saved package by its package hash.
	 *
	 * @param string $hash Package hash.
	 *
	 * @return array|bool The saved package with the given hash, or false if no such package exists.
	 */
	abstract protected function get_saved_package( $hash );

	/**
	 * Saves a package.
	 *
	 * @param string $hash    Package hash.
	 * @param array  $package Package to save.
	 */
	abstract protected function save_package( $hash, $package );

	/**
	 * Reset sales tax totals.
	 *
	 * @since 5.0
	 */
	abstract protected function reset_taxes();

	/**
	 * Update sales tax totals.
	 *
	 * @since 5.0
	 */
	abstract protected function update_taxes();

	/**
	 * Set the tax for a product.
	 *
	 * @param mixed $id  Product ID.
	 * @param float $tax Sales tax for product.
	 *
	 * @since 5.0
	 */
	abstract protected function set_product_tax( $id, $tax );

	/**
	 * Set the tax for a shipping item.
	 *
	 * @param mixed $id  Item ID.
	 * @param float $tax Sales tax for item.
	 *
	 * @since 5.0
	 */
	abstract protected function set_shipping_tax( $id, $tax );

	/**
	 * Set the tax for a fee.
	 *
	 * @param mixed $id  Fee ID.
	 * @param float $tax Sales tax for fee.
	 *
	 * @since 5.0
	 */
	abstract protected function set_fee_tax( $id, $tax );

	/**
	 * Get the exemption certificate for the customer.
	 *
	 * @return TaxCloud\ExemptionCertificateBase
	 * @since 5.0
	 */
	abstract public function get_certificate();

	/**
	 * Handle an error by logging it or displaying it to the user.
	 *
	 * @param string $message Message describing the error.
	 *
	 * @since 5.0
	 */
	abstract protected function handle_error( $message );

}
