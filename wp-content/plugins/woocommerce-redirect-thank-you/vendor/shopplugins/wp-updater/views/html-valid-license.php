<tr class="wp-updater-license-row <?php echo esc_attr( sanitize_html_class( $plugin->get_license_status() ) ); ?>">
	<td colspan="5">
		<?php
		// translators: Plugin Name.
		echo esc_html( sprintf( __( 'Your license for %s is active.' ), $plugin->get_name() ) );
		?>
		<a
			href="javascript:void(0);"
			class="deactivate"
			data-plugin="<?php echo esc_attr( $plugin->plugin_basename ); ?>"
		><?php esc_html_e( 'Deactivate license' ); ?></a>
		<span class="waiting spinner" style="float: none; vertical-align: top;"></span>
	</td>
</tr>
