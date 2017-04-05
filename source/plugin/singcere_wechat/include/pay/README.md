说明
=========================

本目录源码修改至 https://pay.weixin.qq.com/wiki/doc/api/app.php  V3版本

做了如下改动:

- 移除config明文配置, 配置数据来至singcere_wechat插件
- WxPay.Api整合原lib下的相关接口文件, 重写了data类
- 新增Wxpay.class类方便微信插件调用
- cacert目录用于存放证书密钥
