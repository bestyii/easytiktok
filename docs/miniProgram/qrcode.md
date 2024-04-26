# 二维码

## 实例化
```php
<?php
use EasyTiktok\Application;

$app = Application::miniProgram([
    'app_id' => 'app_id',
    'secret' => 'secret'
])->qr_code;
```

## 生成二维码【createQRCode】

> 🚨 使用前建议熟读 [抖音小程序文档](https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/qr-code/create-qr-code)

获取小程序/小游戏的二维码。该二维码可通过任意 app 扫码打开，能跳转到开发者指定的对应字节系 app 内拉起小程序/小游戏， 并传入开发者指定的参数。通过该接口生成的二维码，永久有效，暂无数量限制。

使用示例：
```php
$data = $app->getUnLimit(string $appName, array $optional = []);
```