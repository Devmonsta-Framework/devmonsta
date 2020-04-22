<?php
namespace Devmonsta\Options\Taxonomies\Controls\Text;

use Devmonsta\Options\Taxonomies\Structure;

class Text extends Structure
{
    public function init()
    {
        $this->columns();
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

            <lable><?php echo esc_html($this->content['label']); ?></lable>
            <input name="<?php echo $name; ?>"  type="text">

    <?php
    }

    public function columns()
    {
        $content = $this->content;
        add_filter('manage_edit-' . $this->taxonomy . '_columns', function ($columns) use ($content) {

            $columns[$content['name']] = __($content['label'], 'devmonsta');

            return $columns;
        });
    }

}
