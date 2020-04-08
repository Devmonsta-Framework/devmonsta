<?php
/**
 * Plugin Name: Devmonsta
 */

use Devmonsta\Libs\Color;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

final class Devmonsta
{

    /**
     * Plugin version
     */
    const version = '1.0';

    /**
     * Class construcotr
     */
    private function __construct()
    {
        $this->define_constants();

        register_activation_hook(__FILE__, [$this, 'activate']);

        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Initializes a singleton instance
     *
     */

    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     */

    public function define_constants()
    {
        define('DEVMONSTA',true);
        define('DEVMONSTA_VERSION', self::version);
    }

    /**
     * Initialize the plugin
     */
    public function init_plugin()
    {
        (new Color)->init();
    }

    /**
     * Plugin activation
     */
    public function activate()
    {

    }
}

/**
 * Initializes the main plugin
 */
function devmonsta()
{
    return Devmonsta::init();
}


devmonsta();
