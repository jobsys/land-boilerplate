#

### 开启异步任务

#### DEV

1. `.env` 中 `QUEUE_CONNECTION` 设置为 `database`
2. 运行任务 `php artisan queue:work`

#### PROD

1. 运行队列，尽量使用 `supervisor` 等工具运行（如队列中的逻辑有变动，在更新后再重新开启队列）

   ```bash
   # /etc/supervisor/conf.d
   # work.conf
   [program:work]
   directory = /var/www/html/
   command = /usr/local/bin/php /var/www/html/artisan queue:work --timeout=7200
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
	# 重启supervisor
   supervisorctl reload
   ```

> 注意：`config/queue.php` 中 `database.retry_after` 需要比较上面命令中的 `timeout` 长，避免任务未完成时重试。


### 生成 SM2 密钥对

```bash
# 生成密钥对
php artisan app:generate-sm2-key-pairs
```
