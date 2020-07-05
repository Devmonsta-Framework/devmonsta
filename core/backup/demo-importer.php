<?php

class DEVM_Demo_Importer {

    /**
     * The instance *Singleton* of this class
     *
     * @var object
     */
    private static $instance;

    public $importer;

    private $plugin_page;

    public $import_files;

    public $log_file_path;

    /**
     * The index of the `import_files` array (which import files was selected).
     *
     * @var int
     */
    private $selected_index;

    /**
     * The paths of the actual import files to be used in the import.
     *
     * @var array
     */
    private $selected_import_files;

    /**
     * Holds any error messages, that should be printed out at the end of the import.
     *
     * @var string
     */
    public $frontend_error_messages = [];

    /**
     * Was the before content import already triggered?
     *
     * @var boolean
     */
    private $before_import_executed = false;

    private $imort_page_setup = [];

    public static function get_instance() {

        if ( null === static::$instance ) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    function __construct() {

        add_action( 'admin_init', [$this, 'dms_demo_import_script_enqueuer'] );
        // Actions.
        add_action( 'admin_menu', [$this, 'create_import_page'] );
        add_action( "wp_ajax_dms_import_config", [$this, "dms_import_config"] );
        add_action( "wp_ajax_dms_import_content_before", [$this, "dms_import_content_before"] );
        add_action( "wp_ajax_dms_import_erase_data", [$this, "dms_import_erase_data"] );
        add_action( "wp_ajax_dms_import_plugin_install", [$this, "dms_import_plugin_install"] );

        add_action( "wp_ajax_dms_import_demo", [$this, "dms_import_demo_ajax_call_back"] );
    }

    function dms_demo_import_script_enqueuer() {
        global $pagenow;

        if ( isset( $_GET['page'] ) && 'tools.php' == $pagenow && 'devm-demo-import' == $_GET['page'] ) {
            wp_enqueue_style(
                'devm-font-style',
                devm_get_framework_directory_uri( '/static/css/font-style.css' ),
                null );

            wp_enqueue_style(
                'devm-importer-ui',
                devm_get_framework_directory_uri( '/static/css/importer-ui.css' ),
                null );

            wp_enqueue_style(
                'devm-importer-custom-ui',
                devm_get_framework_directory_uri( '/static/css/importer-ui-custom.css' ),
                null );

            wp_enqueue_script(
                'devm-importer-ui',
                devm_get_framework_directory_uri( '/static/js/ui.min.js' ),
                ['jquery'] );

            wp_enqueue_script(
                'devm_import_script',
                devm_get_framework_directory_uri( '/static/js/devm_import_script.js' ),
                ['jquery'] );

            wp_localize_script( 'devm_import_script', 'dmsAjax', ['ajaxurl' => admin_url( 'admin-ajax.php' )] );
        }

    }

    public function create_import_page() {
        $this->imort_page_setup = apply_filters( 'devm/imort_page_setup', [
            'parent_slug' => 'tools.php',
            'page_title'  => esc_html__( 'Demo Import', 'devmonsta' ),
            'menu_title'  => esc_html__( 'Import Demo', 'devmonsta' ),
            'capability'  => 'import',
            'menu_slug'   => 'devm-demo-import',
        ] );

        $this->plugin_page = add_submenu_page(
            $this->imort_page_setup['parent_slug'],
            $this->imort_page_setup['page_title'],
            $this->imort_page_setup['menu_title'],
            $this->imort_page_setup['capability'],
            $this->imort_page_setup['menu_slug'],
            apply_filters( 'devm/import_page_display_callback_function', [$this, 'display_plugin_page'] )
        );

        register_importer( $this->imort_page_setup['menu_slug'], $this->imort_page_setup['page_title'], $this->imort_page_setup['menu_title'], apply_filters( 'devm/plugin_page_display_callback_function', [$this, 'display_plugin_page'] ) );
    }

    public function display_plugin_page() {
        require_once devm_get_framework_directory() . '/views/import.php';
    }

    private function __clone() {
    }

    private function __wakeup() {
    }

    function dms_import_config() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "dms_demo_import_nonce" ) ) {
            exit( "Woof Woof Woof" );
        }

        $result_array = [
            "status"   => 1,
            'xml_link' => esc_url( $_POST["xml_link"] ),
            'xml_data' => $_POST['xml_data'],
            'nonce'    => $_POST["nonce"],
            'name'     => sanitize_title_with_dashes( $_POST['name'] ),
            "messages" => [
                " data message one",
                "delete previous data message two",
                "delete previous data message three",
            ],
            "data"     => [
                "config data one",
                "delete previous data data two",
                "delete previous data data three",
            ],

        ];

        wp_send_json_success( $result_array );
        wp_die();
    }

    function dms_import_erase_data() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "dms_demo_import_nonce" ) ) {
            exit( "Woof Woof Woof" );
        }

        $result_array = [
            "status"   => "1",
            'next'     => 'dms_import_plugin_install',
            'config'   => $_POST['config'],
            "messages" => [],
            "data"     => [],
        ];

        $config_data     = $_POST["config"];
        $delete_selected = $config_data["dms_delete_data"];

        if ( $delete_selected == "true" ) {
            $reset_db_obj = new Dms_Reset_DB();
            $reset_db_obj->dms_reset_previous_data();

            $result_array["messages"] = ["Previous data erased"];
        }

        wp_send_json_success( $result_array );
        wp_die();
    }

    function dms_import_plugin_install() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "dms_demo_import_nonce" ) ) {
            exit( "Woof Woof Woof" );
        }

        $config_data            = $_POST["config"];
        $required_plugins_array = $config_data["required_plugin"];

        $result_array = [
            "status"   => "1",
            'next'     => 'dms_import_demo',
            'nonce'    => $_POST["nonce"],
            'config'   => $_POST['config'],
            "messages" => [],
            "data"     => [],
        ];

        $dms_plugin_obj           = new Dms_Plugin_Backup_Restore();
        $result_message           = $dms_plugin_obj->dms_process_plugins( $required_plugins_array );
        $result_array["messages"] = [$result_message];

        wp_send_json_success( $result_array );
        wp_die();
    }

    function dms_import_demo_ajax_call_back() {

// nonce check for an extra layer of security, the function will exit if it fails
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "dms_demo_import_nonce" ) ) {
            exit( "Woof Woof Woof" );
        }

        $xml_config = $_POST['config'];
        $xml_link   = $xml_config["xml_link"]["xml_link"];
        $xml_name   = $xml_config["xml_link"]["name"];
        $result     = [
            "status"   => "1",
            'next'     => 'final',
            'xml_link' => $xml_link,
            'nonce'    => $_POST["nonce"],
            'config'   => $_POST['config'],
            "messages" => ['Successfully imported the content.'],
            "data"     => [],
        ];

        $d                      = new Dms_Downloader();
        $im                     = new Dms_Importer();
        $filename               = 'dms_production.xml';
        $dms_imported_file_path = $im->get_import_file_path( $filename );

        ignore_user_abort( true );
        try {
            if ( set_time_limit( 0 ) !== true ) {
                ini_set( 'max_execution_time', 0 );
            }

            if ( ini_get( 'max_execution_time' ) !== '0' ) {
                error_log( "timeout could not be updated to unlimited." );

                if ( set_time_limit( 600 ) !== true ) {
                    ini_set( 'max_execution_time', 600 );
                }

                if ( ini_get( 'max_execution_time' ) !== '600' ) {
                    error_log( "timeout could not be updated." );
                }

            }

        } catch ( Exception $ex ) {
            error_log( "timeout could not be updated: " . $ex->getMessage() );
        }

        if ( file_exists( $dms_imported_file_path ) ) {
            unlink( $dms_imported_file_path );
        }

        try {
            $dms_imported_file_path = $d->download_xml_file( $xml_link, $dms_imported_file_path );
        } catch ( Exception $ex ) {
            error_log( $dms_imported_file_path . ": could not be downloaded. error message:" . $ex->getMessage() );
        }

        if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {

            try {
                $error = esc_html__( 'Data import failed', 'dms' );

                $return_success = $im->import_dummy_xml( $dms_imported_file_path );
                if ( $return_success ) {
                    wp_send_json_success( $result );
                } else {
                    throw new Exception( $error );
                }

            } catch ( Exception $e ) {
                // error_log($dms_imported_file_path  . ": could not be imported. error message:" . $e->getMessage());
                $result['messages'] = ["demo import failed"];
                wp_send_json_error( $result );
            }

        } else {
            header( "Location: " . $_SERVER["HTTP_REFERER"] );
        }

        // don't forget to end your scripts with a die() function - very important
        wp_die();
    }

    function dms_import_content_before() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "dms_demo_import_nonce" ) ) {
            exit( "Woof Woof Woof" );
        }

        $result_array = [
            "status"          => "1",
            'required_plugin' => $_POST['required_plugin'],
            'nonce'           => $_POST["nonce"],
            'dms_delete_data' => $_POST['dms_delete_data'],
            "messages"        => [
                "import content message one",
                "import content message two",
                "import content message three",
            ],
            "data"            => [
                "import content data one",
                "import content data two",
                "import content data three",
            ],
        ];

        wp_send_json_success( $result_array );
        wp_die();
    }

}

DEVM_Demo_Importer::get_instance();
