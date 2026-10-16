<?php
/**
 * Elementor Pro Forms trigger.
 *
 * @package WPIntegrations\ElementorRdStation
 */

namespace WPIntegrations\ElementorRdStation\Integrations\Triggers\Elementor;

use WPIntegrations\ElementorRdStation\Integrations\FlowRunner;
use WPIntegrations\ElementorRdStation\Integrations\Trigger;

defined( 'ABSPATH' ) || exit;

final class ElementorTrigger extends Trigger {

	/** @var ElementorFormScanner */
	private $scanner;

	public function __construct( FlowRunner $runner, ElementorFormScanner $scanner ) {
		parent::__construct( $runner );
		$this->scanner = $scanner;
	}

	public function key(): string {
		return 'elementor';
	}

	public function label(): string {
		return __( 'Elementor Forms', 'wpintegrations-connector-for-elementor-rd-station' );
	}

	public function available(): bool {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	public function sources( bool $refresh = false ): array {
		return $this->scanner->forms( $refresh );
	}

	public function register(): void {
		add_action( 'elementor_pro/forms/new_record', array( $this, 'on_submit' ), 10, 2 );

		// Documents changed, so rediscover forms on the next request.
		add_action( 'elementor/editor/after_save', array( ElementorFormScanner::class, 'forget' ) );
		add_action( 'save_post', array( ElementorFormScanner::class, 'forget' ) );
	}

	/**
	 * @param object $record  \ElementorPro\Modules\Forms\Classes\Form_Record.
	 * @param object $handler \ElementorPro\Modules\Forms\Classes\Ajax_Handler.
	 */
	public function on_submit( $record, $handler ): void {
		if ( ! is_object( $record ) || ! method_exists( $record, 'get_form_settings' ) ) {
			return;
		}

		// The widget element id is canonical: unlike form_name it is unique and
		// survives renames. The name stays an alias so older flows keep firing.
		$element_id = sanitize_text_field( (string) $record->get_form_settings( 'id' ) );
		$form_name  = sanitize_text_field( (string) $record->get_form_settings( 'form_name' ) );
		$global_id  = $this->global_widget_id( $record );
		$source     = '' !== $global_id ? $global_id : ( '' !== $element_id ? $element_id : $form_name );

		$raw    = (array) $record->get( 'fields' );
		$fields = array();
		foreach ( $raw as $id => $field ) {
			$value = isset( $field['value'] ) ? $field['value'] : '';
			$fields[ sanitize_text_field( (string) $id ) ] = is_array( $value )
				? array_map( 'sanitize_text_field', $value )
				: sanitize_text_field( (string) $value );
		}

		if ( ! $fields ) {
			return;
		}

		$this->remember_shape( $source, array_keys( $fields ), $form_name );
		$aliases = array( $source, $element_id, $form_name );
		$this->dispatch( array_values( array_unique( array_filter( $aliases ) ) ), $fields, $this->request_meta() );
	}

	/**
	 * A form inside a Global Widget submits the id of the widget's instance on
	 * the page, while the form list offers the one inside its template. Elementor
	 * reports the template as form_post_id, so the template's id is recovered
	 * from there.
	 *
	 * @param object $record Form record.
	 * @return string The template's form element id, or '' for an ordinary form.
	 */
	private function global_widget_id( $record ): string {
		$template_id = (int) $record->get_form_settings( 'form_post_id' );
		$post_id     = (int) $record->get_form_settings( 'edit_post_id' );
		if ( ! $template_id || $template_id === $post_id || 'elementor_library' !== get_post_type( $template_id ) ) {
			return '';
		}

		$elements = json_decode( (string) get_post_meta( $template_id, '_elementor_data', true ), true );
		$widget   = is_array( $elements ) ? reset( $elements ) : null;
		if ( ! is_array( $widget ) || 'form' !== ( $widget['widgetType'] ?? '' ) ) {
			return '';
		}
		return sanitize_text_field( (string) ( $widget['id'] ?? '' ) );
	}

	/**
	 * Metadata only available at submission time. RD Station's own tracking
	 * cookies preserve lead attribution like a native form; the time and user
	 * are captured now because delivery runs later.
	 */
	private function request_meta(): array {
		$meta = array(
			'page_url'     => esc_url_raw( wp_get_referer() ? wp_get_referer() : '' ),
			'submitted_at' => gmdate( 'Y-m-d H:i:s' ),
		);

		$user = wp_get_current_user();
		if ( $user && $user->exists() ) {
			$meta['user_email'] = sanitize_email( (string) $user->user_email );
			$meta['user_name']  = sanitize_text_field( (string) $user->display_name );
		}

		// RD Station's tracker keeps a JSON blob in `rdtrk`; the conversion API
		// wants only the visitor UUID inside it.
		$rdtrk = '';
		if ( isset( $_COOKIE['rdtrk'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only tracking cookie, no state change.
			$rdtrk = sanitize_text_field( wp_unslash( $_COOKIE['rdtrk'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} elseif ( isset( $_COOKIE['_rdtrk'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$rdtrk = sanitize_text_field( wp_unslash( $_COOKIE['_rdtrk'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		if ( preg_match( '/[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}/i', $rdtrk, $match ) ) {
			$meta['client_tracking_id'] = $match[0];
		}
		// PHP stores the `__trf.src` cookie under `__trf_src`.
		if ( isset( $_COOKIE['__trf_src'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$meta['traffic_source'] = sanitize_text_field( wp_unslash( $_COOKIE['__trf_src'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return $meta;
	}
}
