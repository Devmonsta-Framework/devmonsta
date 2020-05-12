<?php

namespace Devmonsta\Options\Posts\Controls\Url;

use Devmonsta\Options\Posts\Structure;

class Url extends Structure {

    protected $value;

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue( $meta_owner ) {
        $this->current_screen = $meta_owner;
    }

    /**
     * @internal
     */
    public function render() {
        global $wpdocs_admin_page;
        $screen               = get_current_screen();
        $this->current_screen = $screen->base;

        if ( $this->current_screen == "post" ) {
            $content = $this->content;
            global $post;
            $this->value = !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ?
            get_post_meta( $post->ID, $this->prefix . $content['name'], true )
            : $content['value'];
        }

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $prefix             = 'devmonsta_';
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";
        $dynamic_classes    = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {

                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }

            }

        }

        $class_attributes = "class='dm-option form-field $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
           </div>
           <div class="dm-option-column right">
                <input 
                    type="url"
                    class="dm-option-input"
                    id="<?php echo esc_attr( $name ); ?>"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_html( $this->value ); 
                ?>" >
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
}

    public function columns() {
        $visible = false;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns', function ( $columns ) use ( $content, $visible ) {

            $visible = ( isset( $content['show_in_table'] ) && $content['show_in_table'] === true ) ? true : false;

            if ( $visible ) {
                $columns[$content['name']] = __( $content['label'], 'devmonsta' );
            }

            return $columns;
        } );

        $cc = $content;
        add_filter( 'manage_' . $this->taxonomy . '_custom_column', function ( $content, $column_name, $term_id ) use ( $cc ) {

            if ( $column_name == $cc['name'] ) {
                echo esc_html( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );
            }

            return $content;

        }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $prefix = 'devmonsta_';
        $label  = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name   = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc   = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs  = isset( $this->content['attr'] ) ? $this->content['attr'] : '';

        $default_attributes = "";
        $dynamic_classes    = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {

                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }

            }

        }

        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;
        $value = get_term_meta( $term->term_id, $name, true );
        ?>

<tr <?php echo dm_render_markup( $default_attributes ); ?> >
    <th scope="row"><label class="dm-option-label"><?php echo esc_html( $label ); ?></label></th>
    <td> <input name="<?php echo esc_attr( $name ); ?>"
                id="<?php echo esc_attr( $name ); ?>"
                type="url"
                value="<?php echo esc_html( $value ); ?>"
                size="40" aria-required="true">

        <br> <small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
    </td>
</tr>
<?php
}

}
