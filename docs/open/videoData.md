# 视频数据相关

## 实例化
```php
<?php
use EasyTiktok\Application;
 
$app = Application::openPlatform([
    'app_id' => 'app_id',
    'secret' => 'secret',
    'openid' => ''
])->video_data;
```

## 获取视频基础数据

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798407369164803)

该接口用于获取视频基础数据

:::tip 注意
- 用户首次授权应用后，需要第二天才会产生全部的数据；
- 注意参数中item_id作为url参数时，必须encode，只对item_id单独进行encode。【框架已做了适配，开发者不需要特殊处理】
- 三十天内创建的视频，才会返回数据。
:::

使用示例：
```php
$data = $app->base(string $item);
```

## 获取视频点赞数据

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798407369230339)

该接口用于获取视频点赞数据。

:::tip 注意
- 用户首次授权应用后，需要第二天才会产生全部的数据；
- 注意参数中item_id作为url参数时，必须encode，只对item_id单独进行encode。【框架已做了适配，开发者不需要特殊处理】
- 三十天内创建的视频，才会返回数据。
:::

使用示例：
```php
$data = $app->like(string $item, int $date_type = 7);
```

## 获取视频评论数据

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798407369197571)

该接口用于获取视频评论数据。

:::tip 注意
- 用户首次授权应用后，需要第二天才会产生全部的数据；
- 注意参数中item_id作为url参数时，必须encode，只对item_id单独进行encode。【框架已做了适配，开发者不需要特殊处理】
- 三十天内创建的视频，才会返回数据。
:::

使用示例：
```php
$data = $app->comment(string $item, int $date_type = 7);
```

## 获取视频播放数据

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798385894426636)

该接口用于获取视频播放数据。

:::tip 注意
- 用户首次授权应用后，需要第二天才会产生全部的数据；
- 注意参数中item_id作为url参数时，必须encode，只对item_id单独进行encode。【框架已做了适配，开发者不需要特殊处理】
- 三十天内创建的视频，才会返回数据。
:::

使用示例：
```php
$data = $app->play(string $item, int $date_type = 7);
```

## 获取视频分享数据

> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848798385894459404)

该接口用于获取视频分享数据。

:::tip 注意
- 用户首次授权应用后，需要第二天才会产生全部的数据；
- 注意参数中item_id作为url参数时，必须encode，只对item_id单独进行encode。【框架已做了适配，开发者不需要特殊处理】
- 三十天内创建的视频，才会返回数据。
:::

使用示例：
```php
$data = $app->share(string $item, int $date_type = 7);
```