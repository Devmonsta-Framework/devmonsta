<?php

/**
 * =============================================
 *      Add Color option to the Customizer
 * =============================================
 */

namespace Devmonsta\Options\Customizer\Controls\Color;

use Devmonsta\Options\Customizer\Structures\Control;
use Devmonsta\Traits\Singleton;

class Color extends Control
{

    use Singleton;

    public function add_control($args)
    {
        add_action('customize_register', function ($wp_customize) use ($args) {

            $id = $args['id'];
            $args['settings'] = $id;
            $wp_customize->add_setting($id, [
                'default' => isset($args['default']) ? $args['default'] : '#000000',
            ]);

            $wp_customize->add_control(
                new \WP_Customize_Color_Control(
                    $wp_customize,
                    $id,
                    $args
                )
            );

        });

    }

}
