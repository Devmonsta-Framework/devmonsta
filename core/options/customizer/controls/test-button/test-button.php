<?php
namespace Devmonsta\Options\Customizer\Controls\TestButton;

class TestButton extends \WP_Customize_Control
{
    public $type = 'test-button';



    /**
     * enqueue() – Enqueue control related scripts/styles.
     * value() – Fetch a setting’s value. Grabs the main setting by default.
     * to_json() – Refresh the parameters passed to the JavaScript via JSON.
     * check_capabilities() – Check if the theme supports the control and check user capabilities.
     * maybe_render() – Check capabilities and render the control.
     * render() – Render the control. Renders the control wrapper, then calls $this->render_content().
     * render_content() – Render the control’s content.
     */

     public function __construct($manager,$id,$args)
     {
        parent::__construct( $manager, $id, $args );
     }
     

   
    
    public function render_content()
    {
    ?>
  
        <input <?php $this->link(); ?> value="<?php echo $this->value(); ?>" placeholder="Test text" type="text" name="my_cool_text">
    
    <?php
}
}