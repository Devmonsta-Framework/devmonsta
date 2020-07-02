<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc286e51aee7ccaf235821570d4bd7ed5
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
        'DM_WP_Customize_Panel' => __DIR__ . '/../..' . '/core/options/customizer/libs/sections.php',
        'DM_WP_Customize_Section' => __DIR__ . '/../..' . '/core/options/customizer/libs/sections.php',
        'Devmonsta\\Bootstrap' => __DIR__ . '/../..' . '/core/bootstrap.php',
        'Devmonsta\\Libs\\Customizer' => __DIR__ . '/../..' . '/core/libs/customizer.php',
        'Devmonsta\\Libs\\Posts' => __DIR__ . '/../..' . '/core/libs/posts.php',
        'Devmonsta\\Libs\\Repeater' => __DIR__ . '/../..' . '/core/libs/repeater.php',
        'Devmonsta\\Libs\\Taxonomies' => __DIR__ . '/../..' . '/core/libs/taxonomies.php',
        'Devmonsta\\Options\\Customizer\\Controls' => __DIR__ . '/../..' . '/core/options/customizer/controls.php',
        'Devmonsta\\Options\\Customizer\\Customizer' => __DIR__ . '/../..' . '/core/options/customizer/customizer.php',
        'Devmonsta\\Options\\Customizer\\Structure' => __DIR__ . '/../..' . '/core/options/customizer/structure.php',
        'Devmonsta\\Options\\Posts\\Posts' => __DIR__ . '/../..' . '/core/options/posts/posts.php',
        'Devmonsta\\Options\\Posts\\Structure' => __DIR__ . '/../..' . '/core/options/posts/structure.php',
        'Devmonsta\\Options\\Posts\\Validator' => __DIR__ . '/../..' . '/core/options/posts/validator.php',
        'Devmonsta\\Options\\Posts\\View' => __DIR__ . '/../..' . '/core/options/posts/view.php',
        'Devmonsta\\Options\\Taxonomies\\Structure' => __DIR__ . '/../..' . '/core/options/taxonomies/structure.php',
        'Devmonsta\\Options\\Taxonomies\\Taxonomies' => __DIR__ . '/../..' . '/core/options/taxonomies/taxonomies.php',
        'Devmonsta\\Rest' => __DIR__ . '/../..' . '/core/rest.php',
        'Devmonsta\\Traits\\Singleton' => __DIR__ . '/../..' . '/core/traits/singleton.php',
        'Theme_Customize_Repeater_Control' => __DIR__ . '/../..' . '/core/options/customizer/libs/customize-repeater-control.php',
        'Theme_Customize_Repeater_Popup_Control' => __DIR__ . '/../..' . '/core/options/customizer/libs/customize-repeater-control-popup.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc286e51aee7ccaf235821570d4bd7ed5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc286e51aee7ccaf235821570d4bd7ed5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc286e51aee7ccaf235821570d4bd7ed5::$classMap;

        }, null, ClassLoader::class);
    }
}
