<?php

use Devmonsta\Libs\Taxonomies;

class Category extends Taxonomies
{

    public function register_controls()
    {

        $this->add_control([
            'type' => 'text',
            'placeholder' => 'Category First name',
            'label' => 'First name',
            'name' => 'c_f_name'
        ]);

    }

}
