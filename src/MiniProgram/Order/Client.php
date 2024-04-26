<?php
namespace EasyTiktok\MiniProgram\Order;

use EasyTiktok\Kernel\BaseClient;
use EasyTiktok\Kernel\Exceptions\HttpException;
use EasyTiktok\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 订单
 * Class Client
 * @package EasyTiktok\MiniProgram\Order
 */
class Client extends BaseClient
{
    /**
     * 订单同步
     * @param string $open_id
     * @param int $order_status
     * @param array $order_detail
     * @param string $extra
     * @return array
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function push(string $open_id, int $order_status, array $order_detail, string $extra = ''): array
    {
        $app_name = 'douyin';
        $order_type = 0;
        $update_time = intval(microtime(true));
        $order_detail = json_encode($order_detail);
        $params = compact('open_id', 'order_status', 'order_detail', 'extra', 'app_name', 'order_type', 'update_time');
        return $this->httpPostJson('apps/order/v2/push', $params);
    }
}
