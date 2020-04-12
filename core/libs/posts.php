<?php

namespace Devmonsta\Libs;

class Posts
{
    

    public static $box;

    public function add_box($box)
    {
        self::$box[] = $box;
    }

    public function all_boxes()
    {
        return self::$box; 
    }

}
