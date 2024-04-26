<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyTiktok\Kernel\Events;

use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpResponseCreated.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class HttpResponseCreated {
    /**
     * @var ResponseInterface
     */
    public $response;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response) {
        $this->response = $response;
    }
}
