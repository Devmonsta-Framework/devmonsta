<?php

namespace Devmonsta;

use Devmonsta\Traits\Singleton;

final class Bootstrap
{

    use Singleton;

    /**
     * =============================================
     *      Bootstrap options for
     *      Customizer , Custom posts & Taxonomies
     *      @since 1.0.0
     * =============================================
     */

    public function init()
    {
        define('DM', true);
        
        \Devmonsta\Options\Customizer\Customizer::instance()->init();
        \Devmonsta\Options\Posts\Posts::instance()->init();

        //Make all the helper functions available
        require_once dirname(__FILE__) . '/helpers/general.php';
    }

}