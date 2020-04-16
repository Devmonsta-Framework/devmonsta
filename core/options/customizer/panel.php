<?php
namespace Devmonsta\Options\Customizer;

use Devmonsta\Traits\Singleton;

class Panel
{

    use Singleton;

    public function add_panel($args)
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
                'default' => __('default text', 'genesischild')
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
            ));

            $wp_customize->add_setting('devmonsta_cool_color',[
                'default' => '#000000'
            ]);

            $wp_customize->add_control(
              new \WP_Customize_Color_Control(
                  $wp_customize,
                  'name',
                  array(
                    'name' => 'cool_color',
                    'label' => __('Cool Color', 'devmonsta'),
                    'section' => 'custom_footer_text',
                    'settings' => 'devmonsta_cool_color',
                    'default' => '#00c3ff',
                    'type' => 'color',
                  )
              )  
            );
        });
    }
}
