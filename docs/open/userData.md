# 用户数据

## 实例化
```php
<?php
use EasyTiktok\Application;

$app = Application::openPlatform([
    'app_id' => 'app_id',
    'secret' => 'secret',
    'openid' => ''
])->user_data;
```

## 获取用户视频情况

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798450331486212)

该接口用于获取用户视频情况。

:::tip 注意
用户首次授权应用后，需要间隔2天才会产生全部的数据。
:::

使用示例：
```php
$data = $app->item(int $date_type = 7);
```

## 获取用户粉丝数

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798450331453444)

该接口用于获取用户粉丝数。

:::tip 注意
用户首次授权应用后，需要间隔2天才会产生全部的数据。
:::

使用示例：
```php
$data = $app->fans(int $date_type = 7);
```


## 获取用户点赞数

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798450331518980)

该接口用于获取用户点赞数。

:::tip 注意
用户首次授权应用后，需要间隔2天才会产生全部的数据。
:::

使用示例：
```php
$data = $app->like(int $date_type = 7);
```

## 获取用户评论数

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798450331420676)

该接口用于获取用户评论数。

:::tip 注意
用户首次授权应用后，需要间隔2天才会产生全部的数据。
:::

使用示例：
```php
$data = $app->comment(int $date_type = 7);
```

## 获取用户分享数

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798471810451459)

该接口用于获取用户分享数。

:::tip 注意
用户首次授权应用后，需要间隔2天才会产生全部的数据。
:::

使用示例：
```php
$data = $app->share(int $date_type = 7);
```

## 获取用户主页访问数

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798450331551748)

该接口用于获取用户主页访问数。

:::tip 注意
用户首次授权应用后，需要间隔2天才会产生全部的数据。
:::

使用示例：
```php
$data = $app->profile(int $date_type = 7);
```