<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita9f943861c85c89187796fe4501729a2
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Devmonsta\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Devmonsta\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core',
        ),
    );

    public static $classMap = array (
        'DEVM_Theme_Customize_Repeater_Control' => __DIR__ . '/../..' . '/core/options/customizer/libs/customize-repeater-control.php',
        'DEVM_Theme_Customize_Repeater_Popup_Control' => __DIR__ . '/../..' . '/core/options/customizer/libs/customize-repeater-control-popup.php',
        'DEVM_WP_Customize_Panel' => __DIR__ . '/../..' . '/core/options/customizer/libs/sections.php',
        'DEVM_WP_Customize_Section' => __DIR__ . '/../..' . '/core/options/customizer/libs/sections.php',
        'Devmonsta\\Bootstrap' => __DIR__ . '/../..' . '/core/bootstrap.php',
        'Devmonsta\\Libs\\Customizer' => __DIR__ . '/../..' . '/core/libs/customizer.php',
        'Devmonsta\\Libs\\Posts' => __DIR__ . '/../..' . '/core/libs/posts.php',
        'Devmonsta\\Libs\\Repeater' => __DIR__ . '/../..' . '/core/libs/repeater.php',
        'Devmonsta\\Libs\\Taxonomies' => __DIR__ . '/../..' . '/core/libs/taxonomies.php',
        'Devmonsta\\Options\\Customizer\\Controls' => __DIR__ . '/../..' . '/core/options/customizer/controls.php',
        'Devmonsta\\Options\\Customizer\\Customizer' => __DIR__ . '/../..' . '/core/options/customizer/customizer.php',
        'Devmonsta\\Options\\Customizer\\Structure' => __DIR__ . '/../..' . '/core/options/customizer/structure.php',
        'Devmonsta\\Options\\Posts\\Controls' => __DIR__ . '/../..' . '/core/options/posts/controls.php',
        'Devmonsta\\Options\\Posts\\Posts' => __DIR__ . '/../..' . '/core/options/posts/posts.php',
        'Devmonsta\\Options\\Posts\\Structure' => __DIR__ . '/../..' . '/core/options/posts/structure.php',
        'Devmonsta\\Options\\Posts\\Validator' => __DIR__ . '/../..' . '/core/options/posts/validator.php',
        'Devmonsta\\Options\\Posts\\View' => __DIR__ . '/../..' . '/core/options/posts/view.php',
        'Devmonsta\\Options\\Taxonomies\\Structure' => __DIR__ . '/../..' . '/core/options/taxonomies/structure.php',
        'Devmonsta\\Options\\Taxonomies\\Taxonomies' => __DIR__ . '/../..' . '/core/options/taxonomies/taxonomies.php',
        'Devmonsta\\Rest' => __DIR__ . '/../..' . '/core/rest.php',
        'Devmonsta\\Traits\\Singleton' => __DIR__ . '/../..' . '/core/traits/singleton.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita9f943861c85c89187796fe4501729a2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita9f943861c85c89187796fe4501729a2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita9f943861c85c89187796fe4501729a2::$classMap;

        }, null, ClassLoader::class);
    }
}
