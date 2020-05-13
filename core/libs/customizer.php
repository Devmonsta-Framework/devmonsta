<?php
namespace Devmonsta\Libs;

class Customizer
{
    protected static $control;
    protected static $section;
    protected static $panel;
    protected static $settings;
    protected static $tabs;

    public function add_control($control)
    {
        self::$control[] = $control;
    }

    public function add_panel($panel)
    {
        self::$panel[] = $panel;
    }

    public function add_section($section)
    {
        self::$section[] = $section;
    }

    public function add_setting($setting){
        self::$settings[] = $setting;
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

    public function all_settings(){
        return self::$settings;
    }

    public function add_tab($tabs){
        self::$tabs = $tabs;
    }

    public function all_tabs(){
        return self::$tabs;
    }
}
