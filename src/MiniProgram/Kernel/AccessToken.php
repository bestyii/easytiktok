<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\MiniProgram\Kernel;

use EasyTiktok\Kernel\Contracts\AccessTokenInterface;
use EasyTiktok\Kernel\Exceptions\HttpException;
use EasyTiktok\Kernel\Exceptions\InvalidArgumentException;
use EasyTiktok\Kernel\Exceptions\InvalidConfigException;
use EasyTiktok\Kernel\Exceptions\RuntimeException;
use EasyTiktok\Kernel\ServiceContainer;
use EasyTiktok\Kernel\Support\Collection;
use EasyTiktok\Kernel\Traits\HasHttpRequests;
use EasyTiktok\Kernel\Traits\InteractsWithCache;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AccessToken.
 *
 * @author overtrue <i@overtrue.me>
 */
abstract class AccessToken implements AccessTokenInterface {
    use HasHttpRequests;
    use InteractsWithCache;

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var string
     */
    protected $requestMethod = 'POST';

    /**
     * @var string
     */
    protected $endpointToGetToken = 'apps/v2/token';

    /**
     * @var string
     */
    protected $queryName;

    /**
     * @var array
     */
    protected $token;

    /**
     * @var string
     */
    protected $tokenKey = 'access_token';

    /**
     * @var string
     */
    protected $cachePrefix = 'EasyTiktok.mini_program.access_token.';

    /**
     * AccessToken constructor.
     *
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app) {
        $this->app = $app;
    }

    /**
     * @param bool $refresh
     *
     * @return array
     *
     * @throws HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function getToken(bool $refresh = false): array {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        if (!$refresh && $cache->has($cacheKey) && $result = $cache->get($cacheKey)) {
            return $result;
        }

        /** @var array $token */
        $token = $this->requestToken($this->getCredentials(), true);
        $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 7200);

        return $token;
    }

    /**
     * @param string $token
     * @param int $lifetime
     *
     * @return AccessTokenInterface
     *
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException|InvalidArgumentException
     */
    public function setToken(string $token, int $lifetime = 7200): AccessTokenInterface {
        $this->getCache()->set($this->getCacheKey(), [
            $this->tokenKey => $token,
            'expires_in'    => $lifetime
        ], $lifetime);

        if (!$this->getCache()->has($this->getCacheKey())) {
            throw new RuntimeException('Failed to cache access token.');
        }

        return $this;
    }

    /**
     * @return AccessTokenInterface
     *
     * @throws HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     * @throws RuntimeException|GuzzleException
     */
    public function refresh(): AccessTokenInterface {
        $this->getToken(true);

        return $this;
    }

    /**
     * @param array $credentials
     * @param bool $toArray
     *
     * @return ResponseInterface|Collection|array|object|string
     *
     * @throws HttpException
     * @throws InvalidConfigException
     * @throws InvalidArgumentException|GuzzleException
     */
    public function requestToken(array $credentials, bool $toArray) {
        $response = $this->sendRequest($credentials);
        $result = json_decode($response->getBody()->getContents(), true);
        $formatted = $this->castResponseToType($response, $this->app['config']->get('response_type'));

        if (empty($result['data'][$this->tokenKey])) {
            throw new HttpException('Request access_token fail: ' . json_encode($result, JSON_UNESCAPED_UNICODE), $response, $formatted);
        }

        return $toArray ? $result['data'] : $formatted;
    }

    /**
     * @param RequestInterface $request
     * @param array $requestOptions
     *
     * @return RequestInterface
     *
     * @throws HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     * @throws RuntimeException|GuzzleException
     */
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface {
        parse_str($request->getUri()->getQuery(), $query);

        $query = http_build_query(array_merge($this->getQuery(), $query));

        return $request->withUri($request->getUri()->withQuery($query));
    }

    /**
     * 处理Post添加AccessToken
     * @param RequestInterface $request
     * @param array $requestOptions
     * @return RequestInterface
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function applyToPostRequest(RequestInterface $request, array $requestOptions = []): RequestInterface {
        $query = $request->getBody()->getContents();
        $request->getBody()->rewind();
        $query = \GuzzleHttp\json_decode($query, true);
        $query = array_merge($this->getQuery(), $query);

        return $request->withBody(Utils::streamFor(json_encode($query)));
    }

    /**
     * Send http request.
     *
     * @param array $credentials
     *
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    protected function sendRequest(array $credentials): ResponseInterface {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : 'json' => $credentials,
        ];

        return $this->setHttpClient($this->app['http_client'])->request($this->getEndpoint(), $this->requestMethod, $options);
    }

    /**
     *
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function getCacheKey(): string {
        return $this->cachePrefix . md5(json_encode($this->getCredentials()));
    }

    /**
     * The request query will be used to add to the request.
     *
     * @return array
     *
     * @throws HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     * @throws RuntimeException|GuzzleException
     */
    protected function getQuery(): array {
        return [$this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey]];
    }

    /**
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getEndpoint(): string {
        if (empty($this->endpointToGetToken)) {
            throw new InvalidArgumentException('No endpoint for access token request.');
        }

        return $this->endpointToGetToken;
    }

    /**
     * Credential for get token.
     *
     * @return array
     */
    abstract protected function getCredentials(): array;
}
