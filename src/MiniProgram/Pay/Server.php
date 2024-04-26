<?php
namespace EasyTiktok\MiniProgram\Pay;

use Closure;
use EasyTiktok\Kernel\Exceptions\InvalidArgumentException;
use EasyTiktok\Kernel\Exceptions\RuntimeException;
use EasyTiktok\Kernel\Http\Response;
use EasyTiktok\Kernel\ServiceContainer;
use EasyTiktok\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use InteractWithHandlers;

    protected ?ServerRequestInterface $request;

    /**
     * @var ServiceContainer
     */
    protected ServiceContainer $app;

    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    public function serve(): Response
    {
        $message = $this->getRequestMessage();
        try {
            $default_response = new Response(
                200,
                [],
                strval(json_encode(['err_no' => 0, 'err_tips' => 'success'], JSON_UNESCAPED_UNICODE))
            );
            $response = $this->handle($default_response, $message);

            if (!($response instanceof ResponseInterface)) {
                $response = $default_response;
            }

            return $response;
        } catch (\Exception $e) {
            return new Response(
                200,
                [],
                strval(json_encode(['err_no' => 400, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE))
            );
        }
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml
     *
     * @throws InvalidArgumentException
     */
    public function handlePaid(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler) {
            return $message->getType() === Message::TYPE_PAY
                ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_11.shtml
     *
     * @throws InvalidArgumentException
     */
    public function handleRefunded(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler) {
            return $message->getType() === Message::TYPE_REFUND
                ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/ecpay/settlements/callback
     *
     * @throws InvalidArgumentException
     */
    public function handleSettled(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler) {
            return $message->getType() === Message::TYPE_SETTLED
                ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function setRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        return $this->request = $request;
    }

    public function getRequestMessage(): Message
    {
        if (empty($this->request)) {
            throw new RuntimeException('empty request.');
        }

        $request = $this->request->getBody();
        $attributes = json_decode($request, true);
        if (! is_array($attributes)) {
            throw new RuntimeException('Invalid request body.');
        }

        // todo验签
        $attributes['msg'] = is_array($attributes['msg']) ? $attributes['msg'] : json_decode($attributes['msg'] ?? '', true);
        return new Message($attributes);
    }
}
