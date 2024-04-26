<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\MiniProgram\Pay;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple): void {
        $pimple['pay'] = static function($app) {
            return new Client($app);
        };
        $pimple['pay_server'] = static function($app) {
            return new Server($app);
        };
    }
}
