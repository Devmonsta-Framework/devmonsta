<?php

namespace Devmonsta\Options\Customizer;

use Devmonsta\Libs\Customizer as LibsCustomizer;

/**
 * Class Controls provide controls data functionality
 * It also provides the controls list as array
 *
 * @package Devmonsta\Options\Customizer
 */
class Controls extends Customizer {

    public function get_controls()
    {
        $customizer_file = $this->get_customizer_file();

        /**
         * Check if the customizer file exists
         */
        if (file_exists($customizer_file))
        {
            require_once $customizer_file;
            $childrens = [];

            /**
             *================================================
             * Fetch all the class extended to Customer class
             * @Devmonsta\Libs\Customizer
             *================================================
             */

            foreach (get_declared_classes() as $class)
            {
                if (is_subclass_of($class, 'Devmonsta\Libs\Customizer')) {

                    /** Store all control class to @var array $childrens */

                    $childrens[] = $class;
                }

            }

            $customizer = new LibsCustomizer;

            foreach ($childrens as $child_class)
            {
                $control = new $child_class;

                if (method_exists($control, 'register_controls')) {
                    $control->register_controls();
                }

            }
            return $customizer->all_controls();
        }
    }
}