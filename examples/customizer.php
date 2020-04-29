<?php

/**
 * =====================================
 *      Example file for customizer
 * =====================================
 */

class Customizer extends \Devmonsta\Libs\Customizer {

    public function register_controls(){

        
        $this->add_control([

            'name'       => 'link_color',
            'label'      => __('Link Color', 'devmonsta'),
            'section'    => 'colors',
            'settings'   => 'devmonsta_link_color',
            'default'    => '#00c3ff',
            'type'       => 'color'

        ]);

        $this->add_control([

            'name'       => 'cool_color',
            'label'      => __('Cool Color', 'devmonsta'),
            'section'    => 'colors',
            'settings'   => 'devmonsta_cool_color',
            'default'    => '#00c3ff',
            'type'       => 'color'

        ]);

    }

}