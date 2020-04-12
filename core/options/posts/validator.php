<?php
namespace Devmonsta\Options\Posts;

use Devmonsta\Traits\Singleton;

class Validator
{
    use Singleton;
    public function check($args)
    {
        return true;
    }
}
