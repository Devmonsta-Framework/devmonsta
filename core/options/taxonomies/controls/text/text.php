<?php
namespace Devmonsta\Options\Taxonomies\Controls\Text;

use Devmonsta\Options\Taxonomies\Structure;

class Text extends Structure
{
    public function init()
    {

      

    }

    public function enqueue()
    {

    }

    public function render()
    {
        $this->output();
    }

    public function output()
    {
        $prefix = 'devmonsta_';
        $name = $prefix . $this->content['name'];

        ?>

            <div class="form-field form-required term-name-wrap">
                <label for="tag-name"><?php echo esc_html($this->content['label']); ?></label>
                <input name="<?php echo $name; ?>" id="<?php echo $name; ?>" type="text" value="" size="40" aria-required="true">
                <p>Devmonsta custom control</p>
            </div>



    <?php
}

    public function columns()
    {
        $visible = true;
        $content = $this->content;
        add_filter('manage_edit-' . $this->taxonomy . '_columns', function ($columns) use ($content,$visible) {

            if (isset($content['show_in_table'])) {
                if ($content['show_in_table'] == false) {
                    $visible = false;
                }
            }
            if ($visible) {
                $columns[$content['name']] = __($content['label'], 'devmonsta');
            }

            return $columns;
        });

        $cc = $content;
        add_filter('manage_' . $this->taxonomy . '_custom_column', function ($content, $column_name, $term_id) use ($cc) {

            if ($column_name == $cc['name']) {
                print_r(get_term_meta($term_id, 'devmonsta_' . $column_name, true));

            }
            return $content;

        }, 10, 3);

    }

    public function edit_fields($term, $taxonomy)
    {

        $prefix = 'devmonsta_';
        $name = $prefix . $this->content['name'];
        $value = get_term_meta($term->term_id, $name, true);
        ?>

        <tr class="form-field term-group-wrap">
            <th scope="row"><label for="feature-group"><?php echo esc_html($this->content['label']); ?></label></th>
            <td> <input name="<?php echo $name; ?>" id="<?php echo $name; ?>" type="text" value="<?php echo $value; ?>" size="40" aria-required="true"></td>
        </tr>


        <?php
}

}
