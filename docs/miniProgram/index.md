# 入门


> 🚨 使用前建议熟读 [抖音小程序文档](https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/server-api-introduction)

常用的配置参数会比较少，因为除非你有特别的定制，否则基本上默认值就可以了:

```php
<?php
use EasyTiktok\Application;

$app = Application::miniProgram([
    'app_id' => 'app_id',
    'secret' => 'secret',
    'cache'  => '....'  // 允许自定义缓存配置
]);
```
📖 更多配置项请参考：[配置](config.md)
