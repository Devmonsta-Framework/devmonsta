<?php

namespace Devmonsta\Options\Customizer;

use Devmonsta\Libs\Customizer as LibsCustomizer;
use Devmonsta\Options\Customizer\Controls\Color;
use Devmonsta\Options\Customizer\Controls\Panel;
use Devmonsta\Options\Customizer\Controls\Section;
use Devmonsta\Traits\Singleton;

class Customizer
{

    use Singleton;

    public function init()
    {

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
            foreach (get_declared_classes() as $class) {
                if (is_subclass_of($class, 'Devmonsta\Libs\Customizer')) {
                    $childrens[] = $class;
                }

            }

            $customizer = new LibsCustomizer;

            foreach ($childrens as $child_class) {

                $control = new $child_class;
                $control->register_controls();

            }

            /**
             * Get all controls defined in the theme
             */

            $all_controls = $customizer->all_controls();

            /**
             * Get all sections defined in the theme
             */

            $all_sections = $customizer->all_sections();

            /**
             * Get all panels defined in the theme
             */

            $all_panels = $customizer->all_panels();

            /**
             * Set all controls defined in the theme
             */

            foreach ($all_controls as $ctrl) {

                $args = $ctrl;

                if ($ctrl['type'] == 'color') {

                    Color::instance()->add_color($args);

                }

                if ($ctrl['section'] == 'text') {

                    Section::instance()->add_section($args);

                }

            }

            /**
             * Set all section defined in the theme
             * @since 1.0.1
             */
         
            foreach ($all_panels as $panel) {
                Panel::instance()->add_panel($panel);
            }

        }
    }

    public function get_customizer_file()
    {
        return get_template_directory() . '/devmonsta/options/customizer.php';
    }

}
