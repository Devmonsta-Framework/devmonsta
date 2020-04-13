<?php

namespace Devmonsta\Libs;

class Posts
{

    protected static $box;
    protected static $control;

    public function add_box($box)
    {
        self::$box[] = $box;
    }

    public function add_control($control)
    {
        self::$control = $control;
    }



    public function all_boxes()
    {
        return self::$box;
    }

    public function all_controls(){
        return self::$control;
    }

}
