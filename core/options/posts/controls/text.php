<?php

namespace Devmonsta\Options\Posts\Controls;

class Text
{
    /**
     * Ever class must contain show method
     * Before name of the controller must use @devmonsta prefix
     */

    public static function show($content)
    {
        global $post;
        // print_r($content);
        $prefix = 'devmonsta_';
        $name = $content['name'];
        $value = get_post_meta($post->ID,$prefix.$name,true);
        self::html($prefix . $name, $content['label'],$value);
    }

    public static function html($name, $lable,$value)
    {
        ?>
        <lable><?php echo esc_html($lable); ?> </lable>
        <input type="text" name="<?php echo esc_html($name); ?>" value="<?php echo esc_html($value);?>" >
      
    <?php

    }
}
