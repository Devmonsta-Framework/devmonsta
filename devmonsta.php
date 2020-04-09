<?php
/**
 * Plugin Name: Devmonsta
 * Description: Freamwork
 */

use Devmonsta\Control;

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
        define('DEVMONSTA', true);
        define('DEVMONSTA_VERSION', self::version);
    }

    /**
     * Initialize the plugin
     */
    public function init_plugin()
    {

        $function_file = get_template_directory() . '/devmonsta/control.php';
        if (file_exists($function_file)) {
            require_once $function_file;

            $childrens = array();
            foreach (get_declared_classes() as $class) {
                if (is_subclass_of($class, 'Devmonsta\Control')) {
                    $childrens[] = $class;
                }

            }

            $color = new Control;

            foreach ($childrens as $child_class) {

                $control = new $child_class;
                $control->register_controls();

            }

            $all_controls = $color->all_control();

            foreach ($all_controls as $ctrl) {

                $args = $ctrl;

                if ($ctrl['type'] == 'color') {
                    add_action('customize_register', function ($wp_customize) use ($args) {

                        $wp_customize->add_setting(
                            $args['settings'],
                            [
                                'default' => isset($args['default']) ? $args['default'] : '#000000',
                            ]
                        );

                        $wp_customize->add_control(
                            new \WP_Customize_Color_Control(
                                $wp_customize,
                                $args['name'],
                                $args
                            )
                        );

                    });
                }

                if ($ctrl['type'] == 'text') {

                    add_action('customize_register', function ($wp_customize) use ($ctrl) {

                        $wp_customize->add_control(new \WP_Customize_Control(
                            $wp_customize,
                            $ctrl['name'],
                            $ctrl
                        ));

                    });

                    
                }

            }

            add_action('customize_register', function ($wp_customize) {

                $wp_customize->add_panel('text_blocks', array(
                    'priority' => 500,
                    'theme_supports' => '',
                    'title' => __('Text Blocks', 'genesischild'),
                    'description' => __('Set editable text for certain content.', 'genesischild'),
                ));
                // Add Footer Text
                // Add section.
                $wp_customize->add_section('custom_footer_text', array(
                    'title' => __('Change Footer Text', 'genesischild'),
                    'panel' => 'text_blocks',
                    'priority' => 10,
                ));
                // Add setting
                $wp_customize->add_setting('footer_text_block', array(
                    'default' => __('default text', 'genesischild'),
                    'sanitize_callback' => 'sanitize_text',
                ));
                // Add control
                $wp_customize->add_control(new \WP_Customize_Control(
                    $wp_customize,
                    'custom_footer_text',
                    array(
                        'label' => __('Footer Text', 'genesischild'),
                        'section' => 'custom_footer_text',
                        'settings' => 'footer_text_block',
                        'type' => 'text',
                    )
                )
                );

                // Sanitize text
                function sanitize_text($text)
                {
                    return sanitize_text_field($text);
                }

            });

        }

    }

    /**
     * Plugin activation
     */
    public function activate()
    {

    }
}

Devmonsta::init();
