<?php

namespace Devmonsta\Options\Posts;

use Devmonsta\Traits\Singleton;

class View
{

    use Singleton;

    public function build($contents)
    {

        foreach ($contents as $content) {

            if (Validator::instance()->check($content)) {
                $this->render($content);
            }

        }

    }

    public function render($content)
    {
        
    }

}
