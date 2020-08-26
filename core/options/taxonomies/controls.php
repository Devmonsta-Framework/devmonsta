<?php

namespace Devmonsta\Options\Taxonomies;

use Devmonsta\Libs\Taxonomies as LibsPosts;


class Controls
{
    public static function get_controls()
    {
        if (!empty(self::get_post_files())) {

            /** Include the file and stract the data  */
            $files = [];

            foreach (self::get_post_files() as $file) {
                require_once $file;
                $files[] = $file;
            }

            $post_file_class = [];

            foreach (get_declared_classes() as $class) {

                if (is_subclass_of($class, 'Devmonsta\Libs\Taxonomies')) {
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

            /**
             *  Get all controls defined in theme
             */
            $all_controls = $post_lib->all_controls();
            return $all_controls;
        }
    }

    private static function get_post_files()
    {
        $files = [];
        foreach (glob(get_template_directory() . '/devmonsta/options/taxonomies/*.php') as $post_files) {
            array_push($files, $post_files);
        }
        return $files;
    }
}