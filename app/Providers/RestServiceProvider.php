<?php
/**
 * Registers REST routes from routes/api.php.
 *
 * @package WPIntegrations\ElementorRdStation
 */

namespace WPIntegrations\ElementorRdStation\Providers;

use WPIntegrations\ElementorRdStation\Core\ServiceProvider;
use WPIntegrations\ElementorRdStation\Http\Router;

defined( 'ABSPATH' ) || exit;

final class RestServiceProvider extends ServiceProvider {

	public function boot(): void {
		add_action(
			'rest_api_init',
			function () {
				$routes = require WPI_ERDS_PATH . 'routes/api.php';
				$routes( new Router( $this->app ) );
			}
		);
	}
}
