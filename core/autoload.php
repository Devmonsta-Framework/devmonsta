<?php
if ( !defined( 'DEVM' ) ) {
    die( 'Forbidden' );
}

spl_autoload_register( 'devm_includes_backup_autoload' );
function devm_includes_backup_autoload( $class ) {

    switch ( $class ) {

    case 'Dms_Downloader':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/Downloader.php';
        break;
    case 'DMS_Helpers':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/Helpers.php';
        break;
    case 'Dms_Importer':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-importer.php';
        break;
    case 'Dms_WXR_Importer':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-importer.php';
        break;
    case 'Dms_WXR_Parser':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-parsers.php';
        break;
    case 'Dms_WXR_Parser_SimpleXML':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-parsers.php';
        break;
    case 'Dms_WXR_Parser_XML':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-parsers.php';
        break;
    case 'Dms_WXR_Parser_Regex':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-wxr-parsers.php';
        break;
    case 'Devm_Plugin_Backup_Restore':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-plugin-backup-restore.php';
        break;
    case 'Devm_Reset_DB':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-reset-db.php';
        break;
    case 'Dms_Importer':
        require_once dirname( __FILE__ ) . '/helpers/backup/inc/class-importer.php';
        break;
    }

}
