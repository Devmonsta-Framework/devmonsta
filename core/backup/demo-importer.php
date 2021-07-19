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

        add_action( 'admin_init', [$this, 'devm_demo_import_script_enqueuer'] );
        // Actions.
        add_action( "wp_ajax_devm_import_config", [$this, "devm_import_config"] );
        add_action( "wp_ajax_devm_import_content_before", [$this, "devm_import_content_before"] );
        add_action( "wp_ajax_devm_import_erase_data", [$this, "devm_import_erase_data"] );
        add_action( "wp_ajax_devm_import_plugin_install", [$this, "devm_import_plugin_install"] );

        new \Devmonsta\Importer\Base;
    }

    function devm_demo_import_script_enqueuer() {
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

            wp_enqueue_script(
                'devm_import_events',
                devm_get_framework_directory_uri( '/static/js/import-events.js' ),
                ['jquery'] );

            wp_localize_script( 'devm_import_script', 'devmAjax', ['ajaxurl' => admin_url( 'admin-ajax.php' )] );
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

    public function __clone() {
    }

    public function __wakeup() {
    }


    function devm_import_config() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "devm_demo_import_nonce" ) ) {
            exit( "Access denied" );
        }

        $result_array = [
            "status"                => 1,
            'xml_link'              => esc_url( $_POST["xml_link"] ),
            'xml_data'              => $_POST['xml_data'],
            'xml_selected_demo'     => $_POST['xml_selected_demo'],
            'nonce'                 => $_POST["nonce"],
            'name'                  => sanitize_title_with_dashes( $_POST['name'] ),
            "messages"  => [
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


    /**
     * Install and active all required plugins
     *
     * @return void
     */
    function devm_import_plugin_install() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "devm_demo_import_nonce" ) ) {
            exit( "Access Denied" );
        }

        $config_data            = $_POST["config"];
        $required_plugins_array = $config_data["required_plugin"];

        $result_array = [
            "status"   => "1",
            'next'     => 'devm_import_demo',
            'nonce'    => $_POST["nonce"],
            'config'   => $_POST['config'],
            "messages" => [],
            "data"     => [],
        ];

        $devm_plugin_obj           = new \Devmonsta\Importer\Plugin_Backup_Restore();
        $result_message           = $devm_plugin_obj->devm_process_plugins( $required_plugins_array );
        $result_array["messages"] = [$result_message];

        wp_send_json_success( $result_array );
        wp_die();
    }


    /**
     * Erase existing data in checkbox selected
     *
     * @return void
     */
    function devm_import_erase_data() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "devm_demo_import_nonce" ) ) {
            exit( "Access Denied" );
        }

        $result_array = [
            "status"   => "1",
            'next'     => 'devm_import_plugin_install',
            'config'   => $_POST['config'],
            "messages" => [],
            "data"     => [],
        ];

        $config_data     = $_POST["config"];
        $delete_selected = $config_data["devm_delete_data"];

        if ( $delete_selected == "true" ) {
            $reset_db_obj = new \Devmonsta\Importer\Reset_DB();
            $reset_db_obj->devm_reset_previous_data();

            $result_array["messages"] = ["Previous data erased"];
        }

        wp_send_json_success( $result_array );
        wp_die();
    }

    /**
     * Prepare all messages and data before starting demo import
     *
     * @return void
     */
    function devm_import_content_before() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "devm_demo_import_nonce" ) ) {
            exit( "Access Denied" );
        }

        $result_array = [
            "status"          => "1",
            'required_plugin' => $_POST['required_plugin'],
            'nonce'           => $_POST["nonce"],
            'devm_delete_data' => $_POST['devm_delete_data'],
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
