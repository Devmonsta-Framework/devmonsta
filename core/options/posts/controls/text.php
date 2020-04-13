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
        print_r($content);
        // $prefix = 'devmonsta_';
        // self::html($prefix . $content['name'], $content['label']);
    }

    public static function html($name, $lable)
    {
    ?>
        <lable><?php echo esc_html($lable); ?> </lable>
        <input type="text" name="<?php echo esc_html($name); ?>" >
    <?php

    }
}
