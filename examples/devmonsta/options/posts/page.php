<?php
use Devmonsta\Libs\Posts;

class Page extends Posts{

    public function register_controls(){
        $this->add_box([
            'id' => 'page_box_1',
            'post_type' => 'page',
            'title' => 'First Box'
        ]);

        $this->add_box([
            'id' => 'page_box_2',
            'post_type' => 'page',
            'title' => 'Second Box'
        ]);

        /**
         * control for text input
         */
        $this->add_control( [
            'box_id' => 'page_box_1',
            'type'   => 'text',
            'name'   => 'f_name_1',
            'value'  => 'default text value',
            'desc'   => 'use this area to input text',
            'label'  => __( 'Text Input Two', '{domain}' ),
        ] );

        /**
         * control for url input
         */
        $this->add_control( [
            'box_id' => 'page_box_2',
            'name'   => 'user_url_one',
            'type'   => 'url',
            'value'  => 'http://www.xs.com',
            'label'  => __( 'Enter valid URL One', '{domain}' ),
            'desc'   => __( 'Url Description', '{domain}' ),
        ] );

        // devm_print(devm_meta('18', 'f_name_1'));
    }

}