<?php

/**
 * =============================================
 *      Add Color option to the Customizer
 * =============================================
 */

namespace Devmonsta\Options\Customizer\Controls;

use Devmonsta\Traits\Singleton;

class Color
{

    use Singleton;

    public function add_color($args)
    {
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
}
