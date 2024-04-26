<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\OpenPlatform\VideoData;

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
     * 获取视频基础数据
     * @param string $item
     * @return array|Collection|object|ResponseInterface|string
     * @throws HttpException
     * @throws InvalidConfigException
     * @throws GuzzleException
     */
    public function base(string $item) {
        return $this->httpGet('data/external/item/base/', ['item_id' => $item]);
    }

    /**
     * 获取视频点赞数
     * @param string $item
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function like(string $item, int $date_type = 7) {
        return $this->httpGet('data/external/item/like/', ['item_id' => $item, 'date_type' => $date_type]);
    }

    /**
     * 获取视频评论数
     * @param string $item
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function comment(string $item, int $date_type = 7) {
        return $this->httpGet('data/external/item/comment/', ['item_id' => $item, 'date_type' => $date_type]);
    }

    /**
     * 获取视频分享数
     * @param string $item
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function share(string $item, int $date_type = 7) {
        return $this->httpGet('data/external/item/share/', ['item_id' => $item, 'date_type' => $date_type]);
    }

    /**
     * 获取视频播放数
     * @param string $item
     * @param int $date_type
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function play(string $item, int $date_type = 7) {
        return $this->httpGet('data/external/item/play/', ['item_id' => $item, 'date_type' => $date_type]);
    }

}
