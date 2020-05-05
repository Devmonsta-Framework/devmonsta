<?php

namespace Devmonsta\Options\Taxonomies;

use Devmonsta\Libs\Taxonomies as LibsTaxonomies;
use Devmonsta\Traits\Singleton;

class Taxonomies {

    use Singleton;

    /**
     * Entry point of taxonomy control
     *
     * @return void
     */
    public function init() {
        global $pagenow;

        if ( $pagenow == 'edit-tags.php' ) {

            if ( isset( $_GET['taxonomy'] ) ) {
                $taxonomy_file = get_template_directory() . '/devmonsta/options/taxonomies/' . $_GET['taxonomy'] . '.php';

                if ( file_exists( $taxonomy_file ) ) {

                    require_once $taxonomy_file;
                    $path = $taxonomy_file;
                    $file = basename( $path );
                    $file = basename( $path, ".php" );

                    $taxonomy = $file;

                    /**
                     * Save term meta
                     */

                    update_option( 'dm_taxonomy', $taxonomy );

                    /**
                     * Edit term meta
                     */

                    $class_name = $this->make_class_structure( $file );

                    $taxonomy_lib = new LibsTaxonomies;

                    if ( class_exists( $class_name ) ) {
                        $taxonomy_class = new $class_name;

                        if ( method_exists( $taxonomy_class, 'register_controls' ) ) {
                            $taxonomy_class->register_controls();
                        }

                        $controls = $taxonomy_lib->all_controls();

                        // error_log('Taxonomy : ' . $taxonomy . ' and data ' . serialize($controls));

                        $this->build_taxonomoy( $taxonomy, $controls );

                    }

                }

            }

        }

        $t = get_option( 'dm_taxonomy' ); // T for taxonomy name

        add_action( 'created_' . $t, [$this, 'save_meta'], 10, 2 );
        add_action( $t . '_edit_form_fields', [$this, 'edit_meta'], 10, 2 );
        add_action( 'edited_' . $t, [$this, 'update_meta'], 10, 2 );

    }

    /**
     * Generate full class name from control type field
     *
     * @param [type] $file
     * @return void
     */
    public function make_class_structure( $file ) {
        $class_name = explode( '-', $file );
        $class_name = array_map( 'ucfirst', $class_name );
        $class_name = implode( '', $class_name );

        return $class_name;
    }

    /**
     * Buils controls dynamically from controls directory
     *
     * @param [type] $taxonomy
     * @param [type] $controls
     * @return void
     */
    public function build_taxonomoy( $taxonomy, $controls ) {

        foreach ( $controls as $control ) {

            if ( isset( $control['type'] ) ) {

                $control_type        = $control['type'];
                $original_class_name = $this->make_class_structure( $control_type );

                $control_class = 'Devmonsta\Options\Posts\Controls\\' .
                    $original_class_name . '\\' .
                    $original_class_name;

                if ( class_exists( $control_class ) ) {

                    $control_object = new $control_class( $control, $taxonomy );

                    $control_object->init();
                    $control_object->enqueue();
                    $control_object->columns();

                    add_action( $taxonomy . '_add_form_fields',
                        function () use ( $control_object ) {
                            $control_object->render();
                        }, 10, 2 );

                } else {

                    $file = DM_CORE . 'options/posts/controls/' . $control['type'] . '/' . $control['type'] . '.php';
                    include_once $file;

                    if ( class_exists( $control_class ) ) {

                        $control = new $control_class( $control, $taxonomy );
                        $control->init();
                        $control->enqueue();
                        $control->columns();

                        add_action( $taxonomy . '_add_form_fields', function () use ( $control ) {
                            $control->render();
                        }, 10, 2 );

                    }

                    error_log( $control_class . ' does not exists' );
                }

            }

        }

    }

    /**
     * Build control for edit form from controls directory
     *
     * @param [type] $term
     * @param [type] $taxonomy
     * @param [type] $controls
     * @return void
     */
    public function build_taxonomoy_edit_fields( $term, $taxonomy, $controls ) {

        foreach ( $controls as $control ) {

            if ( isset( $control['type'] ) ) {

                $control_type        = $control['type'];
                $original_class_name = $this->make_class_structure( $control_type );

                $control_class = 'Devmonsta\Options\Posts\Controls\\' .
                    $original_class_name . '\\' .
                    $original_class_name;

                if ( class_exists( $control_class ) ) {

                    $control_object = new $control_class( $control, $taxonomy );

                    $control_object->init();
                    $control_object->enqueue();

                    $control_object->edit_fields( $term, $taxonomy );

                } else {

                    $file = DM_CORE . 'options/posts/controls/' . $control['type'] . '/' . $control['type'] . '.php';
                    include_once $file;

                    if ( class_exists( $control_class ) ) {

                        $control = new $control_class( $control, $taxonomy );
                        $control->init();
                        $control->enqueue();
                        $control->columns();
                        $control->edit_fields( $term, $taxonomy );
                    }

                }

            }

        }

    }

    /**
     * Save taxonomy meta value
     *
     * @param [type] $term_id
     * @param [type] $tt_id
     * @return void
     */
    public function save_meta( $term_id, $tt_id ) {
        $taxonomy = get_option( 'dm_taxonomy' );
        $prefix   = 'devmonsta_';

        foreach ( $_POST as $key => $value ) {

            if ( strpos( $key, $prefix ) !== false ) {

                add_term_meta( $term_id, $key, $_POST[$key] );
            }

        }

    }

    /**
     * Update taxonomy meta value
     *
     * @param [type] $term_id
     * @param [type] $tt_id
     * @return void
     */
    public function update_meta( $term_id, $tt_id ) {

        $prefix = 'devmonsta_';

        foreach ( $_POST as $key => $value ) {

            if ( strpos( $key, $prefix ) !== false ) {

                update_term_meta( $term_id, $key, $_POST[$key] );
            }

        }

    }


    /**
     * Get controls array from theme and prepare them
     *
     * @param [type] $term
     * @param [type] $taxonomy
     * @return void
     */
    public function get_edit_controls( $term, $taxonomy ) {

        $taxonomy_file = get_template_directory() . '/devmonsta/options/taxonomies/' . $taxonomy . '.php';

        if ( file_exists( $taxonomy_file ) ) {

            require_once $taxonomy_file;
            $path = $taxonomy_file;
            $file = basename( $path );
            $file = basename( $path, ".php" );

            $taxonomy = $file;

            /**
             * Edit term meta
             */

            $class_name = $this->make_class_structure( $file );

            $taxonomy_lib = new LibsTaxonomies;

            if ( class_exists( $class_name ) ) {
                $taxonomy_class = new $class_name;

                if ( method_exists( $taxonomy_class, 'register_controls' ) ) {
                    $taxonomy_class->register_controls();
                }

                $controls = $taxonomy_lib->all_controls();

                $this->build_taxonomoy_edit_fields( $term, $taxonomy, $controls );

            }

        }

    }

    public function edit_meta( $term, $taxonomy ) {

        $this->get_edit_controls( $term, $taxonomy );

    }

}
