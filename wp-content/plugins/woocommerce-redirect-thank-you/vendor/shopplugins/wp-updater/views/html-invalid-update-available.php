<div class="update-message notice inline notice-error notice-alt" style="margin: 10px 0 5px;">
	<p>
		<?php
		// translators: Plugin Name.
		echo esc_html( sprintf( __( 'There is a new version of %s available.' ), $plugin->get_name() ) ); ?>&nbsp;
		<strong><?php esc_html_e( 'Please enter a valid license to receive this update.' ); ?></strong>
	</p>
</div>
