<?php

namespace Devmonsta\Options\Posts\Controls\Text;

use Devmonsta\Options\Posts\Structure;

class Text extends Structure
{

    protected $value;

    public function init()
    {

    }

    public function enqueue()
    {

        add_action('admin_enqueue_scripts', [$this, 'load_scripts']);

    }

    public function load_scripts($hook)
    {

        wp_enqueue_script('custom-js', plugins_url('text/assets/js/script.js', dirname(__FILE__)));
    }

    public function render()
    {

        $content = $this->content;
        global $post;

        $this->value = get_post_meta($post->ID, $this->prefix . $content['name'], true);
        $this->output();
    }

    public function output()
    {

        $lable = $this->content['label'];
        $name = $this->content['name'];
        ?>
        <lable><?php echo esc_html($lable); ?> </lable>
        <input type="text" name="<?php echo esc_html($this->prefix . $name); ?>" value="<?php echo esc_html($this->value); ?>" >

    <?php

    }
}
