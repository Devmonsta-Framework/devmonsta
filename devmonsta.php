<?php

/**
 * Plugin Name: Devmonsta
 * Plugin URI: http://devmonsta.com/
 * Description: A free WordPress custimiser with post meta options with Demo content installation that will help you develop premium themes fast & easy.
 * Version: 1.0.2
 * Author: 	devmonsta
 * Author URI: http://devmonsta.com
 * License: GPL3+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */



if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/autoloader/autoload.php';

final class Devmonsta
{

    /**
     * Plugin version
     */
    const version = '1.0.2';

    /**
     * Construcotr of the class
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
        define('DEVMONSTA', true);
        define('DEVMONSTA_VERSION', self::version);

        define('DEVMONSTA_PATH', plugin_dir_url(__FILE__));
        define('DEVMONSTA_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
        define('DEVMONSTA_CORE', DEVMONSTA_PATH . 'core/');
        define('DEVMONSTA_OPTIONS', DEVMONSTA_CORE . 'options/');
    }

    /**
     * Initialize the plugin
     */
    public function init_plugin()
    {

        Devmonsta\Bootstrap::instance()->init();
    }

    /**
     * Plugin activation
     */
    public function activate()
    {
    }
}

Devmonsta::init();
