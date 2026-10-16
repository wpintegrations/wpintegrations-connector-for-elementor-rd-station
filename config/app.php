<?php
/**
 * Application configuration.
 *
 * @package WPIntegrations\ElementorRdStation
 */

defined( 'ABSPATH' ) || exit;

return array(
	'providers' => array(
		WPIntegrations\ElementorRdStation\Providers\AppServiceProvider::class,
		WPIntegrations\ElementorRdStation\Providers\DatabaseServiceProvider::class,
		WPIntegrations\ElementorRdStation\Providers\IntegrationServiceProvider::class,
		WPIntegrations\ElementorRdStation\Providers\RetentionServiceProvider::class,
		WPIntegrations\ElementorRdStation\Providers\RestServiceProvider::class,
		WPIntegrations\ElementorRdStation\Providers\AdminServiceProvider::class,
	),
);
