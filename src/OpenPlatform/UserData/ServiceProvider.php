<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\OpenPlatform\UserData;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author zhaoxiang <zhaoxiang051405@gmail.com>
 */
class ServiceProvider implements ServiceProviderInterface {
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple): void {
        $pimple['user_data'] = static function($app) {
            return new Client($app);
        };
    }
}
