<?php

namespace Devmonsta\Libs;

class Repeater
{

    protected static $control;
    protected $id;
    protected $box_id;
    public function __construct($id, $box_id)
    {
        $this->id = $id;
        $this->box_id = $box_id;
    }

    public function add_control($control)
    {
        self::$control[] = [
            'repeater_id' => $this->id,
            'box_id' => $this->box_id,
            'control' => $control,
        ];
    }

    public function all_controls()
    {
        return self::$control;
    }

}
