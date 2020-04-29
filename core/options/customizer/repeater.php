<?php
namespace Devmonsta\Options\Customizer;

if (!class_exists('WP_Customize_Control')) {
    return null;
}

class Repeater extends \WP_Customize_Control
{

    public $id;
    private $boxtitle = array();
    private $add_field_label = array();

    private $allowed_html = array();

    public $customizer_repeater_title_control = false;
    public $customizer_repeater_subtitle_control = false;

    /*Class constructor*/
    public function __construct($manager, $id, $args = array())
    {
        parent::__construct($manager, $id, $args);
        /*Get options from customizer.php*/
        $this->add_field_label = esc_html__('Add new field', 'your-textdomain');
        if (!empty($args['add_field_label'])) {
            $this->add_field_label = $args['add_field_label'];
        }

        $this->boxtitle = esc_html__('Customizer Repeater', 'your-textdomain');
        if (!empty($args['item_name'])) {
            $this->boxtitle = $args['item_name'];
        } elseif (!empty($this->label)) {
            $this->boxtitle = $this->label;
        }

        // if (!empty($args['customizer_repeater_title_control'])) {
        //     $this->customizer_repeater_title_control = $args['customizer_repeater_title_control'];
        // }

        // if (!empty($args['customizer_repeater_subtitle_control'])) {
        //     $this->customizer_repeater_subtitle_control = $args['customizer_repeater_subtitle_control'];
        // }

        if (!empty($id)) {
            $this->id = $id;
        }

        $allowed_array1 = wp_kses_allowed_html('post');
        $allowed_array2 = array(
            'input' => array(
                'type' => array(),
                'class' => array(),
                'placeholder' => array(),
            ),
        );

        $this->allowed_html = array_merge($allowed_array1, $allowed_array2);
    }

    /*Enqueue resources for the control*/
    public function enqueue()
    {
        wp_enqueue_style('customizer-repeater-admin-stylesheet', plugin_dir_url(__FILE__) . '/assets/css/admin-style.css', array(), null);

        wp_enqueue_script('customizer-repeater-script', plugin_dir_url(__FILE__) . '/assets/js/customizer_repeater.js', array('jquery', 'jquery-ui-draggable', 'wp-color-picker'), null, true);

    }

    public function render_content()
    {

        /*Get default options*/
        $this_default = json_decode($this->setting->default);

        /*Get values (json format)*/
        $values = $this->value();

        error_log(serialize($this_default));
        /*Decode values*/
        $json = json_decode($values);

        if (!is_array($json)) {
            $json = array($values);
        }?>

        <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
        <div class="customizer-repeater-general-control-repeater customizer-repeater-general-control-droppable">
			<?php
        if ((count($json) == 1 && '' === $json[0]) || empty($json)) {

            if (!empty($this_default)) {


                $this->iterate_array($this_default);
                
                ?>
					<input type="hidden"
						id="customizer-repeater-<?php echo esc_attr($this->id); ?>-colector" <?php esc_attr($this->link());?>
						class="customizer-repeater-colector"
						value="<?php echo esc_textarea(json_encode($this_default)); ?>"/>
				<?php

            } else {

                $this->iterate_array();
                
                ?>
						<input type="hidden"
							id="customizer-repeater-<?php echo esc_attr($this->id); ?>-colector" <?php esc_attr($this->link());?>
							class="customizer-repeater-colector"/>
        <?php
        
            }
            
            } else {

            $this->iterate_array($json);
            
            ?>
								<input type="hidden" id="customizer-repeater-<?php echo esc_attr($this->id); ?>-colector" <?php esc_attr($this->link());?>
									class="customizer-repeater-colector" value="<?php echo esc_textarea($this->value()); ?>"/>
            
            <?php
            }
            
            ?>

        </div>
        <button type="button" class="button add_field customizer-repeater-new-field">
			<?php echo esc_html($this->add_field_label); ?>
        </button>
		<?php
    }

    private function iterate_array($array = array())
    {
        /*Counter that helps checking if the box is first and should have the delete button disabled*/
        $count = 0;

        if (!empty($array)) {
            foreach ($array as $data) {?>
                <div class="customizer-repeater-general-control-repeater-container customizer-repeater-draggable">
                    <div class="customizer-repeater-customize-control-title">
					<!-- box title -->
						<?php echo esc_html($this->boxtitle) ?>
                    </div>
                    <div class="customizer-repeater-box-content-hidden">
						<?php
                    $title = '';

                if (!empty($data->title)) {
                    $title = $data->title;
                }
               

                $this->input_control(array(
                    'label' => apply_filters('repeater_input_labels_filter', esc_html__('Title', 'your-textdomain'), $this->id, 'customizer_repeater_title_control'),
                    'class' => 'customizer-repeater-title-control',
                    'type' => apply_filters('customizer_repeater_input_types_filter', '', $this->id, 'customizer_repeater_title_control'),
                ), $title);

                

                ?>

                 <input type="hidden" class="social-repeater-box-id" value="<?php if (!empty($id)) {
                    echo esc_attr($id);
                }?>">
                        <button type="button" class="social-repeater-general-control-remove-field" <?php if ($count == 0) {
                    echo 'style="display:none;"';
                }?>>
							<?php esc_html_e('Delete field', 'your-textdomain');?>
                        </button>

                    </div>
                </div>

				<?php

                $count++;

            }

        } else {

            ?>
            <div class="customizer-repeater-general-control-repeater-container">
                <div class="customizer-repeater-customize-control-title">

                </div>
                <div class="customizer-repeater-box-content-hidden">
					<?php

            $this->input_control(array(
                'label' => esc_html__('Title', 'your-textdomain'),
                'class' => 'customizer-repeater-title-control',
                
            ));

            ?>


                </div>
            </div>
			<?php
        }
    }

    private function input_control($options, $value = '')
    {
    ?>

						<span class="customize-control-title"><?php echo esc_html($options['label']); ?></span>
						<input type="text" value="<?php echo (!empty($options['sanitize_callback']) ? call_user_func_array($options['sanitize_callback'], array($value)) : esc_attr($value)); ?>" class="<?php echo esc_attr($options['class']); ?>" placeholder="<?php echo esc_attr($options['label']); ?>"/>
	<?php

    }

}
