# 概述

EasyTiktok 是一个开源的 [抖音](https://www.douyin.com/) 非官方 SDK。

EasyTiktok 的安装非常简单，因为它是一个标准的 [Composer](https://getcomposer.org/) 包，这意味着任何满足下列安装条件的 PHP 项目支持 Composer 都可以使用它。

**EasyTiktok关注的是字节跳动旗下的抖音开放平台和小程序，值得注意的是字节体系非常庞杂，除了抖音还有西瓜视频和今日头条，后续我们会考虑逐步支持更多平台，当然大家也不用担心，字节很多接口都是通用的，所以有些功能天然支持跨平台，但是不得不说的是，目前的EasyTiktok核心关注的依旧是抖音开发**

### 环境需求
>  - PHP >= 7.4
>  - [PHP cURL 扩展](http://php.net/manual/en/book.curl.php)
>  - [PHP OpenSSL 扩展](http://php.net/manual/en/book.openssl.php)
>  - [PHP fileinfo 拓展](http://php.net/manual/en/book.fileinfo.php)

### 加入我们
你有以下两种方式加入到我们中来，为广大开发者提供更优质的免费开源的服务：

>  - **贡献代码**：我们的代码都在 [apiadmin/tiktok](https://gitee.com/apiadmin/tiktok) ，你可以提交 PR 到任何一个项目，当然，前提是代码质量必须是 OK 的。
>  - **翻译或补充文档**：我们的文档在：[apiadmin/tiktok-wiki](https://gitee.com/apiadmin/tiktok-wiki)，你可以选择补充文档。

### 开始之前
本 SDK 不是一个全新再造的东西，所以我不会从 0 开始教会你开发抖音，你完全有必要在使用本 SDK 前做好以下工作：

>  - 具备 PHP 基础知识，不要连闭包是啥都不明白。
>  - 熟悉 PHP 常见的知识：自动加载、composer 的使用、JSON 处理、Curl 的使用等；
>  - **仔细阅读并看懂**[抖音开放平台文档](https://open.douyin.com/platform/doc) [抖音小程序文档](https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/server-api-introduction)；
>  - 明白抖音接口的组成，自有服务器、抖音服务器、小程序以及通信原理（交互过程）；
>  - 了解基本的 HTTP 协议，Header 头、请求方式（GET\POST\PUT\PATCH\DELETE）等；
>  - 基本的 Debug 技能，查看 php 日志，nginx 日志等。

如果你不具备这些知识，请不要使用，因为用起来会比较痛苦。

另外你有必要看一下以下的链接：

>  - [断言：不懂《提问的智慧》的人不会从初级程序员水平毕业](https://learnku.com/laravel/t/535/assertion-people-who-do-not-understand-the-wisdom-of-asking-questions-will-not-graduate-from-junior-programmers)
>  - [PHP之道](http://laravel-china.github.io/php-the-right-way/)

如果你在群里问以下类似的问题，这真的是你没有做好上面的工作：

::: tip 例如
- "为啥我的不行啊，请问服务器日志怎么看啊？"
- "请问这是什么原因啊？[结果/报错截图]"
- "请问这个SDK怎么用啊？"
- "谁能告诉我这个SDK是怎么安装的啊？"
- "怎么接收用户发的消息啊？"
- "为啥我的报这个错啊：Class XXXX not found..."
- ...
:::

如果你在问题疑难解答没找到你出现的问题，那么可以在这里提问 [Gitee](https://gitee.com/apiadmin/tiktok/issues)，提问请描述清楚你用的版本，你的做法是什么，不然别人没法帮你。

::: warning 警告
最后，请 **不要在QQ单独找我提问**，除非你是发现了明显的bug。有问题先审查代码，看文档, 再 google，然后 去群里发个问题，带上你的代码，重现流程，大家有空的会帮忙你解答。谢谢合作！:pray:
:::


### 打赏支持

这是一个开源的项目，我们没有收费服务，你如果觉得你从中获益，简化了你的开发工作，你可以[在Gitee上打赏](https://gitee.com/apiadmin/tiktok)来支持我们。
