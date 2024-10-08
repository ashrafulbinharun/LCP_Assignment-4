<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcd37ad9ad66c87123e5fee140f595095
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcd37ad9ad66c87123e5fee140f595095::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcd37ad9ad66c87123e5fee140f595095::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcd37ad9ad66c87123e5fee140f595095::$classMap;

        }, null, ClassLoader::class);
    }
}
