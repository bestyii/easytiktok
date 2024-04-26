<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\OpenPlatform\FansData;

use EasyTiktok\Kernel\BaseClient;
use EasyTiktok\Kernel\Exceptions\HttpException;
use EasyTiktok\Kernel\Exceptions\InvalidConfigException;
use EasyTiktok\Kernel\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client.
 *
 * @author zhaoxiang <zhaoxiang051405@gmail.com>
 */
class Client extends BaseClient {

    protected $postAccessToken = false;

    /**
     * 获取用户粉丝数据
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function data() {
        return $this->httpGet('fans/data/');
    }

}
