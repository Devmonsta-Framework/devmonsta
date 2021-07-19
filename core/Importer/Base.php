<?php 
namespace Devmonsta\Importer;
defined('ABSPATH') || exit;

class Base {

    function __construct() {
        add_action( "wp_ajax_devm_import_demo", [$this, "import_ajax_init"] );
       
        add_filter( 'heartbeat_received', [$this, 'import_ajax_receive_heartbeat'], 10, 2 );
        add_filter( 'heartbeat_settings', [$this, 'heartbeat_settings'] );
    }

    function heartbeat_settings( $settings ) {
        $settings['interval'] = 20;
        return $settings;
    }

    public function import_ajax_receive_heartbeat( array $response, array $received ) {
 
        // If we didn't receive our data, don't send any back.
        if ( empty( $received['devmonsta_import'] ) ) {
            return $response;
        }

        $import_status = Db_Controller::instance()->get(Db_Controller::instance()->imp_status);

        

        if(empty($import_status)){
            return $response;
        }

        $response['devmonsta_import'] = [date('h:i:s'), $received['devmonsta_import'], $import_status];
     
        return $response;
    }

    /**
     * Handle XML import
     *
     * @return void
     */
    public function import_ajax_init() {

        // nonce check for an extra layer of security, the function will exit if it fails
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "devm_demo_import_nonce" ) ) {
            exit( "Access Denied" );
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

        $downloader             = new Downloader();
        $filename               = 'devm_production.xml';
        $devm_imported_file_path = $this->get_import_file_path( $filename );

        ignore_user_abort( true );
        try {
            if ( set_time_limit( 0 ) !== true ) {
                ini_set( 'max_execution_time', 0 );
            }

            if ( ini_get( 'max_execution_time' ) !== '0' ) {
                // error_log( "timeout could not be updated to unlimited." );

                if ( set_time_limit( 600 ) !== true ) {
                    ini_set( 'max_execution_time', 600 );
                }

                if ( ini_get( 'max_execution_time' ) !== '600' ) {
                    // error_log( "timeout could not be updated." );
                }

            }

        } catch ( Exception $ex ) {
            error_log( "timeout could not be updated: " . $ex->getMessage() );
        }

        if ( file_exists( $devm_imported_file_path ) ) {
            unlink( $devm_imported_file_path );
        }

        try {
            $devm_imported_file_path = $downloader->download_xml_file( $xml_link, $devm_imported_file_path );
        } catch ( Exception $ex ) {
            error_log( $devm_imported_file_path . ": could not be downloaded. error message:" . $ex->getMessage() );
        }

        if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {

            try {
                $error = esc_html__( 'Data import failed', 'devm' );

                $selected_demo_array = $_POST['config']["xml_link"]["xml_selected_demo"];
                $return_success = $this->import_from_xml( $devm_imported_file_path, $selected_demo_array );
                if ( $return_success ) {
                    wp_send_json_success( $result );
                } else {
                    throw new \Exception( $error );
                }

            } catch ( Exception $e ) {
                // error_log($devm_imported_file_path  . ": could not be imported. error message:" . $e->getMessage());
                $result['messages'] = ["demo import failed"];
                wp_send_json_error( $result );
            }

        } else {
            header( "Location: " . $_SERVER["HTTP_REFERER"] );
        }

        // don't forget to end your scripts with a die() function - very important
        wp_die();
    }

    public function download($url = null)
	{

		if (is_null($url)) {
			return false;
		}
	}

	public function get_import_file_path($filename)
	{
		$uploads = wp_upload_dir();
		$upload_dir = $uploads['basedir'];
		$upload_dir = $upload_dir . '/devm';
		if (!is_dir($upload_dir)) {
			wp_mkdir_p($upload_dir);
		}
		$file_path = trailingslashit($upload_dir) . sanitize_file_name($filename);
		return $file_path;
	}

	public function import_from_xml($filepath = null, $selected_demo_array = [])
	{
		if (is_null($filepath)) {
			$import_file = $this->get_import_file_path('devm_production.xml');
		} else {
			$import_file = $filepath;
		}

		require_once ABSPATH . 'wp-admin/includes/import.php';

		if (!class_exists('WP_Importer')) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

			if (file_exists($class_wp_importer)) {
				require $class_wp_importer;
			}
		}

		// Import XML file demo content.
		if (is_file($import_file)) {
			(new \Devmonsta\Importer\Import_Controller)->begin( $import_file, $selected_demo_array );
			return true;
		}
		return false;
	}
}