<?php
/**
 * Registers built-in triggers/actions and boots available triggers.
 *
 * @package WPIntegrations\ElementorRdStation
 */

namespace WPIntegrations\ElementorRdStation\Providers;

use WPIntegrations\ElementorRdStation\Core\ServiceProvider;
use WPIntegrations\ElementorRdStation\Integrations\Actions\RdStation\RdStationAction;
use WPIntegrations\ElementorRdStation\Integrations\Registry;
use WPIntegrations\ElementorRdStation\Integrations\Triggers\Elementor\ElementorTrigger;

defined( 'ABSPATH' ) || exit;

final class IntegrationServiceProvider extends ServiceProvider {

	public function boot(): void {
		$registry = $this->app->make( Registry::class );

		$registry->add_trigger( $this->app->make( ElementorTrigger::class ) );
		$registry->add_action( $this->app->make( RdStationAction::class ) );

		/**
		 * Add-ons register additional triggers and actions here.
		 *
		 * @param Registry $registry The integration registry.
		 */
		do_action( 'wpi_erds_register_integrations', $registry );

		add_action(
			'init',
			static function () use ( $registry ) {
				foreach ( $registry->triggers() as $trigger ) {
					if ( $trigger->available() ) {
						$trigger->register();
					}
				}
			}
		);
	}
}
