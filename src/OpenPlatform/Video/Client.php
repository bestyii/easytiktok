<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\OpenPlatform\Video;

use EasyTiktok\Kernel\BaseClient;
use EasyTiktok\Kernel\Exceptions\BadRequestException;
use EasyTiktok\Kernel\Exceptions\HttpException;
use EasyTiktok\Kernel\Exceptions\InvalidArgumentException;
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
     * 视频上传
     * @param string $filePath
     * @return array|Collection|object|ResponseInterface|string
     * @throws HttpException
     * @throws InvalidConfigException
     * @throws GuzzleException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function uploadVideo(string $filePath) {
        return $this->httpUpload('video/upload/', ['video' => $filePath]);
    }

    /**
     * 分片上传视频
     * @param string $filePath
     * @return array
     * @throws BadRequestException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException|InvalidArgumentException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function partUpload(string $filePath): array {
        $initInfo = $this->httpPostJson('video/part/init/');
        if ($initInfo['data']['error_code'] !== 0) {
            throw new BadRequestException($initInfo['data']['description']);
        }

        $this->httpChunkUpload('video/part/upload/', $filePath, [
            'upload_id' => $initInfo['data']['upload_id']
        ]);

        return $this->httpPostJson('video/part/complete/', [], [
            'upload_id' => $initInfo['data']['upload_id']
        ]);
    }

    /**
     * 创建视频
     * @param string $videoId
     * @param array $body
     * @return array
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function createVideo(string $videoId, array $body = []): array {
        $body['video_id'] = $videoId;

        return $this->httpPostJson('video/create/', $body);
    }

    /**
     * 获取用户视频列表
     * @param int $cursor
     * @param int $count
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function getList(int $cursor, int $count = 10) {
        return $this->httpGet('video/list/', [
            'cursor' => $cursor,
            'count'  => $count
        ]);
    }

    /**
     * 查询特定视频数据
     * @param array $item_ids
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function getData(array $item_ids) {
        return $this->httpPost('video/data/', $item_ids);
    }
}
