<?php

namespace Devmonsta\Options\Posts;

use Devmonsta\Traits\Singleton;

class View
{

    use Singleton;

    public function build($box_id, $controls)
    {

        // print_r($controls);
        foreach ($controls as $control) {

            if (Validator::instance()->check($control)) {
                if ($control['box_id'] == $box_id) {
                    $this->render($control);
                }

            }

        }

    }

    public function render($control_content)
    {
        if (isset($control_content['type'])) {
            $control_class = 'Devmonsta\Options\Posts\Controls\\' . ucwords($control_content['type']);
            if (class_exists($control_class)) {

                $control_class::show($control_content);
            }

        }

        // echo $control_type;
    }

}
