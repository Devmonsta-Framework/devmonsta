<?php

namespace Devmonsta\Importer\Custom_Supports;

class Revslider {
    private $slider_source;

    public function set_source( $slider_source ) {

        $this->slider_source = $slider_source;
        return $this;
    }

    public function process_data() {

        if ( class_exists( 'RevSliderSlider' ) ) {
            $filepath = $this->slider_source;
            $slider   = new \RevSliderSlider();
            $slider->importSliderFromPost( true, true, $filepath );
        }

        return;
    }
}