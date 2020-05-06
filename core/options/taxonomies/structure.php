<?php

namespace Devmonsta\Options\Taxonomies;

abstract class Structure {

    public $content;
    public $taxonomy;
    public function __construct( $taxonomy, $content ) {
        $this->taxonomy = $taxonomy;
        $this->content  = $content;
    }

    abstract public function init();
    abstract public function render();
    abstract public function output();
    abstract public function enqueue( $meta_owner );
    abstract public function edit_fields( $term, $taxonomy );
}
