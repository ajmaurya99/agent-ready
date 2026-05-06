<?php
/**
 * Crawlbridge uninstall handler.
 *
 * Runs only when the plugin is uninstalled (deleted from the Plugins screen),
 * not on deactivation. Removes every option the plugin created so the database
 * is left clean.
 *
 * @package Crawlbridge
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$options = array(
	'crawlbridge_markdown_enabled',
	'crawlbridge_content_signals_enabled',
	'crawlbridge_api_catalog_enabled',
	'crawlbridge_mcp_server_card_enabled',
	'crawlbridge_agent_skills_index_enabled',
	'crawlbridge_webmcp_enabled',
	'crawlbridge_json_ld_enabled',
	'crawlbridge_openapi_enabled',
	'crawlbridge_indexnow_enabled',
	'crawlbridge_llms_txt_enabled',
	'crawlbridge_indexnow_key',
);

$transients = array(
	'crawlbridge_reset_notice',
	'crawlbridge_openapi_cache',
	'crawlbridge_llms_txt_cache',
	'crawlbridge_show_wizard',
	'crawlbridge_wizard_applied',
);

if ( is_multisite() ) {
	$sites = get_sites( array( 'fields' => 'ids' ) );
	foreach ( $sites as $site_id ) {
		switch_to_blog( $site_id );
		foreach ( $options as $option ) {
			delete_option( $option );
		}
		foreach ( $transients as $transient ) {
			delete_transient( $transient );
		}
		restore_current_blog();
	}
} else {
	foreach ( $options as $option ) {
		delete_option( $option );
	}
	foreach ( $transients as $transient ) {
		delete_transient( $transient );
	}
}
