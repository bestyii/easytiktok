<?php

namespace EasyTiktok\MiniProgram;

use EasyTiktok\Kernel\ServiceContainer;
use EasyTiktok\Kernel\Traits\ResponseCastable;
use EasyTiktok\MiniProgram\Pay\Server as PayServer;

/**
 * Class Application.
 * @property Base\Client $base
 * @property Auth\AccessToken $access_token
 * @property Auth\Client $auth
 * @property QrCode\Client $qr_code
 * @property Server\Encryptor $encryptor
 * @property Pay\Client $pay
 * @property PayServer $pay_server
 * @property Order\Client $order
 * @author zhaoxiang <zhaoxiang051405@gmail.com>
 */
class Application extends ServiceContainer {

    use ResponseCastable;

    /**
     * @var array
     */
    protected $providers = [
        Base\ServiceProvider::class,
        Auth\ServiceProvider::class,
        QrCode\ServiceProvider::class,
        Server\ServiceProvider::class,
        Pay\ServiceProvider::class,
        Order\ServiceProvider::class,
    ];

    /**
     * 初始化小程序的基础接口
     * @var array|\string[][]
     */
    protected $defaultConfig = [
        'http' => [
            'base_uri' => 'https://developer.toutiao.com/api/',
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
    public function __call(string $method, array $args) {
        return $this->base->$method(...$args);
    }
}
