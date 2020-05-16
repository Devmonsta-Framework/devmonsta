<?php

namespace Devmonsta\Options\Posts\Controls\Switcher;

use Devmonsta\Options\Posts\Structure;

class Switcher extends Structure {

    protected $current_screen;

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

        add_action( 'admin_enqueue_scripts', [$this, 'load_switcher_scripts'] );
    }

    /**
     * @internal
     */
    public function load_switcher_scripts() {
        // js
        wp_enqueue_script( 'dm-switcher', plugins_url( 'switcher/assets/js/dm-switcher.js', dirname( __FILE__ ) ), ['jquery'], time(), true );
        //css
        wp_enqueue_style( 'dm-switcher', plugins_url( 'switcher/assets/css/dm-switcher.css', dirname( __FILE__ ) ) );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $this->value = (  ( $this->current_screen == "post" )
            && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
            && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
        ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];

        $this->output();
    }

    public function array_key_first( array $array ) {
        foreach ( $array as $key => $value ) {return $key;}
    }

    /**
     * @internal
     */
    public function output() {
        $label        = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix       = 'devmonsta_';
        $name         = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc         = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $left_choice  = isset( $this->content['left-choice'] ) ? $this->content['left-choice'] : '';
        $right_choice = isset( $this->content['right-choice'] ) ? $this->content['right-choice'] : '';
        $left_key     = $this->array_key_first( $left_choice );
        $right_key    = $this->array_key_first( $right_choice );

        // add inline css for dynamic value
        $style = '';
        $style .= '
        .dm_switcher_item label.dm_switcher_label:before {
            content: "' . esc_attr( $left_choice[$left_key] ) . '";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 10px;
            display: inline-block;
            text-transform: uppercase;
            font-weight: 600;
        }
        .dm_switcher_item input.dm-control-input:checked + label.dm_switcher_label:before {
            content: "' . esc_attr( $right_choice[$right_key] ) . '";
            right: inherit;
            left: 10px;
        }
        ';
        wp_register_style( 'dm-switcher-inline-css', false, ['dm-switcher'] );
        wp_enqueue_style( 'dm-switcher-inline-css' );
        wp_add_inline_style( 'dm-switcher-inline-css', $style );

        ?>

        <div><?php echo esc_html( $label ); ?> </div>
        <div><small class='dm-option-desc'><?php echo esc_html( $desc ); ?></small></div>
            <div class='dm_switcher_main_block'>
                <div class='dm_switcher_item'>
                    <input  type='checkbox' value='<?php echo esc_attr( $right_key ); ?>' class='dm-control-input dm_switcher_right' name='<?php echo esc_attr( $name ); ?>'
                            <?php echo ( $this->value == $right_key ) ? 'checked' : ''; ?> />
                    <label  class='dm_switcher_label dm-option-label'></label>
                </div>
                <input class='dm_switcher_left' type='checkbox' value='<?php echo esc_attr( $left_key ); ?>'  name='<?php echo esc_attr( $name ); ?>' <?php echo ( $this->value == $left_key ) ? 'checked' : ''; ?> />
            </div>

        <?php
}

    public function columns() {
        $visible = false;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns',
            function ( $columns ) use ( $content, $visible ) {

                $visible = ( isset( $content['show_in_table'] ) && $content['show_in_table'] === true ) ? true : false;

                if ( $visible ) {
                    $columns[$content['name']] = __( $content['label'], 'devmonsta' );
                }

                return $columns;
            } );

        $cc = $content;
        add_filter( 'manage_' . $this->taxonomy . '_custom_column',
            function ( $content, $column_name, $term_id ) use ( $cc ) {

                if ( $column_name == $cc['name'] ) {
                    echo esc_html( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );

                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $this->load_switcher_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix             = 'devmonsta_';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $left_choice        = isset( $this->content['left-choice'] ) ? $this->content['left-choice'] : '';
        $right_choice       = isset( $this->content['right-choice'] ) ? $this->content['right-choice'] : '';
        $left_key           = $this->array_key_first( $left_choice );
        $right_key          = $this->array_key_first( $right_choice );
        $value              = get_term_meta( $term->term_id, $name, true );
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

        $class_attributes = "class='dm-option term-group-wrap $dynamic_classes'";
        $default_attributes .= $class_attributes;

        // add inline css for dynamic value
        $style = '';
        $style .= '
        .dm_switcher_item label.dm_switcher_label:before {
            content: "' . esc_attr( $left_choice[$left_key] ) . '";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 10px;
            display: inline-block;
            text-transform: uppercase;
            font-weight: 600;
        }
        .dm_switcher_item input.dm-control-input:checked + label.dm_switcher_label:before {
            content: "' . esc_attr( $right_choice[$right_key] ) . '";
            right: inherit;
            left: 10px;
        }
        ';
        wp_register_style( 'dm-switcher-inline-css', false, ['dm-switcher'] );
        wp_enqueue_style( 'dm-switcher-inline-css' );
        wp_add_inline_style( 'dm-switcher-inline-css', $style );

        ?>

    <tr <?php echo dm_render_markup( $default_attributes ); ?> >
    <th scope="row">
        <label class="dm-option-label"><?php echo esc_html( $label ); ?></label>
    </th>
    <td>
            <div class='dm_switcher_main_block'>
                <div class='dm_switcher_item'>
                    <input type='checkbox' value='<?php echo esc_attr( $right_key ); ?>' class='dm-control-input dm_switcher_right' name='<?php echo esc_attr( $name ); ?>'
                            <?php echo ( $value == $right_key ) ? 'checked' : ''; ?> />
                    <label  class='dm_switcher_label dm-option-label'></label>
                </div>
                <input class='dm_switcher_left' type='checkbox' value='<?php echo esc_attr( $left_key ); ?>'  name='<?php echo esc_attr( $name ); ?>' <?php echo ( $value == $left_key ) ? 'checked' : ''; ?> />
            </div>
        <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
    </td>
    </tr>
<?php
}

}
