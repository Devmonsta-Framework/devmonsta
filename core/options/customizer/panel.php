<?php
namespace Devmonsta\Options\Customizer;

use Devmonsta\Traits\Singleton;

class Panel
{

    use Singleton;

    public function add_panel($args)
    {
        add_action('customize_register', function ($wp_customize) {

            // footer text panel
            $wp_customize->add_panel('text_blocks', array(
                'priority' => 500,
                'theme_supports' => '',
                'title' => __('Text Blocks', 'genesischild'),
                'description' => __('Set editable text for certain content.', 'genesischild'),
            ));

            // color panel

            $wp_customize->add_panel('devmonsta_colors', [
                'priority' => 500,
                'theme_supports' => '',
                'title' => __('My Colors', 'devmonsta'),
                'description' => __('Set colors for the theme', 'devmonsta'),
            ]);

            // Add Footer Text
            // Add section.
            $wp_customize->add_section('custom_footer_text', array(
                'title' => __('Change Footer Text', 'genesischild'),
                'panel' => 'text_blocks',
                'priority' => 10,
            ));

            // add colors section

            $wp_customize->add_section('header_colors', [
                'title' => __('Header colors', 'devmonsta'),
                'panel' => 'devmonsta_colors',
                'priority' => 10,
            ]);

            // Add setting
            $wp_customize->add_setting('footer_text_block', array(
                'default' => __('default text', 'genesischild'),
            ));

            // add color settings

            $wp_customize->add_setting('header_color_1', [
                'default' => '#000000',
            ]);

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
            ));

            $wp_customize->add_setting('devmonsta_cool_color_settings', [
                'default' => '#000000',
            ]);

            $wp_customize->add_control(
                new \WP_Customize_Control(
                    $wp_customize,
                    'header_color_control_1',
                    [
                        'name' => 'header_color_xoxo_a',
                        'label' => __('Header color 1', 'devmonsta'),
                        'section' => 'header_colors',
                        'settings' => 'devmonsta_cool_color_settings',
                        'default' => '#00c3ff',
                        'type' => 'color',
                    ]
                )
            );

            $wp_customize->add_control(
                new \WP_Customize_Color_Control(
                    $wp_customize,
                    'name',
                    array(
                        'name' => 'cool_color',
                        'label' => __('Cool Color', 'devmonsta'),
                        'section' => 'header_colors',
                        'settings' => 'devmonsta_cool_color_settings',
                        'default' => '#00c3ff',
                        'type' => 'color',
                    )
                )
            );

            $wp_customize->add_control(
                'your_control_id', 
                array(
                    'label'    => __( 'Control Label', 'mytheme' ),
                    'section'  => 'header_colors',
                    'settings' => 'devmonsta_cool_color_settings',
                    'type'     => 'radio',
                    'choices'  => array(
                        'left'  => 'Left',
                        'right' => 'Right',
                    ),
                )
            );
        });
    }
}
