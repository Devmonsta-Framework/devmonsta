<?php
namespace Devmonsta\Libs;

class Customizer
{
    protected static $control;
    protected static $section;
    protected static $panel;
    protected static $settings;

    public function add_control($control)
    {
        self::$control[] = $control;
    }

    public function add_panel($panel)
    {
        self::$panel = $panel;
    }

    public function add_section($section)
    {
        self::$section = $section;
    }

    public function all_controls()
    {
        return self::$control;
    }

    public function all_panels()
    {
        return self::$panel;
    }

    public function all_sections()
    {
        return self::$section;
    }

}
