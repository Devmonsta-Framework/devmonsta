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
        define( 'DEVM', true );

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

    }

}
