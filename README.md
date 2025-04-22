# xyb-sm4-php
该项目为XYB加密数据的解密的PHP版本SDK，由于官方仅提供了JAVA版本的SDK，没有php等其他平台的版本内容。本项目代码基于官方提供的JAVA SDK以及第三方php国密SM4库。
## 注意事项：
1. 解密秘钥为你负责项目的应用秘钥（也就是clientSecret）。
2. 本项目仅支持64位系统，32位系统暂不支持。（即PHP_INT_SIZE=8）
## 使用说明：
```
use xyb\sm4\App;
$sm4= new App($clientSecret);
$encryptStr='接口返回的encryptStr';
$plaintext = $sm4->decrypt($encryptStr);
```
@muquan    QQ:34936743
