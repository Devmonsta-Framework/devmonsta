<?php

namespace Devmonsta\Options\Posts\Controls\Text;

use Devmonsta\Options\Posts\Structure;

class Text extends Structure
{
    protected $name;
    protected $value;
    protected $prefix;
    /**
     * Ever class must contain show method
     * Before name of the controller must use @devmonsta prefix
     */

    public function init()
    {

    }

    public function enqueue()
    {

    }

    public function render()
    {

        $content = $this->content;
        global $post;

        $this->prefix = 'devmonsta_';
        $this->value = get_post_meta($post->ID, $this->prefix . $content['name'], true);
        $this->output();
    }

    public function output()
    {
        error_log('output section from text');
        $lable = $this->content['label'];
        $name = $this->content['name'];
        ?>
        <lable><?php echo esc_html($lable); ?> </lable>
        <input type="text" name="<?php echo esc_html($this->prefix . $name); ?>" value="<?php echo esc_html($this->value); ?>" >

    <?php

    }
}
