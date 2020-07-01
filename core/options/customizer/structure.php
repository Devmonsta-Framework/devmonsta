<?php
namespace Devmonsta\Options\Customizer;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Structure extends \WP_Customize_Control {
    
    /**
     * Prepare all attributes for parent class of this control
     *
     * @param [type] $content
     * @return void
     */
    public function prepare_default_attributes( $content, $additional_classes = "" ){
        $attrs              = isset( $content['attr'] ) ? $content['attr'] : '';
        $default_attributes = "";
        $dynamic_classes    = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {
            foreach ( $attrs as $key => $val ) {
                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }
            }
        }

        $condition_class    = "";
        $condition_data     = "";
        if( isset( $content['conditions'] ) && is_array( $content['conditions'] ) ){
            $condition_class = "devm-condition-active";
            $condition_data = json_encode($content['conditions'], true);
            $default_attributes .= " data-devm_conditions='$condition_data' ";
        }
        $class_attributes = "class='$additional_classes devm-option form-field $condition_class $dynamic_classes'";
        $default_attributes .= $class_attributes;
        return $default_attributes;
    }
}
