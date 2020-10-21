<?php

if ( !defined( 'DEVM' ) ) {
    die( 'Forbidden' );
}

add_filter( 'upload_mimes', 'my_myme_types', 1, 1 );
function my_myme_types( $mime_types ) {
    $mime_types['svg']  = 'image/svg+xml';    // Adding .svg extension
    $mime_types['json'] = 'application/json'; // Adding .json extension

    unset( $mime_types['xls'] );  // Remove .xls extension
    unset( $mime_types['xlsx'] ); // Remove .xlsx extension

    return $mime_types;
}

spl_autoload_register( 'devm_includes_backup_autoload' );
function devm_includes_backup_autoload( $class ) {

    switch ( $class ) {

    case 'Devm_Downloader':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/Downloader.php';
        break;
    case 'DEVM_Helpers':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/Helpers.php';
        break;
    case 'Devm_Importer':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-importer.php';
        break;
    case 'Devm_WXR_Importer':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-importer.php';
        break;
    case 'Devm_WXR_Parser':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-parsers.php';
        break;
    case 'Devm_WXR_Parser_SimpleXML':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-parsers.php';
        break;
    case 'Devm_WXR_Parser_XML':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-parsers.php';
        break;
    case 'Devm_WXR_Parser_Regex':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-parsers.php';
        break;
    case 'Devm_Plugin_Backup_Restore':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-plugin-backup-restore.php';
        break;
    case 'Devm_Reset_DB':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-reset-db.php';
        break;
    case 'Devm_Importer':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-importer.php';
        break;
    }

}
