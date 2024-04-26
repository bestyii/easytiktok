# 服务器

## 实例化
```php
<?php
use EasyTiktok\Application;

$app = Application::miniProgram([
    'app_id' => 'app_id',
    'secret' => 'secret'
])->encryptor;
```

## 解密数据

> 🚨 使用前建议熟读 [抖音小程序文档](https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/api/open-interface/user-information/sensitive-data-process/)

- 对称解密使用的算法为 AES-128-CBC，数据采用 PKCS#7 填充
- 对称解密的目标密文为 encryptedData，即敏感数据
- 对称解密秘钥 aeskey = Base64_Decode(session_key), aeskey 长度为 16Byte
- 对称解密算法初始向量为 Base64_Decode(iv)

使用示例：
```php
$data = $app->decryptData(string $sessionKey, string $iv, string $encrypted);
```