<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyTiktok\Kernel\Exceptions;

use EasyTiktok\Kernel\Support\Collection;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpException.
 *
 * @author overtrue <i@overtrue.me>
 */
class HttpException extends Exception {
    /**
     * @var ResponseInterface|null
     */
    public $response;

    /**
     * @var ResponseInterface|Collection|array|object|string|null
     */
    public $formattedResponse;

    /**
     * HttpException constructor.
     *
     * @param string $message
     * @param ResponseInterface|null $response
     * @param null $formattedResponse
     * @param int|null $code
     */
    public function __construct($message, ResponseInterface $response = null, $formattedResponse = null, int $code = null) {
        parent::__construct($message, $code);

        $this->response = $response;
        $this->formattedResponse = $formattedResponse;

        if ($response) {
            $response->getBody()->rewind();
        }
    }
}
