<?php
/**
 * Admin: register the settings page under Settings → Crawlbridge.
 *
 * @package Crawlbridge
 */

namespace Crawlbridge;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', __NAMESPACE__ . '\\add_admin_menu' );

/**
 * Add the Crawlbridge submenu under Settings.
 *
 * @return void
 */
function add_admin_menu(): void {
	add_options_page(
		__( 'Crawlbridge', 'crawlbridge' ),
		__( 'Crawlbridge', 'crawlbridge' ),
		required_capability(),
		'crawlbridge',
		__NAMESPACE__ . '\\render_settings_page'
	);
}
