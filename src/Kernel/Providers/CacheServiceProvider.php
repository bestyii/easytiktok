<?php

namespace EasyTiktok\Kernel\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * Class CacheServiceProvider.
 * 支持多样的缓存配置，在配置中新增cache字段，具体配置见文档。项目建议使用Redis做缓存，可以提升效率。
 */
class CacheServiceProvider implements ServiceProviderInterface {
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple): void {
        !isset($pimple['cache']) && $pimple['cache'] = static function($app) {
            $config = $app->getConfig();
            if (!empty($config['cache']) && !empty($config['cache']['type'])) {
                switch (strtolower($config['cache']['type'])) {
                    case "redis":
                        $dsn = 'redis://';
                        if (!empty($config['cache']['password'])) {
                            $dsn .= "{$config['cache']['password']}@";
                        }
                        if (!empty($config['cache']['host'])) {
                            $dsn .= $config['cache']['host'];
                        } else {
                            $dsn .= '127.0.0.1';
                        }
                        if (!empty($config['cache']['port'])) {
                            $dsn .= ":{$config['cache']['port']}";
                        } else {
                            $dsn .= ":6379";
                        }
                        if (!empty($config['cache']['select'])) {
                            $dsn .= "/{$config['cache']['select']}";
                        }
                        if (empty($config['cache']['prefix'])) {
                            $config['cache']['prefix'] = 'EasyTiktok';
                        }

                        return new RedisAdapter(RedisAdapter::createConnection($dsn), $config['cache']['prefix']);
                    default:
                        return new FilesystemAdapter('EasyTiktok', 1500);
                }
            } else {
                return new FilesystemAdapter('EasyTiktok', 1500);
            }
        };
    }
}
