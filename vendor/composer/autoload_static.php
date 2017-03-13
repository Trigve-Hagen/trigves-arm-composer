<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitff673063754c86cc091246f056c7a05b
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Trigves\\Arm\\' => 12,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Trigves\\Arm\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/trigves/arm/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitff673063754c86cc091246f056c7a05b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitff673063754c86cc091246f056c7a05b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}