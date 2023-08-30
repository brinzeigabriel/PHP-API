<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbe0e982a5ac9943aeb9dc91f8b92543c
{
    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/../..' . '/src',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->fallbackDirsPsr4 = ComposerStaticInitbe0e982a5ac9943aeb9dc91f8b92543c::$fallbackDirsPsr4;
            $loader->classMap = ComposerStaticInitbe0e982a5ac9943aeb9dc91f8b92543c::$classMap;

        }, null, ClassLoader::class);
    }
}
