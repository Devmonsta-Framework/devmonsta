<?php

namespace Devmonsta\Options\Taxonomies;

use Devmonsta\Libs\Taxonomies as LibsTaxonomies;
use Devmonsta\Traits\Singleton;

class Taxonomies
{
    use Singleton;
    protected $meta_owner = "taxonomy";
    protected $controls = null;

    /**
     * Entry point of taxonomy control
     *
     * @return void
     */

    public function init()
    {

        $t = get_option('devm_taxonomy'); // $t for taxonomy name

        add_action('created_' . $t, [$this, 'save_meta'], 10, 2);
        add_action($t . '_edit_form_fields', [$this, 'edit_meta'], 10, 2);
        add_action('edited_' . $t, [$this, 'update_meta'], 10, 2);

        if (!$this->check_requirements()) {
            return;
        }

        add_action('admin_init', [$this, 'load_scripts']);

        /**
         * Add css class to the admin body for better views of controls
         */

        add_filter('admin_body_class', [$this, 'add_body_classes']);

        if (isset($_GET['taxonomy'])) {

            $taxonomy_file = get_template_directory() .
                '/devmonsta/options/taxonomies/' .
                sanitize_text_field($_GET['taxonomy']) . '.php';

            if (file_exists($taxonomy_file)) {

                require_once $taxonomy_file;
                $path = $taxonomy_file;
                $file = basename($path);
                $file = basename($path, ".php");

                $taxonomy = $file;

                /**
                 * Save term meta
                 */

                update_option('devm_taxonomy', $taxonomy);

                /**
                 * Edit term meta
                 */

                $class_name = $this->make_class_structure($file);

                $taxonomy_lib = new LibsTaxonomies;

                if (class_exists($class_name)) {
                    $taxonomy_class = new $class_name;

                    if (method_exists($taxonomy_class, 'register_controls')) {
                        $taxonomy_class->register_controls();
                    }

                    $controls = $taxonomy_lib->all_controls();

                    $this->controls = $controls;


                    $this->build_taxonomoy($taxonomy, $controls);
                }
            }
        }
    }

    public function check_requirements()
    {
        global $pagenow;
        if ($pagenow == 'edit-tags.php' || $pagenow == 'term.php') {

            return true;
        }

        return false;
    }

    /**
     * =======================================================
     * Added CSS class name .devm-taxonomy-wrapper to the body
     * So that controls insdie taxonomy markup can be stylable
     *
     * @return  string
     * =======================================================
     */

    public function add_body_classes($classes)
    {
        global $pagenow;

        $classes = 'devm-taxonomy-wrapper ';

        if ($pagenow == 'term.php')
            $classes .= 'devm-taxonomy-edit-wrapper';

        return $classes;
    }

    /**
     * ==================================================
     * Generate full class name from control type field
     *
     * @param [type] $file
     * @return void
     * ===================================================
     */

    public function make_class_structure($file)
    {
        $class_name = explode('-', $file);
        $class_name = array_map('ucfirst', $class_name);
        $class_name = implode('', $class_name);

        return $class_name;
    }

    /**
     * ====================================================
     * Buils controls dynamically from controls directory
     *
     * @param [type] $taxonomy
     * @param [type] $controls
     * @return void
     * ====================================================
     */
    public function build_taxonomoy($taxonomy, $controls)
    {

        foreach ($controls as $control) {

            if (isset($control['type'])) {

                $control_type = $control['type'];
                $original_class_name = $this->make_class_structure($control_type);

                $control_class = 'Devmonsta\Options\Posts\Controls\\' .
                    $original_class_name . '\\' .
                    $original_class_name;

                if (class_exists($control_class)) {

                    $this->build_taxonomy_content(
                        $control,
                        $control_class,
                        $taxonomy
                    );
                } else {

                    $file = DEVMONSTA_DIR .
                        '/core/options/posts/controls/' .
                        $control['type'] . '/' .
                        $control['type'] . '.php';

                    if (file_exists($file)) {
                        include_once $file;

                        if (class_exists($control_class)) {

                            $this->build_taxonomy_content(
                                $control,
                                $control_class,
                                $taxonomy
                            );
                        }
                    }
                }
            }
        }
    }

    protected function build_taxonomy_content($control, $control_class, $taxonomy)
    {
        $control = new $control_class($control, $taxonomy);
        $control->init();
        $control->enqueue($this->meta_owner);
        $control->columns();

        add_action($taxonomy . '_add_form_fields', function () use ($control) {
            $control->render();
        }, 10, 2);
    }

    /**
     * ======================================================
     * Build control for edit form from controls directory
     *
     * @param [type] $term
     * @param [type] $taxonomy
     * @param [type] $controls
     * @return void
     * =======================================================
     */
    public function build_taxonomoy_edit_fields($term, $taxonomy, $controls)
    {

        foreach ($controls as $control) {

            if (isset($control['type'])) {

                $control_type = $control['type'];
                $original_class_name = $this->make_class_structure($control_type);

                $control_class = 'Devmonsta\Options\Posts\Controls\\' .
                    $original_class_name . '\\' .
                    $original_class_name;

                if (class_exists($control_class)) {

                    $meta_owner = "taxonomy";
                    $control_object = new $control_class($control, $taxonomy);
                    $control_object->init();
                    $control_object->enqueue($this->meta_owner);
                    $control_object->edit_fields($term, $taxonomy);
                } else {

                    $file = DEVMONSTA_DIR . '/core/options/posts/controls/' . $control['type'] . '/' . $control['type'] . '.php';

                    if (file_exists($file)) {

                        include_once $file;

                        if (class_exists($control_class)) {
                            $control = new $control_class($control, $taxonomy);
                            $control->init();
                            $control->enqueue($this->meta_owner);
                            $control->columns();
                            $control->edit_fields($term, $taxonomy);
                        }
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
    public function save_meta($term_id, $tt_id)
    {
        $prefix = 'devmonsta_';

        include('controls.php');

        $controls_data = Controls::get_controls();
        update_option('devmonsta_all_taxonomy_controls', $controls_data);

        foreach ($_POST as $key => $value) {
            if (strpos($key, $prefix) !== false) {
                add_term_meta($term_id, $key, devm_sanitize_taxonomy_data($key, $value));
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
    public function update_meta($term_id, $tt_id)
    {

        $prefix = 'devmonsta_';
        include('controls.php');
        $controls_data = Controls::get_controls();
        update_option('devmonsta_all_taxonomy_controls', $controls_data);

        foreach ($_POST as $key => $value) {

            if (strpos($key, $prefix) !== false) {

                update_term_meta($term_id, $key, devm_sanitize_taxonomy_data($key, $value));
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
    public function get_edit_controls($term, $taxonomy)
    {

        $taxonomy_file = get_template_directory() . '/devmonsta/options/taxonomies/' . $taxonomy . '.php';

        if (file_exists($taxonomy_file)) {

            require_once $taxonomy_file;
            $path = $taxonomy_file;
            $file = basename($path);
            $file = basename($path, ".php");

            $taxonomy = $file;

            /**
             * Edit term meta
             */

            if ($this->controls == null) {
                $class_name = $this->make_class_structure($file);

                $taxonomy_lib = new LibsTaxonomies();

                if (class_exists($class_name)) {
                    $taxonomy_class = new $class_name;

                    if (method_exists($taxonomy_class, 'register_controls')) {
                        $taxonomy_class->register_controls();
                    }

                    $controls = $taxonomy_lib->all_controls();


                    $this->build_taxonomoy_edit_fields($term, $taxonomy, $controls);
                }
            }

            $this->build_taxonomoy_edit_fields($term, $taxonomy, $this->controls);
        }
    }

    public function edit_meta($term, $taxonomy)
    {

        $this->get_edit_controls($term, $taxonomy);
    }

    /**
     * ===========================================
     *      Load Styles & Scripts for controls
     * ===========================================
     */
    public function load_scripts()
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        $colorpicker_l10n = array(
            'clear'            => __('Clear'),
            'clearAriaLabel'   => __('Clear color'),
            'defaultString'    => __('Default'),
            'defaultAriaLabel' => __('Select default color'),
            'pick'             => __('Select Color'),
            'defaultLabel'     => __('Color value'),
        );
        wp_localize_script('wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n);
        wp_enqueue_style('devmonsta-taxonomy-style', DEVMONSTA_PATH . 'core/options/taxonomies/libs/assets/css/style.css');
        wp_enqueue_style('dm-main-style', DEVMONSTA_PATH . 'core/options/assets/css/main.css');
        wp_enqueue_script('vue-js', DEVMONSTA_PATH . 'core/options/posts/assets/js/vue.min.js', [], null, false);
        wp_enqueue_script('dm-color-picker', DEVMONSTA_PATH . 'core/options/posts/assets/js/script.js', [], null, true);
        wp_enqueue_script('jquery-deparam', plugin_dir_url(__FILE__) . '/libs/assets/js/jquery-deparam.js', ['jquery'], null, true);
        // wp_enqueue_script('devmonsta-conditions', DEVMONSTA_PATH . 'core/options/posts/assets/js/conditions.js', [], null, true);
        wp_enqueue_script('devmonsta-taxonomy-script', plugin_dir_url(__FILE__) . '/libs/assets/js/script.js', ['jquery'], null, true);
        wp_enqueue_script('dm-vendor-js', DEVMONSTA_PATH . 'core/options/assets/js/dm-vendor-scripts.bundle.js', ['jquery'], null, true);
        wp_enqueue_script('dm-init-js', DEVMONSTA_PATH . 'core/options/assets/js/dm-init-scripts.bundle.js', ['jquery'], null, true);
    }
}
