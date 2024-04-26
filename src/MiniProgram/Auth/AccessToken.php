<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\MiniProgram\Auth;

use EasyTiktok\MiniProgram\Kernel\AccessToken as BaseAccessToken;

/**
 * Class AccessToken.
 *
 * @author zhaoxiang <zhaoxiang051405@gmail.com>
 */
class AccessToken extends BaseAccessToken {

    /**
     * @return array
     */
    protected function getCredentials(): array {
        return [
            'appid'      => $this->app['config']['app_id'],
            'secret'     => $this->app['config']['secret'],
            'grant_type' => 'client_credential',
        ];
    }
}
