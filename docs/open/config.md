# 配置

下面是一个完整的配置样例：

> 不建议你在配置的时候弄这么多，用到啥就配置啥才是最好的，因为大部分用默认值即可。

```php
use EasyTiktok\Application;

$videoObj = Application::openPlatform([
    'app_id' => '...',
    'secret' => '...',
    'openid' => '...',
    'cache'  => [
        'type'       => 'Redis',
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'prefix'     => '',
        'serialize'  => []
    ],
    /**
     * 接口请求相关配置，超时时间等，具体可用参数请参考：
     * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
     */
    'http' => [
        'timeout' => 5.0
    ]
]);
```