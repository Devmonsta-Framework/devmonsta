<?php

namespace Devmonsta;

use Devmonsta\Traits\Singleton;

final class Bootstrap {

    use Singleton;

    /**
     * =============================================
     *      Bootstrap options for
     *      Customizer , Custom posts & Taxonomies
     *      @since 1.0.0
     * =============================================
     */

    public function init() {
        
        add_filter( 'upload_mimes', [$this, 'accepted_myme_types'], 1, 1 );

        //Make all the helper functions available
        $helper_files = [
            'class-devm-db-options-model',
            'class-devm-dumper',
            'meta',
            'class-devm-cache',
            'class-devm-callback',
            'class-devm-wp-meta',
            'database',
            'class-devm-resize',
            'general',
            'repeater',
        ];

        foreach ( $helper_files as $file ) {
            require dirname( __FILE__ ) . '/helpers/' . $file . '.php';
        }

        \Devmonsta\Options\Customizer\Customizer::instance()->init();
        \Devmonsta\Options\Posts\Posts::instance()->init();
        \Devmonsta\Options\Taxonomies\Taxonomies::instance()->init();
        \Devmonsta\Rest::instance()->init();

        /**
         * Site Optimization Tools
         */

        //include file to backup data
        $current_page = substr( $_SERVER["SCRIPT_NAME"], strrpos( $_SERVER["SCRIPT_NAME"], "/" ) + 1 );

        if ( is_admin() && 'export.php' == $current_page ) {
            require dirname( __FILE__ ) . '/backup/export.php';
            require dirname( __FILE__ ) . '/backup/export-timetable-plugin-data.php';
        }

        require dirname( __FILE__ ) . '/backup/demo-importer.php';
        require dirname( __FILE__ ) . '/autoload.php';
    }
    
    /**
     * Undocumented function
     *
     * @param [type] $mime_types
     * @return void
     */
    function accepted_myme_types( $mime_types ) {
        $mime_types['svg']  = 'image/svg+xml';    // Adding .svg extension
        $mime_types['json'] = 'application/json'; // Adding .json extension
    
        unset( $mime_types['xls'] );  // Remove .xls extension
        unset( $mime_types['xlsx'] ); // Remove .xlsx extension
    
        return $mime_types;
    }
    

}
