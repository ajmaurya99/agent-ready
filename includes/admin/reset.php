<?php
/**
 * Admin: handle the "Reset to Defaults" form on the settings page.
 *
 * Posts to admin-post.php with action=crawlbridge_reset, nonce-verified, then
 * deletes every plugin option, flushes caches, and redirects back with a
 * success flag.
 *
 * @package Crawlbridge
 */

namespace Crawlbridge;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_crawlbridge_reset', __NAMESPACE__ . '\\handle_reset_request' );
add_action( 'admin_notices', __NAMESPACE__ . '\\maybe_show_reset_notice' );

/**
 * Process a reset POST: clear every plugin option + cache, then redirect.
 *
 * @return void
 */
function handle_reset_request(): void {
	if ( ! current_user_can( required_capability() ) ) {
		wp_die(
			esc_html__( 'You do not have permission to reset Crawlbridge settings.', 'crawlbridge' ),
			'',
			array( 'response' => 403 )
		);
	}

	check_admin_referer( 'crawlbridge_reset' );

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
	foreach ( $options as $option ) {
		delete_option( $option );
	}

	// Flush cached endpoint outputs so the next request reflects the reset state.
	delete_transient( 'crawlbridge_openapi_cache' );
	delete_transient( 'crawlbridge_llms_txt_cache' );

	// One-shot notice flag — consumed on the next settings page render so a
	// browser refresh doesn't show the notice repeatedly.
	set_transient( 'crawlbridge_reset_notice', 1, 60 );

	$redirect = add_query_arg(
		array( 'page' => 'crawlbridge' ),
		admin_url( 'options-general.php' )
	);
	wp_safe_redirect( $redirect );
	exit;
}

/**
 * Show a success notice after a reset. Uses the `settings-error` class so the
 * existing JS notice-mover relocates it to below the Save button, matching
 * the standard "Settings saved." treatment. Consumed once via transient so a
 * page refresh doesn't keep showing it.
 *
 * @return void
 */
function maybe_show_reset_notice(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->id !== 'settings_page_crawlbridge' ) {
		return;
	}

	if ( ! get_transient( 'crawlbridge_reset_notice' ) ) {
		return;
	}

	delete_transient( 'crawlbridge_reset_notice' );
	?>
	<div class="notice notice-success settings-error is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Crawlbridge settings reset to defaults.', 'crawlbridge' ); ?></strong>
			<?php esc_html_e( 'All toggles are off and the IndexNow API key is cleared.', 'crawlbridge' ); ?>
		</p>
	</div>
	<?php
}
