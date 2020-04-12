<?php

/**
 * =================================
 *       Handel page , post and
 *      custom post type metabox
 * =================================
 */

namespace Devmonsta\Options\Posts;

use Devmonsta\Libs\Posts as LibsPosts;
use Devmonsta\Traits\Singleton;

class Posts
{

    use Singleton;

    protected $data;

    public function init()
    {

        /** Check if post files exists in active theme directory */

        if (!empty($this->get_post_files())) {

            /** Include the file and stract the data  */
            $files = [];
            foreach ($this->get_post_files() as $file) {

                require_once $file;
                $files[] = $file;
                /** Get the class name which is extended to @Devmonsta\Libs\Posts */
            }

            $post_file_class = [];

            foreach (get_declared_classes() as $class) {
                if (is_subclass_of($class, 'Devmonsta\Libs\Posts')) {
                    $post_file_class[] = $class;
                }
            }

            /** Get all the properties defined in post file */

            $post_lib = new LibsPosts;

            foreach ($post_file_class as $child_class) {

                $post_file = new $child_class;

                if (method_exists($post_file, 'register_controls')) {

                    $post_file->register_controls();

                }

            }

            /** Get all the metaboxed that has been defined */

            $all_meta_box = $post_lib->all_boxes();

            /**
             *  Get Post type anem from the file name
             */

            foreach ($files as $file_name) {
                $post_type = basename($file_name, ".php");

                /** Create metabox */

                foreach ($all_meta_box as $args) {

                    if ($post_type == $args['type']) {
                        $this->data = $args;
                        $this->add($post_type, $args);
                    }

                }

            }

        }
        /** include post file from theme */

        // add_action('add_meta_boxes', [$this, 'add']);
        // add_action('save_post', [$this, 'save']);
    }

    public function get_post_files()
    {
        $files = [];
        foreach (glob(get_template_directory() . '/devmonsta/options/posts/*.php') as $post_files) {
            array_push($files, $post_files);
        }

        return $files;
    }

    /** Add Metabox to the post */

    public function add($post_type, $args)
    {

        add_action('add_meta_boxes', function () use ($post_type, $args) {
            /**
             * Create a random id for metabox
             * So that user don't need to add a extra properties
             */
            $random_id = rand(1000, 9999);
            add_meta_box(
                $random_id, // Unique ID / metabox ID
                $args['title'], // Box title
                function () use ($args) {

                    if (isset($args['contents'])) {

                        View::instance()->build($args['contents']);

                    }

                }, // Content callback, must be of type callable
                $post_type // Post type
            );
        });

    }

    /** Save metbox data */

    public function save($post_id)
    {
        if (array_key_exists('wporg_field', $_POST)) {
            update_post_meta(
                $post_id,
                '_wporg_meta_key',
                $_POST['wporg_field']
            );
        }
    }

    public function box_content($post)
    {
        $options = $this->data;

        print_r($options);
    }

}
