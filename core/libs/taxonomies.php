<?php

namespace Devmonsta\Libs;

class Taxonomies
{
    protected static $data;

    public function add_taxonomy($data)
    {
        self::$data[] = $data;
    }

    public function all_taxonomies()
    {
        return self::$data;
    }
}
