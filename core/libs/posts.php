<?php

namespace Devmonsta\Libs;

class Posts
{

    protected static $box;
    protected static $control;
    protected static $css;
    protected static $js;
    protected static $style_link;
    protected static $script_link;

    public function add_box($box)
    {
        self::$box[] = $box;
    }

    public function add_control($control)
    {
        self::$control[] = $control;
    }

    public function add_css($css)
    {
        self::$css[] = $css;
    }

    public function add_js($js)
    {
        self::$js[] = $js;
    }

    public function all_css()
    {
        return self::$css;
    }

    public function all_js()
    {
        return self::$js;
    }

    public function add_style_link($style_link)
    {
        self::$style_link[] = $style_link;
    }

    public function add_script_link($script_link)
    {
        self::$script_link[] = $script_link;
    }

    public function all_style_link()
    {
        return self::$style_link;
    }

    public function all_script_link()
    {
        return self::$script_link;
    }

    public function all_boxes()
    {
        return self::$box;
    }

    public function all_controls()
    {
        return self::$control;
    }

}
