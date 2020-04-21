<?php

/**
 * =============================================
 *      Add Color option to the Customizer
 * =============================================
 */

namespace Devmonsta\Options\Customizer\Controls\Text;

use Devmonsta\Options\Customizer\Structures\Control;
use Devmonsta\Traits\Singleton;

class Text extends Control
{

    use Singleton;

    public function add_control($args)
    {
        add_action('customize_register', function ($wp_customize) use ($args) {

            $id = $args['id'];
            $args['settings'] = $id;
            $wp_customize->add_setting($id, [
                'default' => isset($args['default']) ? $args['default'] : '',
            ]);

            if (isset($args['selector'])) {
                $wp_customize->selective_refresh->add_partial($id, [
                    'selector' => $args['selector'],
                    'render_callback' => function () use ($args) {
                        echo get_theme_mod($args['settings']);
                    },
                ]);
            }

            $wp_customize->add_control(
                new \WP_Customize_Control(
                    $wp_customize,
                    $id,
                    $args
                )
            );

        });

    }

}
