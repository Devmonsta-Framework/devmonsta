<?php

if ( !defined( 'DEVM' ) ) {
    die( 'Forbidden' );
}

final class _Devm {
    /** @var bool If already loaded */
    private static $loaded = false;

    /** @var DEVM_Framework_Manifest */
    public $manifest;

    /** @var _DEVM_Component_Backend */
    public $backend;

    /** @var _DEVM_Component_Theme */
    public $theme;

    public function __construct() {

        if ( self::$loaded ) {
            trigger_error( 'Framework already loaded', E_USER_ERROR );
        } else {
            self::$loaded = true;
        }

        $devm_dir = devm_get_framework_directory();

        // manifest
        {
            require $devm_dir . '/manifest.php';
            /** @var array $manifest */

            $this->manifest = new DEVM_Framework_Manifest( $manifest );

            add_action( 'devm_init', [$this, 'check_requirements'], 1 );
        }

        // components
        {
            $this->backend = new _DEVM_Component_Backend();
            $this->theme   = new _DEVM_Component_Theme();
        }

    }

    /**
     * @internal
     */
    public function check_requirements() {

        if ( is_admin() && !$this->manifest->check_requirements() ) {
            DEVM_Flash_Messages::add(
                'devm_requirements',
                __( 'Framework requirements not met:', 'devmonsta' ) . ' ' . $this->manifest->get_not_met_requirement_text(),
                'warning'
            );
        }

    }

}

/**
 * @return _DEVM Framework instance
 */
function devm() {
    static $DEVM = null;

    // cache
    if ( $DEVM === null ) {
        $DEVM = new _Devm();
    }

    return $DEVM;
}
