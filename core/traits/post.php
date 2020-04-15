<?php
namespace Devmonsta\Traits;

trait Post
{
    protected static $data;

    public function add_data($data)
    {

        self::$data[] = $data;

    }

    public function get_data()
    {
        return self::$data;
    }
}
