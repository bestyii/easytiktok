<?php
namespace EasyTiktok\MiniProgram\Pay;

class OrderInfo
{
    public string $order_id;

    public string $order_token;

    public function __construct(string $order_id, string $order_token)
    {
        $this->order_id = $order_id;
        $this->order_token = $order_token;
    }
}