# ci-api-framework

    基于CodeIgniter的API框架。
    An API framework based on the CodeIgniter.

## 特性

    1. 接口参数签名校验（Key - Security），防止中间人攻击篡改数据
    2. 接口时效性验证（精确到毫秒），防止拒绝服务
    3. 日志记录，让请求有据可查
    4. API文档自动生成
    5. API在线调试，支持多环境（开发、测试、预发布）
    6. 请求参数自动强类型过滤，防止sql注入
    7. 支持API版本迭代、API降级处理
    
## 搭建

    1. 基础配置请参考CI框架。index.php文件目录：web/index.php
    2. API框架配置文件路径：application/config/restful_api.php
    3. 搭建好环境，配好虚拟主机，访问：域名/debug/package 即可看到调试界面
    
### 相关设置：
    
    配置文件：application/config/restful_api.php
    基础控制器：application/core/MY_Controller.php
    API文档界面：域名/debug/package
    签名方式界面：域名/debug/help
    配置描述界面：域名/debug/specification
    
    
## 预览图

![list](https://github.com/ziiber/ci-api-framework/blob/master/list.png?raw=true")
![debug](https://github.com/ziiber/ci-api-framework/blob/master/debug.png?raw=true")
![help](https://github.com/ziiber/ci-api-framework/blob/master/help.png?raw=true")
