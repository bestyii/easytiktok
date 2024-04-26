# 入门


> 🚨 使用前建议熟读 [抖音开放平台文档](https://open.douyin.com/platform/doc/6848834666171009035)

常用的配置参数会比较少，因为除非你有特别的定制，否则基本上默认值就可以了:

```php
<?php
use EasyTiktok\Application;

$app = Application::openPlatform([
    'app_id' => 'app_id',
    'secret' => 'secret',
    'openid' => '',
    'cache'  => '....'  // 允许自定义缓存配置
]);
```
📖 更多配置项请参考：[配置](config.md)

注意：相较于微信而言，抖音开放平台主要针对的是个人，所以我们在调用开放平台接口之前需要先获得当前用户的`OpenId`。

## 数据授权

抖音开放平台接口均需要申请权限，请在【管理中心-应用管理-权限管理】申请相关权限。
> PS：真的很难申请！！！！

## 用户授权

抖音的接口基本都需要获取用户的授权，才可以请求，所以`EasyTiktok`针对用户的身份授权做了很多的保障。具体的抖音规则可以参看[抖音文档](https://open.douyin.com/platform/doc/6848806497707722765)。`EasyTiktok`会自动刷新`AccessToken`和`RefreshToken`。不过当系统刷新不了`Token`的时候，会抛出`AccessTokenException`异常，开发者可以捕获相关异常做特殊处理。
