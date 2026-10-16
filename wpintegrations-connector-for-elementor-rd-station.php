<?php
/**
 * Plugin Name:       WPIntegrations Form Connector for Elementor and RD Station
 * Plugin URI:        https://wpintegrations.net/plugins/connect-elementor-forms-to-rd-station/
 * Description:       Send Elementor Pro form submissions to RD Station Marketing as conversion events, with field mapping, LGPD consent and lead attribution. Not affiliated with Elementor or RD Station.
 * Version:           1.0.2
 * Requires at least: 6.2
 * Requires Plugins:  elementor
 * Requires PHP:      7.4
 * Author:            WPIntegrations
 * Author URI:        https://wpintegrations.net/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpintegrations-connector-for-elementor-rd-station
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pro shares this plugin's namespace, constants and tables, so only one may run
 * and Pro wins. Decided before any constant is defined and without relying on
 * load order; the option is the only check available this early, since
 * is_plugin_active() lives in an admin-only file.
 */
$wpi_erds_pro_alive = false;
foreach ( array(
	'wpintegrations-connector-for-elementor-rd-station-pro/wpintegrations-connector-for-elementor-rd-station-pro.php',
	// Pro's basename before it took this plugin's slug.
	'connect-elementor-forms-to-rd-station-pro/connect-elementor-forms-to-rd-station-pro.php',
) as $wpi_erds_pro ) {
	$wpi_erds_pro_alive = $wpi_erds_pro_alive
		|| in_array( $wpi_erds_pro, (array) get_option( 'active_plugins', array() ), true )
		|| ( is_multisite() && array_key_exists( $wpi_erds_pro, (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
}

if ( $wpi_erds_pro_alive ) {
	add_action(
		'admin_notices',
		static function () {
			echo '<div class="notice notice-warning"><p>' . esc_html__(
				'The Pro version is active, so this free copy is doing nothing. You can safely delete it — your integrations and connection stay with Pro.',
				'wpintegrations-connector-for-elementor-rd-station'
			) . '</p></div>';
		}
	);
	return;
}

define( 'WPI_ERDS_VERSION', '1.0.2' );
define( 'WPI_ERDS_FILE', __FILE__ );
define( 'WPI_ERDS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPI_ERDS_URL', plugin_dir_url( __FILE__ ) );
define( 'WPI_ERDS_SLUG', 'wpintegrations-connector-for-elementor-rd-station' );
define( 'WPI_ERDS_REST_NS', 'wpi_erds/v1' );

// PSR-4 autoloader for app/. No Composer needed at runtime.
spl_autoload_register(
	static function ( $class ) {
		$prefix = 'WPIntegrations\\ElementorRdStation\\';
		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}
		$relative = substr( $class, strlen( $prefix ) );
		$file     = WPI_ERDS_PATH . 'app/' . str_replace( '\\', '/', $relative ) . '.php';
		if ( is_readable( $file ) ) {
			require $file;
		}
	}
);

register_activation_hook( __FILE__, array( WPIntegrations\ElementorRdStation\Core\Application::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( WPIntegrations\ElementorRdStation\Core\Application::class, 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		WPIntegrations\ElementorRdStation\Core\Application::instance()->boot();
	}
);
