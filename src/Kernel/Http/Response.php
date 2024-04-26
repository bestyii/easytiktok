<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyTiktok\Kernel\Http;

use EasyTiktok\Kernel\Exceptions\HttpException;
use EasyTiktok\Kernel\Support\Collection;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response.
 *
 * @author overtrue <i@overtrue.me>
 */
class Response extends GuzzleResponse {
    /**
     * @return string
     */
    public function getBodyContents(): string {
        $this->getBody()->rewind();
        $contents = $this->getBody()->getContents();
        $this->getBody()->rewind();

        return $contents;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Response
     */
    public static function buildFromPsrResponse(ResponseInterface $response): Response {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    /**
     * Build to json.
     * @return string
     * @throws HttpException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function toJson(): string {
        return json_encode($this->toArray());
    }

    /**
     * Build to array.
     * @return array
     * @throws HttpException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function toArray(): array {
        $content = $this->removeControlCharacters($this->getBodyContents());

        if (false !== stripos($this->getHeaderLine('Content-Type'), 'xml') || 0 === stripos($content, '<xml')) {
            throw new HttpException('Http Error');
        }

        $array = json_decode($content, true, 512, JSON_BIGINT_AS_STRING);

        if (JSON_ERROR_NONE === json_last_error()) {
            return (array)$array;
        }

        return [];
    }

    /**
     * Get collection data.
     * @return Collection
     * @throws HttpException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function toCollection(): Collection {
        return new Collection($this->toArray());
    }

    /**
     *
     * @return object
     * @throws HttpException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function toObject(): object {
        return json_decode($this->toJson());
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getBodyContents();
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function removeControlCharacters(string $content): string {
        return \preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $content);
    }
}
