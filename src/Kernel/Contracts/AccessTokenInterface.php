<?php
/**
 *
 * @since   2021-11-01
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace EasyTiktok\Kernel\Contracts;

use Psr\Http\Message\RequestInterface;

/**
 * Interface AuthorizerAccessToken.
 *
 * @author overtrue <i@overtrue.me>
 */
interface AccessTokenInterface {
    /**
     * @return array
     */
    public function getToken(): array;

    /**
     * @return AccessTokenInterface
     */
    public function refresh(): self;

    /**
     * @param RequestInterface $request
     * @param array $requestOptions
     *
     * @return RequestInterface
     */
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface;


    /**
     * @param RequestInterface $request
     * @param array $requestOptions
     *
     * @return RequestInterface
     */
    public function applyToPostRequest(RequestInterface $request, array $requestOptions = []): RequestInterface;
}
