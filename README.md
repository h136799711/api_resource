### 综合接口地址

api地址
devapi.qqav.club 

public 文件夹
appdowload.php => app下载地址
index.php => 接口请求入口
web.php => 网页模块访问入口
test.php => 暂无特殊意义

//缺少参数的调用
LangHelper::lackParameter('position')

#### require:
composer
thinkphp5.0.9 
#### 文件夹权限:
public/upload 写入权限
runtime 写入权限
open_basedir 权限
#### 开启函数:
scandir,
proc_open,

### redis 安装
http://example.com/redisadmin
git clone https://github.com/ErikDubbelboer/phpRedisAdmin.git
cd phpRedisAdmin
git clone https://github.com/nrk/predis.git vendor

#### 注意
时区设置问题，流浪器端设置cookies，key=timezone   
mysql时区也要设置成default-time_zone = '+0:00'   