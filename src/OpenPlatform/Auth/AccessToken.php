<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\OpenPlatform\Auth;

use EasyTiktok\OpenPlatform\Kernel\AccessToken as BaseAccessToken;

/**
 * AccessToken 【用于开放平台获取授权AccessToken】
 *
 * @author zhaoxiang <zhaoxiang051405@gmail.com>
 */
class AccessToken extends BaseAccessToken {

    /**
     * @var string
     */
    protected $endpointToGetToken = 'oauth/access_token/';

    /**
     * 配置AccessToken的
     * @param string $code
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function getCredentials(string $code): array {
        return [
            'client_key'    => $this->app['config']['app_id'],
            'client_secret' => $this->app['config']['secret'],
            'grant_type'    => 'authorization_code',
            'code'          => $code,
        ];
    }
}
