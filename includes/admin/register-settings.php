<?php
/**
 * Admin: register all plugin settings with the WP Settings API.
 *
 * Settings group: `crawlbridge_settings`. All toggles default to false
 * (opt-in). The IndexNow API key is sanitized via sanitize_indexnow_key().
 *
 * @package Crawlbridge
 */

namespace Crawlbridge;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_init', __NAMESPACE__ . '\\register_settings' );

/**
 * Register all settings with the Settings API.
 *
 * @return void
 */
function register_settings(): void {
	$boolean_options = array(
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
	);

	foreach ( $boolean_options as $option ) {
		register_setting(
			'crawlbridge_settings',
			$option,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => false,
			)
		);
	}

	register_setting(
		'crawlbridge_settings',
		'crawlbridge_indexnow_key',
		array(
			'type'              => 'string',
			'sanitize_callback' => __NAMESPACE__ . '\\sanitize_indexnow_key',
			'default'           => '',
		)
	);
}
