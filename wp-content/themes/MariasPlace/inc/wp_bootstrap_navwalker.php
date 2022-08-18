<?php

/**
 * WP Bootstrap Navwalker
 *
 * @package WP-Bootstrap-Navwalker
 *
 * @wordpress-plugin
 * Plugin Name: WP Bootstrap Navwalker
 * Plugin URI:  https://github.com/wp-bootstrap/wp-bootstrap-navwalker
 * Description: A custom WordPress nav walker class to implement the Bootstrap 4 navigation style in a custom theme using the WordPress built in menu manager.
 * Author: Edward McIntyre - @twittem, WP Bootstrap, William Patton - @pattonwebz
 * Version: 4.3.0
 * Author URI: https://github.com/wp-bootstrap
 * GitHub Plugin URI: https://github.com/wp-bootstrap/wp-bootstrap-navwalker
 * GitHub Branch: master
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */
// Check if Class Exists.
if (!class_exists('WP_Bootstrap_Navwalker')) :

    /**
     * WP_Bootstrap_Navwalker class.
     */
    class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {

        /**
         * Whether the items_wrap contains schema microdata or not.
         *
         * @since 4.2.0
         * @var boolean
         */
        private $has_schema = false;

        /**
         * Ensure the items_wrap argument contains microdata.
         *
         * @since 4.2.0
         */
        public function __construct() {
            if (!has_filter('wp_nav_menu_args', array($this, 'add_schema_to_navbar_ul'))) {
                add_filter('wp_nav_menu_args', array($this, 'add_schema_to_navbar_ul'));
            }
        }

        /**
         * Starts the list before the elements are added.
         *
         * @since WP 3.0.0
         *
         * @see Walker_Nav_Menu::start_lvl()
         *
         * @param string           $output Used to append additional content (passed by reference).
         * @param int              $depth  Depth of menu item. Used for padding.
         * @param WP_Nav_Menu_Args $args   An object of wp_nav_menu() arguments.
         */
        public function start_lvl(&$output, $depth = 0, $args = null) {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = str_repeat($t, $depth);


            // Default class to add to the file.
            if ($depth == 0) {
                $classes = array('dropdown-menu');
            } else {
                $classes = array('dropdown-menu dropdown-sub-menu');
            }
            /**
             * Filters the CSS class(es) applied to a menu list element.
             *
             * @since WP 4.8.0
             *
             * @param array    $classes The CSS classes that are applied to the menu `<ul>` element.
             * @param stdClass $args    An object of `wp_nav_menu()` arguments.
             * @param int      $depth   Depth of menu item. Used for padding.
             */
            $class_names = join(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            /*
             * The `.dropdown-menu` container needs to have a labelledby
             * attribute which points to it's trigger link.
             *
             * Form a string for the labelledby attribute from the the latest
             * link with an id that was added to the $output.
             */
            $labelledby = '';
            // Find all links with an id in the output.
            preg_match_all('/(<a.*?id=\"|\')(.*?)\"|\'.*?>/im', $output, $matches);
            // With pointer at end of array check if we got an ID match.
            if (end($matches[2])) {
                // Build a string to use as aria-labelledby.
                $labelledby = 'aria-labelledby="' . esc_attr(end($matches[2])) . '"';
            }
            $output .= "{$n}{$indent}<ul$class_names $labelledby>{$n}";
        }

        /**
         * Starts the element output.
         *
         * @since WP 3.0.0
         * @since WP 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
         *
         * @see Walker_Nav_Menu::start_el()
         *
         * @param string           $output Used to append additional content (passed by reference).
         * @param WP_Nav_Menu_Item $item   Menu item data object.
         * @param int              $depth  Depth of menu item. Used for padding.
         * @param WP_Nav_Menu_Args $args   An object of wp_nav_menu() arguments.
         * @param int              $id     Current item ID.
         */
        public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = ( $depth ) ? str_repeat($t, $depth) : '';

            if (false !== strpos($args->items_wrap, 'itemscope') && false === $this->has_schema) {
                $this->has_schema = true;
                $args->link_before = '<span itemprop="name">' . $args->link_before;
                $args->link_after .= '</span>';
            }

            $classes = empty($item->classes) ? array() : (array) $item->classes;

            // Updating the CSS classes of a menu item in the WordPress Customizer preview results in all classes defined
            // in that particular input box to come in as one big class string.
            $split_on_spaces = function ( $class ) {
                return preg_split('/\s+/', $class);
            };
            $classes = $this->flatten(array_map($split_on_spaces, $classes));

            /*
             * Initialize some holder variables to store specially handled item
             * wrappers and icons.
             */
            $linkmod_classes = array();
            $icon_classes = array();

            /*
             * Get an updated $classes array without linkmod or icon classes.
             *
             * NOTE: linkmod and icon class arrays are passed by reference and
             * are maybe modified before being used later in this function.
             */
            $classes = self::separate_linkmods_and_icons_from_classes($classes, $linkmod_classes, $icon_classes, $depth);

            // Join any icon classes plucked from $classes into a string.
            $icon_class_string = join(' ', $icon_classes);

            /**
             * Filters the arguments for a single nav menu item.
             *
             * @since WP 4.4.0
             *
             * @param WP_Nav_Menu_Args $args  An object of wp_nav_menu() arguments.
             * @param WP_Nav_Menu_Item $item  Menu item data object.
             * @param int              $depth Depth of menu item. Used for padding.
             *
             * @var WP_Nav_Menu_Args
             */
            $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

            // Add .dropdown or .active classes where they are needed.
            if ($this->has_children) {
                $classes[] = 'dropdown';
            }
            if (in_array('current-menu-item', $classes, true) || in_array('current-menu-parent', $classes, true)) {
                $classes[] = 'active';
            }

            // Add some additional default classes to the item.
            $classes[] = 'menu-item-' . $item->ID;
            $classes[] = 'nav-item';

            // Allow filtering the classes.
            $classes = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth);

            // Form a string of classes in format: class="class_names".
            $class_names = join(' ', $classes);
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            /**
             * Filters the ID applied to a menu item's list item element.
             *
             * @since WP 3.0.1
             * @since WP 4.1.0 The `$depth` parameter was added.
             *
             * @param string           $menu_id The ID that is applied to the menu item's `<li>` element.
             * @param WP_Nav_Menu_Item $item    The current menu item.
             * @param WP_Nav_Menu_Args $args    An object of wp_nav_menu() arguments.
             * @param int              $depth   Depth of menu item. Used for padding.
             */
            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
            $id = $id ? ' id="' . esc_attr($id) . '"' : '';

            $output .= $indent . '<li ' . $id . $class_names . '>';

            // Initialize array for holding the $atts for the link item.
            $atts = array();
            $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
            $atts['target'] = !empty($item->target) ? $item->target : '';
            if ('_blank' === $item->target && empty($item->xfn)) {
                $atts['rel'] = 'noopener noreferrer';
            } else {
                $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
            }

            // If the item has_children add atts to <a>.
            if ($this->has_children && 0 === $depth) {
//                $atts['href'] = '#';
                $atts['href'] = !empty($item->url) ? $item->url : '';
//                $atts['data-hover'] = 'dropdown';
                $atts['data-hover'] = 'dropdown';
                $atts['aria-haspopup'] = 'true';
                $atts['aria-expanded'] = 'false';
                $atts['class'] = 'dropdown-toggle nav-link';
                $atts['id'] = 'menu-item-dropdown-' . $item->ID;
            } else {
                if (true === $this->has_schema) {
                    $atts['itemprop'] = 'url';
                }

                $atts['href'] = !empty($item->url) ? $item->url : '#';
                // For items in dropdowns use .dropdown-item instead of .nav-link.
                if ($depth > 0) {
                    $atts['class'] = 'dropdown-item';
                } else {
                    $atts['class'] = 'nav-link';
                }
            }

            $atts['aria-current'] = $item->current ? 'page' : '';

            // Update atts of this item based on any custom linkmod classes.
            $atts = self::update_atts_for_linkmod_type($atts, $linkmod_classes);

            // Allow filtering of the $atts array before using it.
            $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

            // Build a string of html containing all the atts for the item.
            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    $value = ( 'href' === $attr ) ? esc_url($value) : esc_attr($value);
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            // Set a typeflag to easily test if this is a linkmod or not.
            $linkmod_type = self::get_linkmod_type($linkmod_classes);

            // START appending the internal item contents to the output.
            $item_output = isset($args->before) ? $args->before : '';

            /*
             * This is the start of the internal nav item. Depending on what
             * kind of linkmod we have we may need different wrapper elements.
             */
            if ('' !== $linkmod_type) {
                // Is linkmod, output the required element opener.
                $item_output .= self::linkmod_element_open($linkmod_type, $attributes);
            } else {
                // With no link mod type set this must be a standard <a> tag.
                $item_output .= '<a' . $attributes . '>';
            }

            /*
             * Initiate empty icon var, then if we have a string containing any
             * icon classes form the icon markup with an <i> element. This is
             * output inside of the item before the $title (the link text).
             */
            $icon_html = '';
            if (!empty($icon_class_string)) {
                // Append an <i> with the icon classes to what is output before links.
                $icon_html = '<i class="' . esc_attr($icon_class_string) . '" aria-hidden="true"></i> ';
            }

            /** This filter is documented in wp-includes/post-template.php */
            $title = apply_filters('the_title', $item->title, $item->ID);

            /**
             * Filters a menu item's title.
             *
             * @since WP 4.4.0
             *
             * @param string           $title The menu item's title.
             * @param WP_Nav_Menu_Item $item  The current menu item.
             * @param WP_Nav_Menu_Args $args  An object of wp_nav_menu() arguments.
             * @param int              $depth Depth of menu item. Used for padding.
             */
            $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

            // If the .sr-only class was set apply to the nav items text only.
            if (in_array('sr-only', $linkmod_classes, true)) {
                $title = self::wrap_for_screen_reader($title);
                $keys_to_unset = array_keys($linkmod_classes, 'sr-only', true);
                foreach ($keys_to_unset as $k) {
                    unset($linkmod_classes[$k]);
                }
            }

            // Put the item contents into $output.
            $item_output .= isset($args->link_before) ? $args->link_before . $icon_html . $title . $args->link_after : '';

            /*
             * This is the end of the internal nav item. We need to close the
             * correct element depending on the type of link or link mod.
             */
            if ('' !== $linkmod_type) {
                // Is linkmod, output the required closing element.
                $item_output .= self::linkmod_element_close($linkmod_type);
            } else {
                // With no link mod type set this must be a standard <a> tag.
                $item_output .= '</a>';
            }

            $item_output .= isset($args->after) ? $args->after : '';

            // END appending the internal item contents to the output.
            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }

        /**
         * Menu fallback.
         *
         * If this function is assigned to the wp_nav_menu's fallback_cb variable
         * and a menu has not been assigned to the theme location in the WordPress
         * menu manager the function will display nothing to a non-logged in user,
         * and will add a link to the WordPress menu manager if logged in as an admin.
         *
         * @param array $args passed from the wp_nav_menu function.
         * @return string|void String when echo is false.
         */
        public static function fallback($args) {
            if (!current_user_can('edit_theme_options')) {
                return;
            }

            // Initialize var to store fallback html.
            $fallback_output = '';

            // Menu container opening tag.
            $show_container = false;
            if ($args['container']) {
                /**
                 * Filters the list of HTML tags that are valid for use as menu containers.
                 *
                 * @since WP 3.0.0
                 *
                 * @param array $tags The acceptable HTML tags for use as menu containers.
                 *                    Default is array containing 'div' and 'nav'.
                 */
                $allowed_tags = apply_filters('wp_nav_menu_container_allowedtags', array('div', 'nav'));
                if (is_string($args['container']) && in_array($args['container'], $allowed_tags, true)) {
                    $show_container = true;
                    $class = $args['container_class'] ? ' class="menu-fallback-container ' . esc_attr($args['container_class']) . '"' : ' class="menu-fallback-container"';
                    $id = $args['container_id'] ? ' id="' . esc_attr($args['container_id']) . '"' : '';
                    $fallback_output .= '<' . $args['container'] . $id . $class . '>';
                }
            }

            // The fallback menu.
            $class = $args['menu_class'] ? ' class="menu-fallback-menu ' . esc_attr($args['menu_class']) . '"' : ' class="menu-fallback-menu"';
            $id = $args['menu_id'] ? ' id="' . esc_attr($args['menu_id']) . '"' : '';
            $fallback_output .= '<ul' . $id . $class . '>';
            $fallback_output .= '<li class="nav-item"><a href="' . esc_url(admin_url('nav-menus.php')) . '" class="nav-link" title="' . esc_attr__('Add a menu', 'wp-bootstrap-navwalker') . '">' . esc_html__('Add a menu', 'wp-bootstrap-navwalker') . '</a></li>';
            $fallback_output .= '</ul>';

            // Menu container closing tag.
            if ($show_container) {
                $fallback_output .= '</' . $args['container'] . '>';
            }

            // if $args has 'echo' key and it's true echo, otherwise return.
            if (array_key_exists('echo', $args) && $args['echo']) {
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $fallback_output;
            } else {
                return $fallback_output;
            }
        }

        /**
         * Filter to ensure the items_Wrap argument contains microdata.
         *
         * @since 4.2.0
         *
         * @param  array $args The nav instance arguments.
         * @return array $args The altered nav instance arguments.
         */
        public function add_schema_to_navbar_ul($args) {
            $wrap = $args['items_wrap'];
            if (strpos($wrap, 'SiteNavigationElement') === false) {
                $args['items_wrap'] = preg_replace('/(>).*>?\%3\$s/', ' itemscope itemtype="http://www.schema.org/SiteNavigationElement"$0', $wrap);
            }

            return $args;
        }

        /**
         * Find any custom linkmod or icon classes and store in their holder
         * arrays then remove them from the main classes array.
         *
         * Supported linkmods: .disabled, .dropdown-header, .dropdown-divider, .sr-only
         * Supported iconsets: Font Awesome 4/5, Glypicons
         *
         * NOTE: This accepts the linkmod and icon arrays by reference.
         *
         * @since 4.0.0
         *
         * @param array   $classes         an array of classes currently assigned to the item.
         * @param array   $linkmod_classes an array to hold linkmod classes.
         * @param array   $icon_classes    an array to hold icon classes.
         * @param integer $depth           an integer holding current depth level.
         *
         * @return array  $classes         a maybe modified array of classnames.
         */
        private function separate_linkmods_and_icons_from_classes($classes, &$linkmod_classes, &$icon_classes, $depth) {
            // Loop through $classes array to find linkmod or icon classes.
            foreach ($classes as $key => $class) {
                /*
                 * If any special classes are found, store the class in it's
                 * holder array and and unset the item from $classes.
                 */
                if (preg_match('/^disabled|^sr-only/i', $class)) {
                    // Test for .disabled or .sr-only classes.
                    $linkmod_classes[] = $class;
                    unset($classes[$key]);
                } elseif (preg_match('/^dropdown-header|^dropdown-divider|^dropdown-item-text/i', $class) && $depth > 0) {
                    /*
                     * Test for .dropdown-header or .dropdown-divider and a
                     * depth greater than 0 - IE inside a dropdown.
                     */
                    $linkmod_classes[] = $class;
                    unset($classes[$key]);
                } elseif (preg_match('/^fa-(\S*)?|^fa(s|r|l|b)?(\s?)?$/i', $class)) {
                    // Font Awesome.
                    $icon_classes[] = $class;
                    unset($classes[$key]);
                } elseif (preg_match('/^glyphicon-(\S*)?|^glyphicon(\s?)$/i', $class)) {
                    // Glyphicons.
                    $icon_classes[] = $class;
                    unset($classes[$key]);
                }
            }

            return $classes;
        }

        /**
         * Return a string containing a linkmod type and update $atts array
         * accordingly depending on the decided.
         *
         * @since 4.0.0
         *
         * @param array $linkmod_classes array of any link modifier classes.
         *
         * @return string                empty for default, a linkmod type string otherwise.
         */
        private function get_linkmod_type($linkmod_classes = array()) {
            $linkmod_type = '';
            // Loop through array of linkmod classes to handle their $atts.
            if (!empty($linkmod_classes)) {
                foreach ($linkmod_classes as $link_class) {
                    if (!empty($link_class)) {

                        // Check for special class types and set a flag for them.
                        if ('dropdown-header' === $link_class) {
                            $linkmod_type = 'dropdown-header';
                        } elseif ('dropdown-divider' === $link_class) {
                            $linkmod_type = 'dropdown-divider';
                        } elseif ('dropdown-item-text' === $link_class) {
                            $linkmod_type = 'dropdown-item-text';
                        }
                    }
                }
            }
            return $linkmod_type;
        }

        /**
         * Update the attributes of a nav item depending on the limkmod classes.
         *
         * @since 4.0.0
         *
         * @param array $atts            array of atts for the current link in nav item.
         * @param array $linkmod_classes an array of classes that modify link or nav item behaviors or displays.
         *
         * @return array                 maybe updated array of attributes for item.
         */
        private function update_atts_for_linkmod_type($atts = array(), $linkmod_classes = array()) {
            if (!empty($linkmod_classes)) {
                foreach ($linkmod_classes as $link_class) {
                    if (!empty($link_class)) {
                        /*
                         * Update $atts with a space and the extra classname
                         * so long as it's not a sr-only class.
                         */
                        if ('sr-only' !== $link_class) {
                            $atts['class'] .= ' ' . esc_attr($link_class);
                        }
                        // Check for special class types we need additional handling for.
                        if ('disabled' === $link_class) {
                            // Convert link to '#' and unset open targets.
                            $atts['href'] = '#';
                            unset($atts['target']);
                        } elseif ('dropdown-header' === $link_class || 'dropdown-divider' === $link_class || 'dropdown-item-text' === $link_class) {
                            // Store a type flag and unset href and target.
                            unset($atts['href']);
                            unset($atts['target']);
                        }
                    }
                }
            }
            return $atts;
        }

        /**
         * Wraps the passed text in a screen reader only class.
         *
         * @since 4.0.0
         *
         * @param string $text the string of text to be wrapped in a screen reader class.
         * @return string      the string wrapped in a span with the class.
         */
        private function wrap_for_screen_reader($text = '') {
            if ($text) {
                $text = '<span class="sr-only">' . $text . '</span>';
            }
            return $text;
        }

        /**
         * Returns the correct opening element and attributes for a linkmod.
         *
         * @since 4.0.0
         *
         * @param string $linkmod_type a sting containing a linkmod type flag.
         * @param string $attributes   a string of attributes to add to the element.
         *
         * @return string              a string with the openign tag for the element with attribibutes added.
         */
        private function linkmod_element_open($linkmod_type, $attributes = '') {
            $output = '';
            if ('dropdown-item-text' === $linkmod_type) {
                $output .= '<span class="dropdown-item-text"' . $attributes . '>';
            } elseif ('dropdown-header' === $linkmod_type) {
                /*
                 * For a header use a span with the .h6 class instead of a real
                 * header tag so that it doesn't confuse screen readers.
                 */
                $output .= '<span class="dropdown-header h6"' . $attributes . '>';
            } elseif ('dropdown-divider' === $linkmod_type) {
                // This is a divider.
                $output .= '<div class="dropdown-divider"' . $attributes . '>';
            }
            return $output;
        }

        /**
         * Return the correct closing tag for the linkmod element.
         *
         * @since 4.0.0
         *
         * @param string $linkmod_type a string containing a special linkmod type.
         *
         * @return string              a string with the closing tag for this linkmod type.
         */
        private function linkmod_element_close($linkmod_type) {
            $output = '';
            if ('dropdown-header' === $linkmod_type || 'dropdown-item-text' === $linkmod_type) {
                /*
                 * For a header use a span with the .h6 class instead of a real
                 * header tag so that it doesn't confuse screen readers.
                 */
                $output .= '</span>';
            } elseif ('dropdown-divider' === $linkmod_type) {
                // This is a divider.
                $output .= '</div>';
            }
            return $output;
        }

        /**
         * Flattens a multidimensional array to a simple array.
         *
         * @param array $array a multidimensional array.
         *
         * @return array a simple array
         */
        public function flatten($array) {
            $result = array();
            foreach ($array as $element) {
                if (is_array($element)) {
                    array_push($result, ...$this->flatten($element));
                } else {
                    $result[] = $element;
                }
            }
            return $result;
        }

    }

    endif;

//ADD USer Menu Widget to Main Menu
function add_user_nav_item($items, $args) {
    global $woocommerce;
//    $items = '';
//    pre($args->theme_location);
//    die();
    if ($args->theme_location === 'primary'):
        if (is_user_logged_in()) {
//            $items .= '<li id="myaccount-menu" class="nav-item user-menu"><a class="nav-link" href="' . site_url() . '/my-account/    ">My Account</a></li>';
//            $items .= '<li id="logout-menu" class="nav-item user-menu"><a class="nav-link" href="' . wp_logout_url(home_url()) . '">Logout</a></li>';
        } else {
//            $items .= '<li id="login-menu" class="nav-item user-menu"><a data-toggle="modal" data-target="#Login"  class="nav-link" href="/login">Login</a></li>';
//            $items .= '<li id="signup-menu" class="nav-item user-menu"><a data-toggle="modal" data-target="#Registeration" class="nav-link" href="/registration/?level=26">Sign up</a></li>';
        }
//        if ($woocommerce) {
//            $shopping_icon_url = get_template_directory_uri() . '/inc/assets/images/shopping-icon.png';
//            $items .= '<li id="shopping-cart" class="nav-item user-menu"><a class="nav-link" id="mobile-cart-link" href="' . $woocommerce->cart->get_cart_url() . '"><img width="20" height="20" src="' . $shopping_icon_url . '" alt="Shopping Icon"/>';
//
//            if ($woocommerce->cart->cart_contents_count > 0) {
//                $items .= '<span class="cart-total-items">' . $woocommerce->cart->cart_contents_count . '</span>';
//            }
//            $items .= '</a></li>';
//        }
        $search_icon_url = get_template_directory_uri() . '/inc/assets/images/search-icon.png';
//        $items .= '<li id="search-item" class="nav-item user-menu"><img width="20" height="20" src="' . $search_icon_url . '" alt="Search Icon"/></li><div class="search_form_div"><aside id="searchformWrap" class="d-none"><form action="'.get_site_url().'" id="searchform" method="get"><input type="text" name="s" id="s" placeholder="Start Typing..."><em class="d-block">Press enter to begin your search</em></form></aside></div>';
    endif;
    return $items;
}

add_filter('wp_nav_menu_items', 'add_user_nav_item', 10, 2);
// Archieve Template Adjustment
add_filter('get_the_archive_title', function ($title) {
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif (is_tax()) { //for custom post types
        $title = sprintf(__('%1$s'), single_term_title('', false));
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    }
    return $title;
});
add_filter('widget_text', 'do_shortcode');
/*
 * 
 * Pagination
 * themes_pagination($your_query->max_num_pages)
 */

function themes_pagination($pages = '', $range = 2) {
    $html = '';
    $html .= '<div class="col-md-6 mx-auto text-center my-5">';
    $showitems = ($range * 2) + 1;
    global $paged;
    if (empty($paged))
        $paged = 1;

    if ($pages == '') {
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if (!$pages) {
            $pages = 1;
        }
    }

    if (1 != $pages) {
        $html .= '<ul class="pagination justify-content-center mb-0">';
        $html .= '<span class="d-none">Page ' . $paged .' of ' . $pages . '</span>';
        if ($paged > 2 && $paged > $range + 1 && $showitems < $pages){
               $html .= '<li class="page-item first-item d-none"><a class="page-link" href="' . get_pagenum_link(1) . '">&laquo; First</a></li>';
        }
            
        if ($paged > 1 && $showitems < $pages){
             $html .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link($paged - 1) . '">&laquo;</a></li>';
        }
            

        for ($i = 1; $i <= $pages; $i++) {
            if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                $html .= ($paged == $i) ? '<li class="page-item active"><span class="page-link">' . $i .'</span></li>' :'<li class="page-item"><a class="page-link" href="' . get_pagenum_link($i) . '">'.$i.'</a></li>';
            }
        }

        if ($paged < $pages && $showitems < $pages)
            $html .='<li class="page-item"><a class="page-link" href="' . get_pagenum_link($paged + 1) . '">&raquo;</a></li>';
        if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages)
            $html .='<li class="page-item last-item d-none"><a class="page-link" href="' . get_pagenum_link($pages) . '">Last &raquo;</a></li>';
       $html .= '</ul>';
    }
    ?>

    <?php

    $html .= '</div>';
    return $html;
}
/*
 *  Custom breadcrumbs
 */

function custom_breadcrumbs(array $options = array()) {
    // default values assigned to options
    $options = array_merge(array(
        'crumbId' => 'nav_crumb', // id for the breadcrumb Div
        'crumbClass' => 'nav_crumb', // class for the breadcrumb Div
        'showOnHome' => 1, // 1 - show breadcrumbs on the homepage, 0 - don't show
        'delimiter' => ' ', // delimiter between crumbs
        'homePageText' => 'Home', // text for the 'Home' link
        'showCurrent' => 1, // 1 - show current post/page title in breadcrumbs, 0 - don't show
        'beforeTag' => '<span class="current">', // tag before the current breadcrumb
        'afterTag' => '</span>', // tag after the current crumb
        'showTitle' => 1 // showing post/page title or slug if title to show then 1
            ), $options);

    $crumbId = $options['crumbId'];
    $crumbClass = $options['crumbClass'];
    $showOnHome = $options['showOnHome'];
    $delimiter = $options['delimiter'];
    $homePageText = $options['homePageText'];
    $showCurrent = $options['showCurrent'];
    $beforeTag = $options['beforeTag'];
    $afterTag = $options['afterTag'];
    $showTitle = $options['showTitle'];
    global $post;
    $wp_query = $GLOBALS['wp_query'];
    $homeLink = get_bloginfo('url');
    echo '<div id="' . $crumbId . '" class="' . $crumbClass . '" >';
    if (is_home() || is_front_page()) {
        if ($showOnHome == 1){
             echo $beforeTag . $homePageText . $afterTag;
        }
    } else {
        echo '<a href="' . $homeLink . '">' . $homePageText . '</a> ' . $delimiter . ' ';
        echo '<span class="current-menu-text1"></span>';
        if (is_category()) {
            $thisCat = get_category(get_query_var('cat'), false);

            if ($thisCat->parent != 0)
                echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
            echo $beforeTag . '' . single_cat_title('', false) . '' . $afterTag;
        } elseif (is_tax()) {
            $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            $parents = array();
            $parent = $term->parent;
            while ($parent) {
                $parents[] = $parent;
                $new_parent = get_term_by('id', $parent, get_query_var('taxonomy'));
                $parent = $new_parent->parent;
            }
            if (!empty($parents)) {
                $parents = array_reverse($parents);
                foreach ($parents as $parent) {
                    $item = get_term_by('id', $parent, get_query_var('taxonomy'));
                    echo '<a href="' . get_term_link($item->slug, get_query_var('taxonomy')) . '">' . $item->name . '</a>' . $delimiter;
                }
            }
            $queried_object = $wp_query->get_queried_object();
            echo $beforeTag . $queried_object->name . $afterTag;
        } elseif (is_search()) {
            echo $beforeTag . 'Search results for "' . get_search_query() . '"' . $afterTag;
        } elseif (is_day()) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $beforeTag . get_the_time('d') . $afterTag;
        } elseif (is_month()) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $beforeTag . get_the_time('F') . $afterTag;
        } elseif (is_year()) {
            echo $beforeTag . get_the_time('Y') . $afterTag;
        } elseif (is_single() && !is_attachment()) {
            if ($showTitle)
                $title = get_the_title();
            else
                $title = $post->post_name;
            if (get_post_type() == 'product') { // it is for custom post type with custome taxonomies like
                //Breadcrumb would be : Home Furnishings > Bed Covers > Cotton Quilt King Kantha Bedspread
                // product = Cotton Quilt King Kantha Bedspread, custom taxonomy product_cat (Home Furnishings -> Bed Covers)
                // show ?product with category on single page
                if ($terms = wp_get_object_terms($post->ID, 'product_cat')) {
                    $term = current($terms);
                    $parents = array();
                    $parent = $term->parent;
                    while ($parent) {
                        $parents[] = $parent;
                        $new_parent = get_term_by('id', $parent, 'product_cat');
                        $parent = $new_parent->parent;
                    }
                    if (!empty($parents)) {
                        $parents = array_reverse($parents);
                        foreach ($parents as $parent) {
                            $item = get_term_by('id', $parent, 'product_cat');
                            echo '<a href="' . get_term_link($item->slug, 'product_cat') . '">' . $item->name . '</a>' . $delimiter;
                        }
                    }
                    echo '<a href="' . get_term_link($term->slug, 'product_cat') . '">' . $term->name . '</a>' . $delimiter;
                }
                echo $beforeTag . $title . $afterTag;
            }
            /* Custom Breadcrumbs for Lessons */ elseif (get_post_type() == 'sfwd-lessons') {
                echo '<a href="' . $homeLink . '/courses">Courses</a>';
                $parent_course = learndash_get_setting($post);
                $parentid = $parent_course['course'];
                $parent_title = get_the_title($parentid);
                $parent_url = get_permalink($parentid);
                echo ' ' . $delimiter . ' <a href="' . $parent_url . '">' . $parent_title . '</a>';
                echo ' ' . $delimiter . '<a href="' . $homeLink . '/lessons">Lessons</a>';
                echo ' ' . $delimiter . ' ' . get_the_title();
            }
            /* Custom Breadcrumbs for Topics */ elseif (get_post_type() == 'sfwd-topic') {
                echo '<a href="' . $homeLink . '/courses">Courses</a>';
                $parent_course = learndash_get_setting($post);
                $parentid = $parent_course['course'];
                $parent_title = get_the_title($parentid);
                $parent_url = get_permalink($parentid);
                echo ' ' . $delimiter . ' <a href="' . $parent_url . '">' . $parent_title . '</a>';
                echo ' ' . $delimiter . '<a href="' . $homeLink . '/lessons">Lessons</a>';
                $parentid = $parent_course['lesson'];
                $parent_title = get_the_title($parentid);
                $parent_url = get_permalink($parentid);
                echo ' ' . $delimiter . ' <a href="' . $parent_url . '">' . $parent_title . '</a>';
                //echo ' '.$delimiter.'<a href="'.$homeLink . '/topics">Topics</a>';
                echo ' ' . $delimiter . 'Topics';
                echo ' ' . $delimiter . ' ' . get_the_title();
            }
            /* Hide Breadcrumbs for Quizzes */ elseif (get_post_type() == 'sfwd-quiz') {
                echo '<style>#nav_crumb{display:none;}</style>';
            } elseif (get_post_type() != 'post') {
                // echo get_post_type();exit;
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                // echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';

                /* Custom Breadcrumb to show plural names for custom post types */
                echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->name . '</a>';
                if ($showCurrent == 1)
                    echo ' ' . $delimiter . ' ' . $beforeTag . $title . $afterTag;
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                if ($showCurrent == 0)
                    $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                echo $cats;
                if ($showCurrent == 1)
                    echo $beforeTag . $title . $afterTag;
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            echo $beforeTag . $post_type->labels->singular_name . $afterTag;
        } elseif (is_attachment()) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            echo @get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
            echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
            if ($showCurrent == 1)
                echo '' . $delimiter . '' . $beforeTag . get_the_title() . $afterTag;
        } elseif (is_page() && !$post->post_parent) {
            $title = ($showTitle) ? get_the_title() : $post->post_name;
            if ($showCurrent == 1)
                echo $beforeTag . $title . $afterTag;
        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                echo $breadcrumbs[$i];
                if ($i != count($breadcrumbs) - 1)
                    echo ' ' . $delimiter . ' ';
            }
            $title = ($showTitle) ? get_the_title() : $post->post_name;

            if ($showCurrent == 1)
                echo ' ' . $delimiter . ' ' . $beforeTag . $title . $afterTag;
        } elseif (is_tag()) {
            echo $beforeTag . 'Posts tagged "' . single_tag_title('', false) . '"' . $afterTag;
        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo $beforeTag . 'Articles posted by ' . $userdata->display_name . $afterTag;
        } elseif (is_404()) {
            echo $beforeTag . 'Error 404' . $afterTag;
        }
        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_tax())
                echo ' (';
            echo __('Page') . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_tax())
                echo ')';
        }
    }
    echo '</div>';
}
/**
 * EMBED IFRAME FOR PDF
 * [pdfViewer title="" url="" width="" height=""]
 */
function embed_tag($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'title' => 'PDF title',
        'width' => '450',
        'height' => '575',
            ), $atts);
    $html = '';
//    if(wp_is_mobile()){
//        $atts['width'] = '475';
//        $atts['height'] = '610';
//    }
//    echo str_replace('/preview','/edit',$atts['url']);
    $PDFbutton = '<div class="w-100 d-block my-5"><a class="btn btn-lg btn-navyblue" href="' . str_replace('/preview','/edit',$atts['url']). '" target="_blank">Click here to Print</a></div>';
//    $html .= '<div class="col-12 text-center"><iframe src="' . $atts['url'] . '" width="475px" height="600px" ></iframe>' . $PDFbutton . '</div>';
    $html .= '<div class="col-12 text-center"><iframe src="' . $atts['url'] . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '" title="' . $atts['title'] . '"></iframe>' . $PDFbutton . '</div>';
//    $html .= '<div class="col-12 text-center"><a class="" href="' . $atts['url'] . '" target="_blank"><image src="' . $atts['image'] . '" class="img-fluid" /></a>' . $PDFbutton . '</div>';
    return $html;
}

add_shortcode('pdfViewer', 'embed_tag');
/**
 * current_user_name
 */
function current_user_name() {
    $user =get_user_meta(get_current_user_id());
        if(!empty($user['first_name'][0])){
           $customerName =  $user['first_name'][0].' '. $user['last_name'][0];
        } else {
             $customerName ='';
        }
    return $customerName;
}

add_shortcode('current_user_name', 'current_user_name');
/**
 * GET SOCIAL PAGE CONTENT
 */
function get_social() {
    $page_id = 19349;
    $page_data = get_page($page_id);
    $url = get_the_post_thumbnail_url($page_id, 'full');
    $feature_image = get_the_post_thumbnail($page_id, 'full');
    $html = '';
    $html .='<section id="get-social" class="container-fluid"><div class="row align-items-center">';
    $html .='<div class="col-lg-5 get-social-text">'.do_shortcode($page_data->post_content).'</div>';
    $html .='<div class="col-lg-7 get-social-image p-0">'.$feature_image.'</div>';
    $html .='</div></section>';
    echo $html;
}

add_shortcode('get_social', 'get_social');