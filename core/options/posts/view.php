<?php

namespace Devmonsta\Options\Posts;

use Devmonsta\Traits\Singleton;

class View
{

    use Singleton;

    protected $meta_owner = "post";

    /**
     * Build the metbox for the post
     *
     * @access      public
     * @return      void
     */

    public function build($box_id, $controls)
    {

        echo '<div class="dm-box">'; // This html for wrapper purpose

        foreach ($controls as $control) {

            if (Validator::instance()->check($control)) {

                if ($control['box_id'] == $box_id) {
                    $this->render($control);
                }
            }
        }

        echo '</div>';
    }

    /**
     * Render markup view for the control
     * defined in teme. It will pass the data according to the
     * control type
     *
     * @access      public
     * @return      void
     */
    public function render($control_content)
    {

        if (isset($control_content['type'])) {

            if ($control_content['type'] == 'repeater') {

                $this->build_repeater($control_content);
            }

            $this->build_controls($control_content);
        }
    }

    /**
     * Build controls markup
     *
     * @access  public
     * @return  void
     */

    public function build_controls($control_content)
    {
        $class_name = explode('-', $control_content['type']);
        $class_name = array_map('ucfirst', $class_name);
        $class_name = implode('', $class_name);
        $control_class = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

        if (class_exists($control_class)) {

            $control = new $control_class($control_content);
            $control->init();
            $control->enqueue($this->meta_owner);
            $control->render();
        } else {

            $file = plugin_dir_path(__FILE__) . 'controls/' . $control_content['type'] . '/' . $control_content['type'] . '.php';

            if (file_exists($file)) {

                include_once $file;

                if (class_exists($control_class)) {

                    $control = new $control_class($control_content);
                    $control->init();
                    $control->enqueue($this->meta_owner);
                    $control->render();
                }
            }
        }
    }

    /**
     * Build repeater controls
     *
     * @access  public
     * @return  void
     */

    public function build_repeater($control_data)
    {
        /**
         * Incomplete code , just testing , do not read or use
         */
        if (isset($control_data['controls'])) {

            add_thickbox();
            ?>
            <div class="dm-option form-field ">

                <div class='dm-option-column left'>
                    <label class="dm-option-label"><?php echo $control_data['label']; ?> </label>
                </div>


                <div class='dm-option-column right'>


                    <div class="dm-repeater-control">
                        <a href="#TB_inline?width=600&height=550&inlineId=<?php echo $control_data['name']; ?>" class="thickbox">Control</a>
                        <button type="button" data-id="<?php echo $control_data['name']; ?>" class="components-button editor-post-trash is-link">Delete</button>

                        <div id="<?php echo $control_data['name']; ?>" style="display:none;">
                            <?php $this->repeater_controls($control_data); ?>
                        </div>
                    </div>



                    <br><br><a href='#TB_inline?width=600&height=550&inlineId=<?php echo $control_data['name']; ?>' data-id='<?php echo $control_data['name']; ?>' class='dm-repeater-add-new button thickbox'><?php echo $control_data['add_new']; ?></a>
                </div>
            </div>

        <?php
        }
    }

    public function repeater_controls($control_data)
    {
        // incomplete code , just testing 

        foreach ($control_data['controls'] as $control_content) {



            $name = $control_content['name'];
            unset($control_content['name']);
            $control_content['name'] = $name . '[]';
            $class_name = explode('-', $control_content['type']);
            $class_name = array_map('ucfirst', $class_name);
            $class_name = implode('', $class_name);
            $control_class = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

            if (class_exists($control_class)) {

                $control = new $control_class($control_content);
                $control->init();
                $control->enqueue($this->meta_owner);
                $control->render();
            } else {

                $file = plugin_dir_path(__FILE__) . 'controls/' . $control_content['type'] . '/' . $control_content['type'] . '.php';

                if (file_exists($file)) {

                    include_once $file;

                    if (class_exists($control_class)) {

                        $control = new $control_class($control_content);
                        $control->init();
                        $control->enqueue($this->meta_owner);
                        $control->render();
                    }
                }
            }
        }
    }
}
