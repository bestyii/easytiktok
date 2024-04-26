<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\MiniProgram\QrCode;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * QrCode ServiceProvider.
 *
 * @author dysodeng <dysodengs@gmail.com>
 */
class ServiceProvider implements ServiceProviderInterface {
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple): void {
        $pimple['qr_code'] = static function($app) {
            return new Client($app);
        };
    }
}
