<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf6a03b43c026985e98b856c57e615d5a
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LogonLabs\\' => 10,
        ),
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LogonLabs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/LogonLabs',
        ),
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf6a03b43c026985e98b856c57e615d5a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf6a03b43c026985e98b856c57e615d5a::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
