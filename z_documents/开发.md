# 快速开始

本项目基于 [Laravel](https://laravel.com) v10.0
框架开发，所以在开始之前，你需要先安装好 [Laravel 开发环境](https://laravel.com/docs/10.x)。
推荐使用 [Laravel Valet](https://laravel.com/docs/10.x/valet) 来快速搭建开发环境。

> 该项目遵循 [Laravel](https://laravel.com) 框架的开发规范，所有开发工作均可以按照官方文档进行。

> 但如果需要使用到本项目中的功能或者是模块，则需要参考本文档中的约定以及规则进行开发。

以下为安装步骤，依次执行即可。

```bash
 # 创建项目
composer create-project jobsys/land-boilerplate your-project-name

# 进入项目目录
cd your-project-name
```

> 在 `.env` 文件中配置`数据库连接`信息、`APP_URL`、 `APP_NAME`、`SUPER_PWD`

```bash
# 安装 AppKey (若有需要)
php artisan key:generate

# 安装基础模块
composer require jobsys/starter-module jobsys/permission-module jobsys/importexport-module jobsys/approval-module  --dev

# 启用模块 (若未自动启用)
php artisan module:enable Starter && php artisan module:enable Permission && php artisan module:enable Importexport && php artisan module:enable Approval


# 建表
php artisan migrate


# 初始化数据库和同步权限
php artisan db:seed --class=DatabaseSeeder && php artisan permission:sync


# 创建存储路径软链
php artisan storage:link


# 运行前端
#npm
npm i -d
npm run dev

#pnpm
pnpm i -d
pnpm dev
```

打开浏览器访问 [http://your-project-name.test/](http://your-project-name.test/), 显示项目名称即表示安装成功。

