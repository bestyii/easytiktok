<?php

namespace EasyTiktok\MiniProgram\Server;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple): void {
        !isset($pimple['encryptor']) && $pimple['encryptor'] = static function($app) {
            return new Encryptor(
                $app['config']['app_id'],
                $app['config']['token'],
                $app['config']['aes_key']
            );
        };
    }
}
