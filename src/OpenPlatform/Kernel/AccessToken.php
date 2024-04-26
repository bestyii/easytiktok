<?php

/**
 * This file is part of the apiadmin/tiktok.
 */

namespace EasyTiktok\OpenPlatform\Kernel;

use EasyTiktok\Kernel\Contracts\AccessTokenInterface;
use EasyTiktok\Kernel\Exceptions\AccessTokenException;
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
    protected $endpointToGetToken;

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
    protected $endpointToRenewRefresh = 'oauth/renew_refresh_token/';

    /**
     * @var string
     */
    protected $endpointToRefresh = 'oauth/refresh_token/';

    /**
     * @var string
     */
    protected $refreshKey = 'refresh_token';

    /**
     * @var string
     */
    protected $cachePrefix = 'EasyTiktok.open_platform.';

    /**
     * @var string 用户授权后的ticket
     */
    protected $code;

    /**
     * @var string 用户唯一标识
     */
    protected $openid;

    /**
     * @var int 最大允许刷新RefreshToken的次数
     */
    protected $refreshReTokenLimit = 5;

    /**
     * AccessToken constructor.
     *
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app) {
        $this->app = $app;
        $this->openid = $app['config']['openid'];
    }

    /**
     * 设置授权的Code
     * @param string $code
     * @return $this
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function setCode(string $code): AccessToken {
        $this->code = $code;

        return $this;
    }

    /**
     * 设置用户的Openid
     * @param $openid
     * @return $this
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function setOpenid($openid): AccessToken {
        $this->openid = $openid;

        return $this;
    }

    /**
     * 更新授权信息
     * @param $code
     * @return array
     * @throws AccessTokenException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws RuntimeException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function setAuth($code): array {
        if (empty($code) && empty($this->code)) {
            throw new AccessTokenException('code cannot be empty!');
        } else {
            if (empty($code)) {
                $code = $this->code;
            }
            /** @var array $token */
            $token = $this->requestToken($this->getCredentials($code), true);
            if (empty($this->openid)) {
                $this->setOpenid($token['open_id']);
            }
            $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 1296000);
            $this->setReToken($token[$this->refreshKey], $token['refresh_expires_in'] ?? 2592000);

            return $token;
        }
    }

    /**
     *
     * @return array
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException|AccessTokenException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getToken(): array {
        if (empty($this->openid)) {
            throw new AccessTokenException('missing openid parameter');
        }

        $cacheKey = $this->getCacheKey($this->tokenKey);
        $cache = $this->getCache();

        if ($cache->has($cacheKey) && $result = $cache->get($cacheKey)) {
            $token = $result;
        } else {
            $reCacheKey = $this->getCacheKey($this->refreshKey);
            if ($cache->has($reCacheKey) && $reResult = $cache->get($reCacheKey)) {
                $token = $this->refreshToken($reResult);
            } else {
                throw new AccessTokenException('token is timeout!');
            }
        }

        return $token;
    }

    /**
     * 强制刷新AccessToken
     * @return AccessTokenInterface
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException|AccessTokenException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function refresh(): AccessTokenInterface {
        $this->getCache()->delete($this->getCacheKey($this->tokenKey));
        $this->getToken();

        return $this;
    }

    /**
     * 刷新Token
     * @param $reResult
     * @return mixed
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException|AccessTokenException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function refreshToken($reResult) {
        $now = time();
        // 判断ReToken是否需要刷新
        if ($reResult['refresh_deadline'] <= $now) {
            $this->refreshReToken($reResult);
        }

        $response = $this->setHttpClient($this->app['http_client'])->request(
            $this->endpointToRefresh, 'POST', [
                'multipart' => $this->getPostFormData([
                    'client_key'      => $this->app['config']['app_id'],
                    'grant_type'      => 'refresh_token',
                    $this->refreshKey => $reResult[$this->refreshKey]
                ])
            ]
        );
        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result['data'][$this->tokenKey])) {
            throw new AccessTokenException('Refresh access_token fail: ' . json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        $this->setToken($result['data'][$this->tokenKey], $token['expires_in'] ?? 1296000);

        return $result['data'];

    }

    /**
     * 更新ReFreshToken
     * @param $reResult
     * @throws AccessTokenException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function refreshReToken($reResult): void {
        if ($reResult['re_times'] >= $this->refreshReTokenLimit) {
            throw new AccessTokenException('refresh token can not be used');
        }

        $response = $this->setHttpClient($this->app['http_client'])->request(
            $this->endpointToRenewRefresh, 'POST', [
                'multipart' => $this->getPostFormData([
                    'client_key'    => $this->app['config']['app_id'],
                    'refresh_token' => $reResult[$this->refreshKey]
                ])
            ]
        );
        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result['data'][$this->refreshKey])) {
            throw new HttpException('Renew refresh_token fail: ' . json_encode($result, JSON_UNESCAPED_UNICODE), $response);
        }
        $this->setReToken($result['data'][$this->refreshKey], $token['expires_in'] ?? 2592000, $reResult['re_times'] + 1);
    }

    /**
     * 构建post数据
     * @param array $credentials
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function getPostFormData(array $credentials): array {
        $multipartData = [];
        foreach ($credentials as $key => $value) {
            $multipartData[] = [
                'name'     => $key,
                'contents' => $value
            ];
        }

        return $multipartData;
    }

    /**
     *
     * @param string $token
     * @param int $lifetime
     * @param int $times
     * @return AccessTokenInterface
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function setReToken(string $token, int $lifetime, int $times = 0): AccessTokenInterface {
        $this->getCache()->set($this->getCacheKey($this->refreshKey), [
            're_times'         => $times,
            $this->refreshKey  => $token,
            'refresh_deadline' => time() + $lifetime - 86400
        ], $lifetime);

        if (!$this->getCache()->has($this->getCacheKey($this->tokenKey))) {
            throw new RuntimeException('Failed to cache access token.');
        }

        return $this;
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
    public function setToken(string $token, int $lifetime): AccessTokenInterface {
        $this->getCache()->set($this->getCacheKey($this->tokenKey), [
            $this->tokenKey => $token,
            'expires_in'    => $lifetime
        ], $lifetime - 86400);

        if (!$this->getCache()->has($this->getCacheKey($this->tokenKey))) {
            throw new RuntimeException('Failed to cache access token.');
        }

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
        $response = $this->setHttpClient($this->app['http_client'])->request(
            $this->getEndpoint(), 'POST', [
                'multipart' => $this->getPostFormData($credentials)
            ]
        );
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
     * @throws InvalidArgumentException
     * @throws RuntimeException|GuzzleException|AccessTokenException
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
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException|AccessTokenException
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
     * 获取access_token的key
     * @param string $type
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function getCacheKey(string $type): string {
        return $this->cachePrefix . $type . '.' . md5(json_encode($this->getCredentials($this->openid)));
    }

    /**
     * The request query will be used to add to the request.
     *
     * @return array
     *
     * @throws HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws RuntimeException|GuzzleException|AccessTokenException
     */
    protected function getQuery(): array {
        return [
            $this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey],
            'open_id'                           => $this->openid
        ];
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
     * @param string $code
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    abstract protected function getCredentials(string $code): array;
}
