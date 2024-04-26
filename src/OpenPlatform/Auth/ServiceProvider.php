<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\OpenPlatform\Auth;

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
        !isset($pimple['access_token']) && $pimple['access_token'] = static function($app) {
            return new AccessToken($app);
        };

        !isset($pimple['auth']) && $pimple['auth'] = static function($app) {
            return new Client($app);
        };
    }
}
