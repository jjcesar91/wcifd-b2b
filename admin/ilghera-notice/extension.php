<?php
/**
 * ilGhera Notice class extension
 *
 * @author ilGhera
 * @package ilghera-notice/ 
 * @since 1.0.1
 */

/* The extension */
class WCIFD_Notice extends Ilghera_Notice {

    /**
     * The construct
     */
    public function __construct() {

        $this->products[] = array(
            'name'   => 'WC Importer for Danea - Premium',
            'slug'   => 'woocommerce-importer-for-danea-premium',
            'sign'   => 'wcifd',
            'domain' => 'wc-importer-for-danea',
        );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        $this->check_license();

    }

}
new WCIFD_Notice();

