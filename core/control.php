<?php

namespace Devmonsta;

class Control
{
    protected static $data;

    public function add_control($data)
    {

        self::$data[] = $data;

    }

    public function all_control()
    {
        return self::$data;
    }

}
