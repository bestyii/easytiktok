<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\OpenPlatform\UserData;

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
     * 获取用户视频情况
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws HttpException
     * @throws InvalidConfigException
     * @throws GuzzleException
     */
    public function item(int $date_type = 7) {
        return $this->httpGet('data/external/user/item/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户粉丝数
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function fans(int $date_type = 7) {
        return $this->httpGet('data/external/user/fans/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户点赞数
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function like(int $date_type = 7) {
        return $this->httpGet('data/external/user/like/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户评论数
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function comment(int $date_type = 7) {
        return $this->httpGet('data/external/user/comment/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户分享数
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function share(int $date_type = 7) {
        return $this->httpGet('data/external/user/share/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户主页访问数
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function profile(int $date_type = 7) {
        return $this->httpGet('data/external/user/profile/', ['date_type' => $date_type]);
    }

}
