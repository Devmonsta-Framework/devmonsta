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
        define( 'DM', true );
        //Make all the helper functions available
        $helper_files = [ 'general',
            'class-dm-dumper',
            'meta',
            // 'database',
        ];

        foreach ( $helper_files as $file ) {
            require dirname( __FILE__ ) . '/helpers/' . $file . '.php';
        }

        \Devmonsta\Options\Customizer\Customizer::instance()->init();
        \Devmonsta\Options\Posts\Posts::instance()->init();
        \Devmonsta\Options\Taxonomies\Taxonomies::instance()->init();
    }

}
