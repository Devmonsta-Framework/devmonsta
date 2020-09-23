<?php
namespace Devmonsta\Options\Posts;

abstract class Structure {
    public $content;
    public $controls_url;
    protected static $scripts;
    protected static $styles;
    public $prefix;
    public $taxonomy;

    public function __construct( $content, $taxonomy = null ) {
        $this->taxonomy     = $taxonomy;
        $this->prefix       = 'devmonsta_';
        $this->content      = $content;
        $this->controls_url = plugin_dir_url( __FILE__ ) . 'controls/';
    }

    public function add_script( $script ) {
        self::$scripts[] = $script;
    }

    public function get_all_scripts() {
        return self::$scripts;
    }

    public static function get_data() {
        return self::$scripts;
    }

    public function add_style( $style ) {
        self::$styles[] = $this->controls_url . $style;
    }

    public function save_eneque() {
        update_option( 'devmonsta_scripts', self::$scripts );
        update_option( 'devmonsta_styles', self::$styles );
    }

    public function __call( $method, $arguments ) {

        if ( method_exists( $this, $method ) ) {
            $this->save_eneque();
            return call_user_func( [$this, $method] );
        }

    }

    public function __destruct() {
        $this->save_eneque();
    }

    abstract public function init();
    abstract public function render();
    abstract public function output();
    abstract public function enqueue( $meta_owner );
    abstract public function edit_fields( $term, $taxonomy );

    /**
     * Prepare all attributes for parent class of this control
     *
     * @param [type] $content
     * @return void
     */
    public function prepare_default_attributes( $content, $additional_classes = "" ) {
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

        $condition_class = "";
        $condition_data  = "";

        if ( isset( $content['conditions'] ) && is_array( $content['conditions'] ) ) {
            $condition_class = "devm-condition-active";
            $condition_data  = json_encode( $content['conditions'], true );
            $default_attributes .= " data-devm_conditions='$condition_data' ";
        }

        $class_attributes = "class='$additional_classes devm-option form-field active-script $condition_class $dynamic_classes'";
        $default_attributes .= $class_attributes;
        return $default_attributes;
    }

}
