<?php

namespace Devmonsta\Options\Taxonomies;

use Devmonsta\Libs\Taxonomies as LibsTaxonomies;
use Devmonsta\Traits\Singleton;

class Taxonomies
{

    use Singleton;

    public function init()
    {
        global $pagenow;

        if ($pagenow == 'edit-tags.php') {

            $taxonomy_file = get_template_directory() . '/devmonsta/options/taxonomies/' . $_GET['taxonomy'] . '.php';

            if (file_exists($taxonomy_file)) {

                require_once $taxonomy_file;
                $path = $taxonomy_file;
                $file = basename($path);
                $file = basename($path, ".php");

                $taxonomy = $file;

                /**
                 * Save term meta
                 */

                add_action('created_' . $taxonomy, [$this, 'save_meta'], 10, 2);

                /**
                 * Edit term meta
                 */

                add_action($taxonomy . '_edit_form_fields', [$this, 'edit_meta'], 10, 2);

                $class_name = $this->make_class_structure($file);

                $taxonomy_lib = new LibsTaxonomies;
                if (class_exists($class_name)) {
                    $taxonomy_class = new $class_name;

                    if (method_exists($taxonomy_class, 'register_controls')) {
                        $taxonomy_class->register_controls();
                    }
                    $controls = $taxonomy_lib->all_controls();

                    error_log('Taxonomy : ' . $taxonomy . ' and data ' . serialize($controls));

                    $this->build_taxonomoy($taxonomy, $controls);

                }
            }

        }

    }

    public function make_class_structure($file)
    {
        $class_name = explode('-', $file);
        $class_name = array_map('ucfirst', $class_name);
        $class_name = implode('', $class_name);

        return $class_name;
    }

    public function build_taxonomoy($taxonomy, $controls)
    {

        foreach ($controls as $control) {

            if (isset($control['type'])) {

                $control_type = $control['type'];
                $original_class_name = $this->make_class_structure($control_type);

                $control_class = 'Devmonsta\Options\Taxonomies\Controls\\' .
                    $original_class_name . '\\' .
                    $original_class_name;

                if (class_exists($control_class)) {

                    $control_object = new $control_class($taxonomy, $control);

                    $control_object->init();
                    $control_object->enqueue();

                    add_action($taxonomy . '_add_form_fields', function () use ($control_object) {
                        $control_object->render();
                    }, 10, 2);

                } else {

                    $file = plugin_dir_path(__FILE__) . 'controls/' . $control['type'] . '/' . $control['type'] . '.php';
                    include_once $file;

                    if (class_exists($control_class)) {

                        $control = new $control_class($taxonomy, $control);
                        $control->init();
                        $control->enqueue();

                        add_action($taxonomy . '_add_form_fields', function () use ($control) {
                            $control->render();
                        }, 10, 2);

                    }

                    error_log($control_class . ' does not exists');
                }
            }

        }

    }

    public function save_meta($term_id, $tt_id)
    {
        $prefix = 'devmonsta_';
        add_term_meta($term_id, 'devmonsta_test_key', 'devmonsta_test_value');
        foreach ($_POST as $key => $value) {

            if (strpos($key, $prefix) !== false) {

                add_term_meta($term_id, $key, $_POST[$key]);
            }

        }

    }

    public function edit_meta($term, $taxonomy)
    {
        global $feature_groups;
        echo 'ok';
        // get current group
        // $feature_group = get_term_meta($term->term_id, 'feature-group', true);
        print_r($feature_groups);

    }

}
