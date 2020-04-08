<?php
namespace Devmonsta\Libs;

use Exception;

class Color
{
    public $data;

    public $child;

    public function add_control($data)
    {

        $args = [
            'label' => __('Link Color', 'tcx'),
            'section' => 'colors',
            'settings' => 'devmonsta_link_color',
        ];

        add_action('customize_register', function ($wp_customize) use ($data, $args) {

            $wp_customize->add_setting(
                'devmonsta_link_color',
                [
                    'default' => '#000000',
                ]
            );

            $wp_customize->add_control(
                new \WP_Customize_Color_Control(
                    $wp_customize,
                    'link_color',
                    $args
                )
            );

        });
    }

    public function __construct()
    {

        $childrens = array();
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'Devmonsta\Libs\Color')) {
                $childrens[] = $class;
            }

        }

        foreach ($childrens as $child_class) {
            
            if(class_exists($child_class)){
                
               
            }
            
        }

        // error_log('Constructor :'.serialize($childrens));
    }

    public function init()
    {

    }

    public function devmonsta_register_theme_customizer($wp_customize)
    {

        $wp_customize->add_setting(
            'devmonsta_link_color',
            [
                'default' => '#000000',
            ]
        );

        $wp_customize->add_control(
            new \WP_Customize_Color_Control(
                $wp_customize,
                'link_color',
                [
                    'label' => __('Link Color', 'tcx'),
                    'section' => 'colors',
                    'settings' => 'devmonsta_link_color',
                ]
            )
        );

    }

}
