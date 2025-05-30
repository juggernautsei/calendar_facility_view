<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3d0e09e2723d22f95b8d532e49d81ea4
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Juggernaut\\Dexcom\\Module\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Juggernaut\\Dexcom\\Module\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3d0e09e2723d22f95b8d532e49d81ea4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3d0e09e2723d22f95b8d532e49d81ea4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3d0e09e2723d22f95b8d532e49d81ea4::$classMap;

        }, null, ClassLoader::class);
    }
}
