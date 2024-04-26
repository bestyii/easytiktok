<?php

namespace EasyTiktok\Kernel;

use EasyTiktok\Kernel\Contracts\AccessTokenInterface;
use EasyTiktok\Kernel\Exceptions\BadRequestException;
use EasyTiktok\Kernel\Exceptions\InvalidArgumentException;
use EasyTiktok\Kernel\Http\Response;
use EasyTiktok\Kernel\Support\Collection;
use EasyTiktok\Kernel\Traits\HasHttpRequests;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Closure;

/**
 * Class BaseClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class BaseClient {

    use HasHttpRequests {
        request as performRequest;
    }

    /**
     * @var ServiceContainer
     */
    protected $app;
    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var bool 是否需要传递AccessToken
     */
    protected $needAccessToken = true;

    /**
     * @var bool
     */
    protected $postAccessToken = true;

    /**
     * @var AccessTokenInterface
     */
    private $accessToken;

    /**
     * BaseClient constructor.
     * @param ServiceContainer $app
     * @param null $accessToken
     */
    public function __construct(ServiceContainer $app, $accessToken = null) {
        $this->app = $app;
        $this->accessToken = $accessToken ?? $this->app['access_token'];
    }

    /**
     * @param string $url
     * @param array $query
     * @return array|Collection|object|ResponseInterface|string
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException
     */
    public function httpGet(string $url, array $query = []) {
        return $this->request($url, 'GET', ['query' => $query]);
    }

    /**
     * @param string $url
     * @param array $data
     * @return array|Collection|object|ResponseInterface|string
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException
     */
    public function httpPost(string $url, array $data = []) {
        return $this->request($url, 'POST', ['form_params' => $data]);
    }

    /**
     * @param string $url
     * @param array $data
     * @return array|Collection|object|ResponseInterface|string
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException
     */
    public function httpPostFormData(string $url, array $data = []) {
        $multipartData = [];
        foreach ($data as $key => $value) {
            $multipartData[] = [
                'name'     => $key,
                'contents' => $value
            ];
        }

        return $this->request($url, 'POST', ['multipart' => $multipartData]);
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $query
     * @return array
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException
     */
    public function httpPostJson(string $url, array $data = [], array $query = []): array {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    /**
     * 分片上传文件
     * @param string $url
     * @param string $file
     * @param array $query
     * @param int|float $chunkSize 每个分片的大小，不建议修改
     * @return array
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException|BadRequestException|InvalidArgumentException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function httpChunkUpload(string $url, string $file, array $query = [], int $chunkSize = 1024 * 1024 * 10): array {
        $result = [];
        $fh = Utils::tryFopen($file, 'rb');
        $filesize = filesize($file);
        if ($filesize <= 1024 * 1024 * 5) {
            throw new InvalidArgumentException('The file size cannot be less than 5M');
        }
        $chunkNum = (int)round($filesize / $chunkSize);

        rewind($fh);
        $chunkIndex = 1;
        $tempPart = md5($query['upload_id']) . '.part';
        while ($chunkIndex <= $chunkNum) {
            $left = $filesize - ($chunkIndex - 1) * $chunkSize;
            $tempPartFile = dirname($file) . DIRECTORY_SEPARATOR . $tempPart . $chunkIndex;
            if (!is_writable($tempPartFile)) {
                throw new InvalidArgumentException("{$tempPartFile} can not be write");
            }

            file_put_contents($tempPartFile, $chunkIndex === $chunkNum ? fread($fh, $left) : fread($fh, $chunkSize));
            $multipart = [
                [
                    'name'     => 'video',
                    'contents' => fopen($tempPartFile, 'rb')
                ]
            ];
            $query['part_number'] = $chunkIndex;
            $response = $this->request(
                $url,
                'POST',
                ['query' => $query, 'multipart' => $multipart, 'connect_timeout' => 300, 'timeout' => 300, 'read_timeout' => 300]
            );
            if ($response['data']['error_code'] !== 0) {
                throw new BadRequestException($response['data']['description']);
            }
            @unlink($tempPartFile);
            $result[$chunkIndex] = $response;
            $chunkIndex++;
        }

        return $result;
    }

    /**
     * @param string $url
     * @param array $files
     * @param array $form
     * @param array $query
     * @return array|Collection|object|ResponseInterface|string
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException
     */
    public function httpUpload(string $url, array $files = [], array $form = [], array $query = []) {
        $multipart = [];
        $headers = [];

        if (isset($form['filename'])) {
            $headers = [
                'Content-Disposition' => 'form-data; name="media"; filename="' . $form['filename'] . '"'
            ];
        }

        foreach ($files as $name => $path) {
            $multipart[] = [
                'name'     => $name,
                'contents' => fopen($path, 'r'),
                'headers'  => $headers
            ];
        }

        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        return $this->request(
            $url,
            'POST',
            ['query' => $query, 'multipart' => $multipart, 'connect_timeout' => 300, 'timeout' => 300, 'read_timeout' => 300]
        );
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * @param AccessTokenInterface $accessToken
     *
     * @return $this
     */
    public function setAccessToken(AccessTokenInterface $accessToken): BaseClient {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     *
     * @param string $url
     * @param string $method
     * @param array $options
     * @param false $returnRaw
     * @return array|Collection|object|ResponseInterface|string
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function request(string $url, string $method = 'GET', array $options = [], bool $returnRaw = false) {
        if (empty($this->middlewares)) {
            $this->registerHttpMiddlewares();
        }
        $response = $this->performRequest($url, $method, $options);
        $this->app->events->dispatch(new Events\HttpResponseCreated($response));

        return $returnRaw ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @return Response
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException
     */
    public function requestRaw(string $url, string $method = 'GET', array $options = []): Response {
        return Response::buildFromPsrResponse($this->request($url, $method, $options, true));
    }

    /**
     * Register Guzzle middlewares.
     */
    protected function registerHttpMiddlewares(): void {
        // retry
        $this->pushMiddleware($this->retryMiddleware(), 'retry');
        // access token
        if ($this->needAccessToken) {
            $this->pushMiddleware($this->accessTokenMiddleware(), 'access_token');
        }
    }

    /**
     * Attache access token to request query.
     *
     * @return Closure
     */
    protected function accessTokenMiddleware(): Closure {
        return function(callable $handler) {
            return function(RequestInterface $request, array $options) use ($handler) {
                if ($this->accessToken) {
                    if ($this->postAccessToken) {
                        $request = $this->accessToken->applyToPostRequest($request, $options);
                    } else {
                        $request = $this->accessToken->applyToRequest($request, $options);
                    }
                }

                return $handler($request, $options);
            };
        };
    }

    /**
     * Return retry middleware.
     *
     * @return Closure
     */
    protected function retryMiddleware(): Closure {
        return Middleware::retry(
            function($retries, RequestInterface $request, ResponseInterface $response = null) {
                if ($retries < $this->app->config->get('http.max_retries', 1) && $response && $body = $response->getBody()) {
                    $response = json_decode($body, true);
                    if (
                        (!empty($response['errcode']) && in_array(abs($response['errcode']), [40002], true)) ||
                        (!empty($response['extra']['error_code']) && in_array(abs($response['extra']['error_code']), [2190008, 2190002, 10008], true))
                    ) {
                        $this->accessToken->refresh();

                        return true;
                    }
                }

                return false;
            },
            function() {
                return abs($this->app->config->get('http.retry_delay', 500));
            }
        );
    }
}
