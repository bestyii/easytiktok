<?php

namespace EasyTiktok\Kernel;

use EasyTiktok\Kernel\Providers\CacheServiceProvider;
use EasyTiktok\Kernel\Providers\ConfigServiceProvider;
use EasyTiktok\Kernel\Providers\EventDispatcherServiceProvider;
use EasyTiktok\Kernel\Providers\HttpClientServiceProvider;
use EasyTiktok\Kernel\Providers\RequestServiceProvider;
use GuzzleHttp\Client;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @property Config $config
 * @property Client $http_client
 * @property EventDispatcher $events
 * @since   2021-11-01
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */
class ServiceContainer extends Container {
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $defaultConfig = [];

    /**
     * @var array
     */
    protected $userConfig = [];

    /**
     * Constructor.
     *
     * @param array $config
     * @param array $prepends
     * @param string|null $id
     */
    public function __construct(array $config = [], array $prepends = [], string $id = '') {
        $this->userConfig = $config;
        parent::__construct($prepends);
        $this->registerProviders($this->getProviders());
        $this->id = $id;
        $this->events->dispatch(new Events\ApplicationInitialized($this));
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id ?? $this->id = md5(json_encode($this->userConfig));
    }

    /**
     * @return array
     */
    public function getConfig(): array {
        $base = [
            'http' => [
                'timeout' => 30.0
            ]
        ];

        return array_replace_recursive($base, $this->defaultConfig, $this->userConfig);
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders(): array {
        return array_merge([
            CacheServiceProvider::class,
            ConfigServiceProvider::class,
            HttpClientServiceProvider::class,
            RequestServiceProvider::class,
            EventDispatcherServiceProvider::class
        ], $this->providers);
    }

    /**
     * @param string $id
     * @param mixed $value
     */
    public function rebind(string $id, $value): void {
        $this->offsetUnset($id);
        $this->offsetSet($id, $value);
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get(string $id) {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed $value
     */
    public function __set(string $id, $value) {
        $this->offsetSet($id, $value);
    }

    /**
     * Just For ide
     * @param string $id
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function __isset(string $id) {

    }

    /**
     *
     * @param array $providers
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function registerProviders(array $providers): void {
        foreach ($providers as $provider) {
            $this->register(new $provider());
        }
    }
}