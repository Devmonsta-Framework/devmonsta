<?php

namespace Devmonsta\Options\Customizer\Controls;

use Devmonsta\Traits\Singleton;

class Section
{

    use Singleton;

    public function add_section($args)
    {

    }

    

    public function init()
    {
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
