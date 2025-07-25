# Laravel 项目部署

## 项目更新

1. 更新代码
   
   ```bash
   git pull
   ```

2. 是否需要更新数据库?
   
   ```bash
   php artisan migrate --force
   ```

3. 是否需要更新 `seeder`?
   
   ```bash
   php artisan db:seed --class=DatabaseSeeder
   ```

4. 是否需要更新 `permission`?
   
   ```bash
   php artisan permission:sync
   ```

5. 是否需要重启队列?
   
   ```bash
   ps aux | grep "queue:work"
   kill -q <pid>
   # 使用 `supervisor` 等工具将自动重启
   nohup php artisan queue:work &      # 使用 `nohup` 运行
   ```

<div style="page-break-after: always;"></div>

## 初次部署

1. 安装 `PHP` 依赖
   
   a. PHP 拓展依赖包括：
   
   ```bash
   zip mbstring exif pdo_mysql sodium bcmath gd curl
   ```
   
   > 记住要使用 `--no-dev` 排除依赖，因为安装 dev 包会覆盖掉项目里的 `jobsys/*` 等 Module，如果安装了开发依赖，在依赖安装完毕后使用 Git 来复原代码
   > 
   > ```bash
   > git reset --hard
   > git clean -f -d 
   > ```
   
   b. 安装项目依赖：

   
   ```bash
   composer install --no-dev
   ```

> 如果使用 packagist 镜像无法安装，使用 Huawei 镜像, 在 `composer.json` 中添加以下配置：
```json
{
	"repositories": {
		"packagist": {
			"type": "composer",
			"url": "https://mirrors.huaweicloud.com/repository/php/"
		}
	}
}
```

2. 配置环境变量 `.env`
   
   ```
   APP_NAME=
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=
   USE_HTTPS=true/false  #是否使用HTTPS
   
   DB_CONNECTION  #数据库配置
   ```

3. 创建数据库
   
   ```bash
   php artisan migrate
   ```

4. 安装 `seeder`
   
   ```bash
   php artisan db:seed --class=DatabaseSeeder
   ```

5. 同步权限
   
   ```bash
   php artisan permission:sync
   ```

6. 创建软链
   
   ```bash
   php artisan storage:link
   ```

7. 运行队列，尽量使用 `supervisor` 等工具运行（如队列中的逻辑有变动，在更新后再重新开启队列）
   
   ```bash
   # /etc/supervisor/conf.d
   # work.conf
   [program:work]
   directory = /var/www/html/
   command = /usr/local/bin/php /var/www/html/artisan queue:work --timeout=0
   autostart = true
   startsecs = 5
   startretries=1000
   autorestart=  true
   user = www-data
   redirect_stderr = true
   stdout_logfile = /var/log/supervisor/work.log
   environment=HOME= /root
   ```
   
   ```bash
   supervisorctl reload
   ```
   
   ```bash
   /usr/local/bin/php
   /var/www/html/artisan queue:work
   ```

8. 在系统中添加定时任务
   
   ```bash
    # 注意切换到 `www-data` 用户
   crontab -u www-data -e
   ```
   
   ```bash
   * * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
   ```
   
   ```bash
   * * * * * cd /var/www/html && /usr/local/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1
   ```

9. 在 apache 中配置 `DocumentRoot` 为 `public` 目录，并添加 `.htaccess` 的改写功能
   
   ```apacheconf
   <Directory /var/www/html/public>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```
