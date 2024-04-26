# 登录授权

## 实例化
```php
<?php
use EasyTiktok\Application;

$app = Application::miniProgram([
    'app_id' => 'app_id',
    'secret' => 'secret'
])->auth;
```

## 登录【code2Session】

> 🚨 使用前建议熟读 [抖音小程序文档](https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/log-in/code-2-session)

使用code兑换openid。

使用示例：
```php
$data = $app->session(string $code);
```
