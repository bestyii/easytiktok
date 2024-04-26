<?php

namespace EasyTiktok\OpenPlatform;

use EasyTiktok\Kernel\ServiceContainer;
use EasyTiktok\Kernel\Traits\ResponseCastable;

/**
 * Class Application.
 * @property Auth\AccessToken $access_token
 * @property Auth\Client $auth
 * @property \Overtrue\Socialite\Providers\DouYin $oauth
 * @property Base\Client $base
 * @property UserData\Client $user_data
 * @property FansData\Client $fans_data
 * @property Video\Client $video
 * @property VideoData\Client $video_data
 * @author zhaoxiang <zhaoxiang051405@gmail.com>
 */
class Application extends ServiceContainer
{

    use ResponseCastable;

    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        OAuth\ServiceProvider::class,
        FansData\ServiceProvider::class,
        UserData\ServiceProvider::class,
        Video\ServiceProvider::class,
        VideoData\ServiceProvider::class,
        Base\ServiceProvider::class
    ];

    /**
     * 初始化开放平台的基础接口
     * @var array|\string[][]
     */
    protected $defaultConfig = [
        'http' => [
            'base_uri' => 'https://open.douyin.com/',
        ],
    ];

    /**
     * Handle dynamic calls.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->base->$method(...$args);
    }
}
