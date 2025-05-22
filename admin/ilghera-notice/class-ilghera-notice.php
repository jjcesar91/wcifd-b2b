<?php
/**
 * ilGhera Notice class
 *
 * @author ilGhera
 * @package ilghera-notice/ 
 * @version 1.0.1
 */

/**
 * ilGhera Notice
 */
if ( ! class_exists( 'Ilghera_Notice' ) ) {

    class Ilghera_Notice {

        /**
         * The products installed
         */
        public $products = array();


        /**
         * The current product
         */
        private $current_product = array();


        /**
         * Enqueue scripts
         *
         * @return void
         */
        public function enqueue_scripts() {

            wp_enqueue_style( 'ilghera-notice-style', plugin_dir_url( __FILE__ ) . 'css/ilghera-notice.css' );

        }


        /**
         * Get the plugin text domain
         *
         * @return string
         */
        private function get_text_domain() {

            $output = isset( $this->current_product['domain'] ) && $this->current_product['domain'] ? $this->current_product['domain'] : $this->current_product['sign'];

            return $output;

        }


        /**
         * The notice 
         *
         * @param string $message the text to add to the notice.
         * @param bool   $renew different button text with true.
         *
         * @return void 
         */
        public function the_notice( $message, $renew = false ) {

            $url         = sprintf( 'https://www.ilghera.com/product/%s/', $this->current_product['slug'] );
            $button_text = $renew ? __( 'Renew now', $this->get_text_domain() ) : __( 'Get a license', $this->get_text_domain() );
            $output      = '<div class="update-nag notice notice-warning ilghera-notice-warning is-dismissible">';
            $output     .= '<div class="ilghera-notice__content">';

            /* Translators: img URL */
            $output .= sprintf(
                '<div class="ilghera-notice__logo"><img src="%s"></div>',
                plugin_dir_url( __FILE__ ) . 'images/ilGhera-icon-40px.png',
            );

            /* Translators: product name, button text */
            $output .= sprintf(
                '<div class="ilghera-notice__message"><b>%1$s</b>. %2$s</div>',
                $this->current_product['name'],
                $message
            );

            $output .= '</div>'; // ilghera-notice__content.

            /* Translators: img URL, button text */
            $output .= sprintf(
                '<div class="ilghera-notice__buttons"><a href="%1$s" class="button-primary" target="_blank">%2$s</a></div>',
                $url,
                $button_text
            );

            $output .= '</div>'; // ilghera-notice-warnin.

            echo wp_kses_post( $output );

        }


        /**
         * The message to the admin with an expired license
         *
         * @return void 
         */
        public function expired_license() {

            $message = wp_kses_post(
                __( 'Your license is <u>expired</u> and you\'re unable to receive updates.', $this->get_text_domain() )
            );
     
            $this->the_notice( $message, true ); 

        }


        /**
         * The message to the admin with a bad license
         *
         * @return void 
         */
        public function bad_license() {

            $message = wp_kses_post(
                __( 'It seems like your license is <u>not valid</u> and you\'re unable to receive updates.', $this->get_text_domain() )
            );
     
            $this->the_notice( $message ); 

        }


        /**
         * The message to the admin with license not set
         *
         * @return void 
         */
        public function no_license() {

            $message = wp_kses_post(
                __( 'You have not set up a license key and you cannot receive updates.', $this->get_text_domain() )
            );
     
            $this->the_notice( $message ); 

        }


        /**
         * Check if the license is expired
         */
        public function check_license() {

            if ( is_array( $this->products ) ) {

                foreach ( $this->products as $product ) {
                
                    if ( isset( $product['name'], $product['slug'], $product['sign'] ) ) {

                        $this->current_product = $product;

                        /* Get the Premium key */
                        $key_name = $product['sign'] . '-premium-key'; 
                        $key      = get_option( $key_name ); 

                        if ( isset( $_POST[ $key_name ], $_POST[ $key_name . '-nonce' ] ) && wp_verify_nonce( wp_unslash( $_POST[ $key_name . '-nonce' ] ), $key_name ) ) {

                            $key = sanitize_text_field( $_POST[ $key_name ] );

                        }

                        if ( $key ) {
                        
                            /* Get the Premium key details */
                            $decoded_key = explode( '|', base64_decode( $key ) );

                            if ( isset( $decoded_key[1] ) ) {

                                $bought_date = date( 'd-m-Y', strtotime( $decoded_key[1] ) );
                                $limit       = strtotime( $bought_date . ' + 365 day' );
                                $now         = strtotime( 'today' );

                                if ( $limit < $now ) {

                                    add_action( 'admin_notices', array( $this, 'expired_license' ) );

                                }

                            } else {

                                add_action( 'admin_notices', array( $this, 'bad_license' ) );

                            }

                        } else {

                            add_action( 'admin_notices', array( $this, 'no_license' ) );

                        }

                    }

                }

            }

        }

    } 

    new Ilghera_Notice();

}

/* Get the class extension */
require plugin_dir_path( __FILE__ ) . 'extension.php';

