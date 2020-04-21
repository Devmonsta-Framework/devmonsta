<?php

namespace Devmonsta\Options\Taxonomies;

use Devmonsta\Libs\Taxonomies as LibsTaxonomies;
use Devmonsta\Traits\Singleton;

class Taxonomies
{

    use Singleton;

    public function init()
    {
        $files = $this->get_taxonomy_files();

        if (!empty($files)) {

            foreach ($files as $taxonomy_file) {

                require_once $taxonomy_file;
                $path = $taxonomy_file;
                $file = basename($path);
                $file = basename($path, ".php");

                $class_name = explode('-', $file);
                $class_name = array_map('ucfirst', $class_name);
                $class_name = implode('', $class_name);

                $taxonomy_lib = new LibsTaxonomies;

                $taxonomy_class = new $class_name;

                if(method_exists($taxonomy_class,'register_controls')){
                    $taxonomy_class->register_controls();
                }

                $texonomy_of_the_class = $taxonomy_lib->all_taxonomies();
                error_log(serialize($texonomy_of_the_class));

            }

        }

        add_action('init', [$this, 'wporg_register_taxonomy_course']);
    }

    public function get_taxonomy_files()
    {
        $files = [];

        foreach (glob(get_template_directory() . '/devmonsta/options/taxonomies/*.php') as $taxonomies_files) {
            array_push($files, $taxonomies_files);
        }

        return $files;
    }

    public function wporg_register_taxonomy_course()
    {
        $labels = [
            'name' => _x('Courses', 'taxonomy general name'),
            'singular_name' => _x('Course', 'taxonomy singular name'),
            'search_items' => __('Search Courses'),
            'all_items' => __('All Courses'),
            'parent_item' => __('Parent Course'),
            'parent_item_colon' => __('Parent Course:'),
            'edit_item' => __('Edit Course'),
            'update_item' => __('Update Course'),
            'add_new_item' => __('Add New Course'),
            'new_item_name' => __('New Course Name'),
            'menu_name' => __('Course'),
        ];
        $args = [
            'hierarchical' => true, // make it hierarchical (like categories)
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'course'],
        ];
        register_taxonomy('course', ['post', 'page'], $args);
    }
}
