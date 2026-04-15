<?php
/**
 * Plugin Name: KO – GF Address Character Limit
 * Description: Enforces a 30-character limit on Gravity Forms Address Line 1 & 2 across all forms (frontend maxlength + server-side validation).
 * Version: 1.1.0
 * Author: KO
 */

if ( ! defined( 'ABSPATH' ) ) exit;

final class KO_GF_Address_Char_Limit {
    const VERSION = '1.1.0';
    const LIMIT_LINE1 = 30;
    const LIMIT_LINE2 = 30;

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );

        // Server-side enforcement
        add_filter( 'gform_pre_validation', [ $this, 'validate_address_lines' ] );
        add_filter( 'gform_pre_submission_filter', [ $this, 'validate_address_lines' ] );
    }

    public function enqueue() {
        if ( ! class_exists( 'GFForms' ) ) return;

        wp_register_script(
            'ko-gf-address-charlimit',
            plugins_url( 'assets/js/ko-gf-address-charlimit.js', __FILE__ ),
            [],
            self::VERSION,
            true
        );

        wp_localize_script( 'ko-gf-address-charlimit', 'KO_GF_ADDRESS_LIMITS', [
            'line1' => self::LIMIT_LINE1,
            'line2' => self::LIMIT_LINE2,
        ] );

        wp_enqueue_script( 'ko-gf-address-charlimit' );
    }

    public function validate_address_lines( $form ) {
        if ( empty( $form['fields'] ) ) return $form;

        foreach ( $form['fields'] as &$field ) {
            if ( ! is_object( $field ) || $field->type !== 'address' ) continue;

            $field_id = (string) $field->id;

            $line1 = (string) rgpost( "input_{$field_id}.1" );
            $line2 = (string) rgpost( "input_{$field_id}.2" );

            $len1 = function_exists( 'mb_strlen' ) ? mb_strlen( $line1 ) : strlen( $line1 );
            $len2 = function_exists( 'mb_strlen' ) ? mb_strlen( $line2 ) : strlen( $line2 );

            if ( $len1 > self::LIMIT_LINE1 || $len2 > self::LIMIT_LINE2 ) {
                $field->failed_validation  = true;
                $field->validation_message = 'Address lines must be 30 characters or fewer.';
            }
        }

        return $form;
    }
}

new KO_GF_Address_Char_Limit();
