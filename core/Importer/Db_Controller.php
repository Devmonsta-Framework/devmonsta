<?php 
namespace Devmonsta\Importer;
defined('ABSPATH') || exit;

class Db_Controller {

    private static $instance;
    private $ser_key;
    public $imp_status;
    public $pnt_opt; 

    public static function instance() {

        if ( null === static::$instance ) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct(){
        $this->ser_key = 'devmonsta_import_serialized_data';
        $this->imp_status = 'devmonsta_import_status';
        $this->pnt_opt = 'devmonsta_import_pointer_options';   
    }

    public function delete($transient){
        delete_option( $transient);
    }

    public function set($transient, $value){
        update_option( $transient, $value);
    }

    public function merge($transient, $value){
        $old_value = $this->get($transient);

        if(is_array($old_value)){
            $value = array_merge($old_value, $value);
        }
        $this->set($transient, $value);
    }

    public function get($transient){
        return get_option( $transient );
    }

}