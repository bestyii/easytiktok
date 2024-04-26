<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyTiktok\MiniProgram\Auth;

use EasyTiktok\Kernel\BaseClient;
use EasyTiktok\Kernel\Exceptions\HttpException;
use EasyTiktok\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Auth.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient {

    protected $needAccessToken = false;

    /**
     * Get session info by code.
     * @param string $code
     * @param string $anonymousCode
     * @return array
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function session(string $code, string $anonymousCode = ''): array {
        $params = [
            'appid'          => $this->app['config']['app_id'],
            'secret'         => $this->app['config']['secret'],
            'code'           => $code,
            'anonymous_code' => $anonymousCode
        ];

        return $this->httpPostJson('apps/v2/jscode2session', $params);
    }
}
