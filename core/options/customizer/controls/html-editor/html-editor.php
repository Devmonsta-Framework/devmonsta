<?php
namespace Devmonsta\Options\Customizer\Controls\HtmlEditor;

use Devmonsta\Options\Customizer\Structure;

class HtmlEditor extends Structure
{

    public $type = 'html-editor';

    public function render_content()
    {
        ?>

        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php

        $settings = array(
            
            'media_buttons' => true,
            'drag_drop_upload' => false,
            'teeny' => true,
            'quicktags' => true,
            'textarea_rows' => 5,
        );

        $this->filter_editor_setting_link();

        wp_editor(
            $this->value(),
            $this->id,
            $settings
        );

        ?>

        </label>

        <?php

        do_action('admin_footer');
        do_action('admin_print_footer_scripts');

    }

    public function enqueue()
    {

        wp_enqueue_script('html_editor_control_js', plugin_dir_url(__FILE__) . 'js/html-editor.js', array('jquery', 'jquery-ui-core'), rand(), true);
        add_action('customize_controls_print_styles', array($this, 'print_styles'));

    }

    private function filter_editor_setting_link()
    {
        add_filter('the_editor', function ($output) {return preg_replace('/<textarea/', '<textarea ' . $this->get_link(), $output, 1);});
    }

    public function print_styles()
    {?>

        <style type="text/css" id="acid-toggle-css">

        </style>

    <?php
}

}
