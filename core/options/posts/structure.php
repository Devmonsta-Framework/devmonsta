<?php
namespace Devmonsta\Options\Posts;

abstract class Structure
{
    public $content;
    public $controls_url;
    protected $scripts;
    public function __construct($content)
    {

        $this->content = $content;
        $this->controls_url = plugin_dir_url(__FILE__) . 'controls/';

    }

    public function add_script($script)
    {
        $this->scripts[] = $script;
     
    }

    function __destruct()
    {
   
    }

    abstract public function init();
    abstract public function render();
    abstract public function output();
}
