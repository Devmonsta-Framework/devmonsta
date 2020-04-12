<?php
/**
 * Plugin Name: Devmonsta
 * Description: Freamwork
 * Version: 1.0.0
 * Author: Xpeedstudio
 * Author URI:  https://xpeedstudio.com
 * Text Domain: devmonsta
 * License:  GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

use Devmonsta\Bootstrap;


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
    }

    /**
     * Initialize the plugin
     */
    public function init_plugin()
    {

        Bootstrap::instance()->init();

    }

    /**
     * Plugin activation
     */
    public function activate()
    {

    }
}

Devmonsta::init();
