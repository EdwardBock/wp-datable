<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1a9428702e81046ffb3ec9efd8a56cd5
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Palasthotel\\WordPress\\Datable\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Palasthotel\\WordPress\\Datable\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static $classMap = array (
        'Palasthotel\\WordPress\\Datable\\Database' => __DIR__ . '/../..' . '/classes/Database.php',
        'Palasthotel\\WordPress\\Datable\\Datable' => __DIR__ . '/../..' . '/classes/Datable.php',
        'Palasthotel\\WordPress\\Datable\\WPQueryExtension' => __DIR__ . '/../..' . '/classes/WPQueryExtension.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1a9428702e81046ffb3ec9efd8a56cd5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1a9428702e81046ffb3ec9efd8a56cd5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1a9428702e81046ffb3ec9efd8a56cd5::$classMap;

        }, null, ClassLoader::class);
    }
}
