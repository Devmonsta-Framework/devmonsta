<?php

namespace Devmonsta\Options\Posts\Controls\DatePicker;

use Devmonsta\Options\Posts\Structure;

class DatePicker extends Structure{

	/**
	 * @internal
	 */
    public function init(){

    }

	/**
	 * @internal
	 */
    public function enqueue()
    {
        
    }

    
	/**
	 * @internal
	 */
    public function render(){
        $content = $this->content;
        global $post;
        $this->value = !is_null(get_post_meta($post->ID, $this->prefix . $content['name'], true)) ? 
                        get_post_meta($post->ID, $this->prefix . $content['name'], true)
                        : $content['value'];
        $this->output();
    }

	/**
	 * @internal
	 */
    public function output()
    { 
        $lable          = isset($this->content['label'])? $this->content['label'] : '';
        $name           = isset($this->content['name']) ? $this->content['name'] : '';
        $desc           = isset($this->content['desc']) ? $this->content['desc'] : '';
        $attrs          = isset($this->content['attr']) ? $this->content['attr'] : '';
        $min_date       = isset($this->content['min-date']) ? $this->content['min-date'] : date('d-m-Y');
        $max_date       = isset($this->content['max-date']) ? $this->content['max-date'] : '';
        ?>
        <div <?php echo esc_attr($attrs); ?>>
            <lable><?php echo esc_html($lable); ?> </lable>
            <div><small><?php echo esc_html($desc); ?> </small></div>
            <input type="date" name="<?php echo esc_html($this->prefix . $name);?>"
                    value="<?php echo esc_html('Y-m-d', $this->value);?>"
                    min="<?php echo esc_html($min_date)?>" max="<?php echo esc_html($max_date)?>">
        </div<>
    <?php
    }

}
