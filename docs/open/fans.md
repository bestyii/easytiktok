# 粉丝数据

## 实例化
```php
<?php
use EasyTiktok\Application;

$app = Application::openPlatform([
    'app_id' => 'app_id',
    'secret' => 'secret',
    'openid' => ''
])->fans_data;
```

## 获取用户粉丝数据

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798471810484227)

该接口用于查询用户的粉丝数据，如性别分布，年龄分布，地域分布等。

:::tip 注意
用户首次授权应用后，需要间隔2天才会产生全部的数据；并只提供粉丝大于100的用户数据。
:::

使用示例：
```php
$data = $app->data();
```
