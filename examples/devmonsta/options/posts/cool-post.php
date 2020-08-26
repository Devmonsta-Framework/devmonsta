<?php
use Devmonsta\Libs\Posts;

class Coolpost extends Posts{

    public function register_controls(){
        $this->add_box([
            'id' => 'cool_post_box_1',
            'post_type' => 'cool-post',
            'title' => 'Meta box for Cool Page'
        ]);
    }

}