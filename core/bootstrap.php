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
        \Devmonsta\Options\Customizer\Customizer::instance()->init();
    }

}
