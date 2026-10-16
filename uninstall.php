<?php
/**
 * Removes tables, options and cron events when the plugin is deleted.
 *
 * @package WPIntegrations\ElementorRdStation
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Both editions share this data. Bail on an installed Pro copy, not just an
 * active one: a bulk delete can remove this plugin while Pro is momentarily
 * deactivated, taking Pro's tokens and integrations with it.
 */
$wpi_erds_active = (array) get_option( 'active_plugins', array() );
$wpi_erds_shared = false;
foreach ( array(
	'wpintegrations-connector-for-elementor-rd-station-pro/wpintegrations-connector-for-elementor-rd-station-pro.php',
	'connect-elementor-forms-to-rd-station-pro/connect-elementor-forms-to-rd-station-pro.php',
) as $wpi_erds_pro ) {
	$wpi_erds_shared = $wpi_erds_shared
		|| in_array( $wpi_erds_pro, $wpi_erds_active, true )
		|| ( is_multisite() && array_key_exists( $wpi_erds_pro, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
		|| file_exists( WP_PLUGIN_DIR . '/' . $wpi_erds_pro );
}

if ( $wpi_erds_shared ) {
	return;
}

global $wpdb;

// Plugin tables. Names are constants, not user input.
foreach ( array( 'wpi_erds_flows', 'wpi_erds_logs', 'wpi_erds_jobs' ) as $wpi_erds_table ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.NoCaching -- uninstall cleanup of plugin-owned tables.
	$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . $wpi_erds_table ) );
}

// Options.
delete_option( 'wpi_erds_settings' );
delete_option( 'wpi_erds_version' );
delete_option( 'wpi_erds_migrations' );
delete_option( 'wpi_erds_recent_shapes' );

// Transients: the form-scan cache, plus RD Station field lists swept by prefix.
delete_transient( 'wpi_erds_elementor_forms' );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- uninstall cleanup.
$wpdb->query(
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '\_transient\_wpi\_erds\_rd\_fields\_%'
	    OR option_name LIKE '\_transient\_timeout\_wpi\_erds\_rd\_fields\_%'"
);

// Cron events.
wp_clear_scheduled_hook( 'wpi_erds_queue_tick' );
wp_clear_scheduled_hook( 'wpi_erds_daily_purge' );
