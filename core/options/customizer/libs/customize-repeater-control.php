<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Abort if Customizer API not available
if (!class_exists('WP_Customize_Control')) {
    return;
}

class DEVM_Theme_Customize_Repeater_Control extends WP_Customize_Control
{
    /*
     ** Field that is used as the repeater label
     */
    protected $labelField = '';

    /*
     ** Storage for repeater fields
     */
    protected $fields = array();

    /*
     ** Storage for settings argument
     */
    protected $_settings;

    /**
     * Args passed by settings
     */

    public $args;

    protected $title_field;

    /*
     ** Constructor
     */
    public function __construct($manager, $id, $args = array())
    {

        parent::__construct($manager, $id, $args);

        if (isset($args['add_button_text'])) {

            update_option('devm_add_button_text', $args['add_button_text']);
        }

        $this->_settings = isset($args['settings']) ? $args['settings'] : $this->id;

        if (is_array($this->fields) && !empty($this->fields)) {
            $this->prepareFields($this->fields);
        }



        // Force control type to 'repeater'
        $this->type = 'repeater';

        $title_field = '';

        if(isset($args['title_field'])){
            $title_field = $args['title_field'];
        }

        $this->title_field = $title_field;
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue()
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('customize-repeater-control', plugin_dir_url(__FILE__) . '/assets/js/customize-repeater-control.js', array('jquery', 'customize-controls'), false, true);
    }

    /*
     ** Refresh the parameters passed to the JavaScript via JSON
     */
    public function to_json()
    {
        parent::to_json();

        $value = json_decode($this->value(), true);
        if (!$value || !is_array($value)) {
            $value = array();
        }

        $fields = $this->fields;

        foreach ($fields as $key => &$field) {
            $Control = new $field['control']($this->manager, $this->_settings, $field['args']);

            $field['control'] = str_replace(array('WP_Customize', '_'), '', $field['control']);

            $field['args'] = $Control->json();

            if ('image' === $Control->type) {
                $field['attachments'] = array();

                // Store all attachments
                foreach ($value as $row) {
                    if (!isset($row[$key]) || empty($row[$key])) {
                        $field['attachments'][] = null;
                        continue;
                    }

                    $attachmentID = $row[$key];
                    if (!is_numeric($attachmentID)) {
                        $attachmentID = attachment_url_to_postid($attachmentID);
                    }

                    $field['attachments'][] = wp_prepare_attachment_for_js($attachmentID);
                }
            }
        }

        $this->json['fields'] = $fields;
        $this->json['labelField'] = $this->labelField;
        $this->json['developer'] = 'Xpeedstudio';
        $this->json['title_field'] = $this->title_field;

    }

    /*
     ** Prepare fields and add them to local storage
     */
    protected function prepareFields($fields)
    {
        // Reset fields
        $this->fields = array();

        foreach ($fields as $field) {
            $field = $this->normalizeField($field);

            if (class_exists($field['control'])) {
                // Force section id
                $field['args']['section'] = $this->section;

                $this->fields[$field['key']] = $field;
            }
        }
    }

    /*
     ** Normalize field arguments
     */
    protected function normalizeField($args)
    {
        $defaults = array(
            'key' => '',
            'control' => 'WP_Customize_Control',
            'args' => array(),
        );

        $args = wp_parse_args($args, $defaults);

        return $args;
    }

    /*
     ** Don't render the control's content - it uses a JS template instead.
     */
    protected function render_content()
    {}

    /*
     ** An Underscore (JS) template for this control's content (but not its container)
     */
    public function content_template()
    {
        ?>

		<# if (data.label) { #>
			<span class="customize-control-title">{{{ data.label }}}</span>
		<# } #>
		<# if (data.description) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<div class="customize-control-content">
			<div class="customize-control-repeater-fields">
				<div class="customize-control-repeater-field menu-item menu-item-edit-inactive prototype">
					<div class="menu-item-bar">
						<div class="customize-control-repeater-field-handle menu-item-handle">
							<span class="item-title" aria-hidden="true">
                                <span class="menu-item-title"> {{{ data.title_field }}} </span>

							</span>
							<span class="item-controls ">
								<button type="button" class="button-link item-edit" aria-expanded="false">
									<span class="screen-reader-text"><?php _ex('Edit', 'widget')?></span>
									<span class="toggle-indicator" aria-hidden="true"></span>
								</button>
							</span>
						</div>
					</div>
					<div class="menu-item-settings wp-clearfix">
						<ul class="customize-control-repeater-field-settings">

                        </ul>

						<div class="menu-item-actions description-thin submitbox">
							<button type="button" class="button-link button-link-delete item-delete submitdelete deletion"><?php esc_html_e('Delete', 'devmonsta')?></button>
						</div>
					</div>
				</div>
            </div>





			<button type="button" class="button customize-add-repeater-field" aria-label="<?php esc_attr_e('Add new item');?>" aria-expanded="false" aria-controls="available-repeater-items">
				<?php echo esc_html(get_option('devm_add_button_text'))?>
			</button>
		</div>
		<?php
}
}