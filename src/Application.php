<?php
/**
 * This file is part of the apiadmin/tiktok.
 *
 * (c) zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace EasyTiktok;

/**
 * @method static MiniProgram\Application  miniProgram(array $config)
 * @method static OpenPlatform\Application  openPlatform(array $config)
 */
class Application {
    /**
     * @param string $name
     * @param array $config
     *
     * @return mixed
     */
    public static function make(string $name, array $config) {
        $namespace = Kernel\Support\Str::studly($name);
        $application = "\\EasyTiktok\\{$namespace}\\Application";
        return new $application($config);
    }

    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments) {
        return self::make($name, ...$arguments);
    }
}