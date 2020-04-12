<?php

namespace Devmonsta;

use Devmonsta\Traits\Singleton;

final class Bootstrap
{

    use Singleton;

    /**
     * =============================================
     *      Bootstrap options for
     *      Customizer , Custom posts & Taxonomies
     *      @since 1.0.0
     * =============================================
     */

    public function init()
    {
        \Devmonsta\Options\Customizer\Customizer::instance()->init();
        \Devmonsta\Options\Posts\Posts::instance()->init();

        // add_action('add_meta_boxes', [$this, 'wporg_add_custom_box']);
    }

    public function wporg_add_custom_box()
    {
        $screen = 'page';

        add_meta_box(
            'wporg_box_id', // Unique ID
            'Metabox 1', // Box title
            [$this, 'wporg_custom_box_html'], // Content callback, must be of type callable
            $screen // Post type
        );

        add_meta_box(
            'dms_id',
            'Metabox 2',
            [$this,'custom_content'],
            $screen
        );
    }

    public function custom_content(){

    }

    public function wporg_custom_box_html($post)
    {
        $value = get_post_meta($post->ID, '_wporg_meta_key', true);
        ?>
    <label for="wporg_field">Description for this field</label>
    <select name="wporg_field" id="wporg_field" class="postbox">
        <option value="">Select something...</option>
        <option value="something" <?php selected($value, 'something');?>>Something</option>
        <option value="else" <?php selected($value, 'else');?>>Else</option>
    </select>
    <?php
}

}
