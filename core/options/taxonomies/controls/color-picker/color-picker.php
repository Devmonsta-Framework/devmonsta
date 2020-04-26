<?php
namespace Devmonsta\Options\Taxonomies\Controls\ColorPicker;

use Devmonsta\Options\Taxonomies\Structure;

class ColorPicker extends Structure
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
    ?>
    <lable><?php echo esc_html( $this->content['label'] ); ?></lable>
    <input  type="color">
    <?php
    }

     public function columns(){
         
     }

     public function edit_fields($term, $taxonomy){
       
     }
}