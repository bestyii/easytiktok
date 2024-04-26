<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\MiniProgram\Order;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple): void {
        $pimple['order'] = static function($app) {
            return new Client($app);
        };
    }
}
