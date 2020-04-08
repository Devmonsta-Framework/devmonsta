<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit678b69e97bc75a31559092d5ed937d64
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

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit678b69e97bc75a31559092d5ed937d64::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit678b69e97bc75a31559092d5ed937d64::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
