<?php
namespace EasyTiktok\MiniProgram\Pay;

use EasyTiktok\Kernel\BaseClient;
use EasyTiktok\Kernel\Exceptions\HttpException;
use EasyTiktok\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 支付
 * Class Client
 * @package EasyTiktok\MiniProgram\Pay
 */
class Client extends BaseClient
{
    protected bool $needAccessToken = false;

    protected array $no_need_sign_params = [
        'app_id',
        'thirdparty_id',
        'other_settle_params',
        'sign',
    ];

    /**
     * 预下单接口.
     * @param string $out_order_no
     * @param int $total_amount
     * @param string $subject
     * @param string $body
     * @param string $notify_url
     * @param int $valid_time
     * @param string $cp_extra
     * @return array
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function createOrder(string $out_order_no, int $total_amount, string $subject, string $body, string $notify_url = '', int $valid_time = 1200, $cp_extra = ''): array
    {
        $app_id = $this->app['config']['app_id'];
        $params = compact('app_id', 'out_order_no', 'total_amount', 'subject', 'body', 'notify_url', 'valid_time', 'cp_extra');
        $params['sign'] = $this->sign($params);
        return $this->httpPostJson('apps/ecpay/v1/create_order', $params);
    }

    /**
     * 发起退款
     * @param string $out_order_no
     * @param string $out_refund_no
     * @param int $refund_amount
     * @param string $reason
     * @param string $notify_url
     * @param string $msg_page
     * @param string $cp_extra
     * @return array
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function refundPayOrder(string $out_order_no, string $out_refund_no, int $refund_amount, string $reason, string $notify_url = '', string $msg_page = '', string $cp_extra = ''): array
    {
        $app_id = $this->app['config']['app_id'];
        $params = compact('app_id', 'out_order_no', 'out_refund_no', 'refund_amount', 'reason', 'notify_url', 'msg_page', 'cp_extra');
        $params['sign'] = $this->sign($params);
        return $this->httpPostJson('apps/ecpay/v1/create_refund', $params);
    }

    /**
     * 申请结算
     * @param string $out_settle_no
     * @param string $out_order_no
     * @param string $notify_url
     * @param string $settle_desc
     * @param string $cp_extra
     * @param string $finish
     * @return array
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function settle(string $out_settle_no, string $out_order_no, string $notify_url = '', string $settle_desc = '主动结算', string $cp_extra = '', string $finish = 'true'): array
    {
        $app_id = $this->app['config']['app_id'];
        $params = compact('app_id', 'out_order_no', 'out_settle_no', 'settle_desc', 'notify_url', 'finish', 'cp_extra');
        $params['sign'] = $this->sign($params);
        return $this->httpPostJson('apps/ecpay/v1/settle', $params);
    }

    /**
     * 签名
     * @param array $params
     * @return string
     */
    protected function sign(array $params): string
    {
        $need_sign_params = [];
        foreach ($params as $k => $v) {
            $v = trim(strval($v));
            if (empty($v) || in_array($k, $this->no_need_sign_params)) {
                continue;
            }
            $need_sign_params[] = $v;
        }
        $need_sign_params[] = $this->app['config']['pay']['salt'];
        sort($need_sign_params, SORT_STRING);
        return md5(implode('&', $need_sign_params));
    }
}
