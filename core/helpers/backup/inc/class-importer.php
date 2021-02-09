<?php

defined('ABSPATH') || exit;

class Devm_Importer
{

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

	public function import_dummy_xml($filepath = null, $selected_demo_array = [])
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
		require dirname(__FILE__) . '/class-wxr-importer.php';

		// Import XML file demo content.
		if (is_file($import_file)) {

			$wp_import                    = new Devm_WXR_Importer();
			$wp_import->fetch_attachments = true;

			ob_start();
			$wp_import->import( $import_file, $selected_demo_array );
			ob_end_clean();
			flush_rewrite_rules();
			return true;
		}
		return false;
	}
}
