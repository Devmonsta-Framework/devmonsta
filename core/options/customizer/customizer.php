<?php

namespace Devmonsta\Options\Customizer;

use Devmonsta;
use Devmonsta\Libs\Customizer as LibsCustomizer;
use Devmonsta\Options\Customizer\Panel;
use Devmonsta\Traits\Singleton;

class Customizer
{

    use Singleton;

    /**
     * ==========================================
     *
     * Initial method of the customizer
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     * ==========================================
     */
    public function init()
    {

        $this->repeater();
        /**
         * Get Customizer file from the
         * current active theme
         */

        $customizer_file = $this->get_customizer_file();

        /**
         * Check if the customizer file exists
         */

        if (file_exists($customizer_file)) {

            require_once $customizer_file;

            $childrens = array();

            /**
             *================================================
             * Fetch all the class extended to Customer class
             * @Devmonsta\Libs\Customizer
             *================================================
             */
            foreach (get_declared_classes() as $class) {
                if (is_subclass_of($class, 'Devmonsta\Libs\Customizer')) {

                    /** Store all control class to @var array $childrens */

                    $childrens[] = $class;
                }

            }

            $customizer = new LibsCustomizer;

            foreach ($childrens as $child_class) {

                $control = new $child_class;

                if (method_exists($control, 'register_controls')) {
                    $control->register_controls();
                }

            }

            /**
             * Get all panels defined in the theme
             */

            $all_panels = $customizer->all_panels();

            /**
             * Get all sections defined in the theme
             */

            $all_sections = $customizer->all_sections();

            /**
             * Get all controls defined in the theme
             */

            $all_controls = $customizer->all_controls();

            /**
             * Get all settings for the customizer defined in theme
             */

            $all_settings = $customizer->all_settings();

            /**
             * Build the panel , sections and controls
             */

            $this->build_panels($all_panels);
            $this->build_sections($all_sections);
            $this->build_controls($all_controls);

            /**
             * Set all controls defined in the theme
             */

            // foreach ($all_controls as $args) {

            //     $this->build_controls($args);

            // }

        }
    }

    /**
     * ======================================
     * Get the active theme location
     * and the customzer file of the theme
     *
     * @access  public
     * @return  string
     * ======================================
     */

    public function get_customizer_file()
    {
        /**
         * Return the customizer file
         *
         * @link https://developer.wordpress.org/reference/functions/get_template_directory
         */
        return get_template_directory() . '/devmonsta/options/customizer.php';
    }

    /**
     *=================================
     * Build options for customizer
     * @access  public
     * @return  void
     *=================================
     */

    public function build_controls($args)
    {

        /**
         * =====================================================
         *      Check if the @type of control is set or not
         *      Create a dynamic object of class and add the
         *      data to control
         * =====================================================
         */
        if (!empty($args)) {
            foreach ($args as $control) {
                if (isset($control['type'])) {
                    $type = $control['type'];
                    $control_class = 'Devmonsta\Options\Customizer\Controls\\' . $type . '\\' . $type;
                    if (class_exists($control_class)) {
                        $control_class::instance()->add_control($control);
                    }

                }
            }
        }

    }

    /**
     * ==================================
     * Build Panels for customizer
     *
     * @access  public
     * @return  void
     * ==================================
     */
    public function build_panels($panels)
    {
        if (!empty($panels)) {
            foreach ($panels as $panel) {
                add_action('customize_register', function ($wp_customize) use ($panel) {
                    $panel_id = $panel['id'];
                    unset($panel['id']);
                    $wp_customize->add_panel($panel_id, $panel);
                });
            }
        }

    }

    /**
     * ========================
     * Build sections in panel
     *
     * @access  public
     * @return  void
     * ========================
     */
    public function build_sections($sections)
    {
        if (!empty($sections)) {
            foreach ($sections as $section) {

                add_action('customize_register', function ($wp_customize) use ($section) {
                    $section_id = $section['id'];
                    unset($section['id']);
                    $wp_customize->add_section($section_id, $section);
                });

            }
        }

    }

    /**
     * ======================================
     * Repeater functionality for customizer
     *
     * @access  public
     * @return  void
     * ======================================
     */

    public function repeater()
    {
        add_action('customize_register', [$this, 'customizer_repeater_register']);

    }

    public function customizer_repeater_register($wp_customize)
    {

        require_once 'repeater.php';

        $wp_customize->add_setting('customizer_repeater_example', array(
            'sanitize_callback' => 'customizer_repeater_sanitize',
        ));
        $wp_customize->add_control(new Repeater($wp_customize, 'customizer_repeater_example', array(
            'label' => esc_html__('Movie info', 'customizer-repeater'),
            'section' => 'devmonsta_text_settings_section',
            'priority' => 1,
            'item_name' => 'movie',
            'customizer_repeater_title_control' => true,
            'customizer_repeater_subtitle_control' => true,

        )));

    }

}
