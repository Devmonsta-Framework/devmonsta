<?php
namespace Devmonsta\Options\Posts;

abstract class Structure {
    public $content;
    public $controls_url;
    protected static $scripts;
    protected static $styles;
    public $prefix;
    public function __construct($content) {
        $this->prefix = 'devmonsta_';

        $this->content = $content;
        $this->controls_url = plugin_dir_url(__FILE__) . 'controls/';

    }

    public function add_script($script) {
        self::$scripts[] = $script;

    }

    public function get_all_scripts() {
        return self::$scripts;
    }

    public static function get_data() {
        return self::$scripts;
    }

    public function add_style($style) {
        self::$styles[] = $this->controls_url . $style;
    }

    public function save_eneque() {
        update_option('devmonsta_scripts', self::$scripts);
        update_option('devmonsta_styles', self::$styles);
    }

    public function __call($method, $arguments) {

        if (method_exists($this, $method)) {
            $this->save_eneque();
            return call_user_func(array($this, $method));
        }
    }

    public function __destruct() {
        $this->save_eneque();

    }

    abstract public function init();
    abstract public function render();
    abstract public function output();
    abstract public function enqueue();
}
