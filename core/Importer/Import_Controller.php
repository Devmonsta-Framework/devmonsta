<?php 
namespace Devmonsta\Importer;
defined('ABSPATH') || exit;

class Import_Controller {

    private $wxr_import;
    private $db;


    public function __construct(){
        $this->db = Db_Controller::instance();
    }

    public function begin($import_file, $selected_demo_array){
        $this->wxr_import = new WXR_Importer();
        $this->wxr_import->fetch_attachments = true;

        $this->db->set('devmonsta_import_pointer_options', 'begin');
        $this->db->set($this->db->pnt_opt, ['selected_demo_array' => $selected_demo_array]);

        ob_start();
        $this->wxr_import->import( $import_file, $selected_demo_array );
        ob_end_clean();
        $this->db->delete($this->db->imp_status);
    }

    public function end(){
        $this->db->delete($this->db->imp_status);
        flush_rewrite_rules();
        return true;
    }
}