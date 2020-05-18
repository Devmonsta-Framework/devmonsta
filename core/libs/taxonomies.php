<?php

namespace Devmonsta\Libs;

class Taxonomies
{
    protected static $data;

    public function add_control($data)
    {
        self::$data[] = $data;
    }

    public function all_controls()
    {
        return self::$data;
    }

    public function __destruct()
    {
        self::$data[] = '';
    }
}
