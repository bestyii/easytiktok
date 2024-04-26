<?php
namespace EasyTiktok\MiniProgram\Pay;

use ArrayAccess;
use EasyTiktok\Kernel\Traits\HasAttributes;

/**
 * Class Message
 * @package EasyTiktok\MiniProgram\Pay
 * @property array $msg
 */
class Message implements ArrayAccess
{
    use HasAttributes;

    public const TYPE_PAY = 'payment';

    public const TYPE_REFUND = 'refund';

    public const TYPE_SETTLED = 'settle';

    public function getType(): string
    {
        $type = $this->toArray()['type'] ?? '';
        if (empty($type)) {
            throw new \RuntimeException('Invalid event type.');
        }
        return $type;
    }
}
