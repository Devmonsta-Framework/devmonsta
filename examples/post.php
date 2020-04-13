<?php

use Devmonsta\Libs\Posts;

class Post extends Posts
{

    public function register_controls()
    {

        $this->add_box([
            'id' => 'post_box_1',
            'type' => 'post',
            'title' => 'Metabox for post',

        ]);

        $this->add_control([
            'box_id' => 'post_box_1',
            'type' => 'text',
            'lable' => 'Name',
            'name' => 'txt_name',
        ]);

        $this->add_control([
            'box_id' => 'post_box_1',
            'type' => 'email',
            'label' => 'Email',
            'name' => 'txt_email',
        ]);

    }

}
