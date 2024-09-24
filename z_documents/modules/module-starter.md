# **Starter** 基本模块 (**Required**)

该模块主要是提供系统的基本功能，包括基础用户认证登录、日志管理、字典管理、消息通知管理等。

## 模块安装

```bash
# 安装依赖
composer require jobsys/starter-module --dev

# 启用模块
php artisan module:enable Starter && php artisan module:publish-migration Starter && php artisan migrate


```
### 配置
#### 模块配置

```php
"Starter" => [
   "name" => "Starter",
	"languages" => [ //可选语言
		"zh_CN" => "简体中文",
		//"en" => "English"
	],
	"default_lang" => "zh_CN", // 默认语言
]
```

## 模块功能

### 用户认证登录

`用户认证登录`功能流程：`用户登录` => `选择角色` *（如果只有一个角色自动选定）* => `初始化权限信息` => `跳转到Dashboard`。

#### 开发规范

1. 可以根据业务需求自定义登录页面，默认登录页面的路由为 `/login`。
2. 默认的登录界面为 `Modules/Starter/Resources/views/web/NudePageLogin.vue`，登录用户名由 `config/conf.php`
   中的 `login_field` 定制，可以根据业务需求进行修改。

### 字典管理

字典管理功能提供了对系统中常用的字典进行管理，如：性别、状态、类型等，根据不同的系统功能进行初始化。

#### 开发规范

1. 字典管理的数据表为 `dictionaries`，数据模型为 `Modules\Starter\Entities\Dictionary`。
2. 在 `database/seeders/DictDefaultSeeder.php` 的 `run` 方法中定义字典的初始化数据。
	```php
	if (!Dictionary::where('slug', config('conf.dict_key'))->exists()) {
		$dict = Dictionary::create(['name' => '词典名称', 'is_cascaded' => false, 'slug' => config('conf.dict_key')]);
		$items = ["A", "B", "C", "D"];
		foreach ($items as $index => $item) {
			DictionaryItem::updateOrCreate(['name' => $item, 'dictionary_id' => $dict->id], ["parent_id" => null, "value" => $item, 'sort_order' => $index * -1]);
		}
	}
	
	if (!Dictionary::where('slug', config('conf.dict_key'))->exists()) {
		$dict = Dictionary::create(['name' => '词典名称', 'is_cascaded' => false, 'slug' => config('conf.dict_key')]);
	
		$items = [
			['name' => 'A', 'value' => 'A'],
			['name' => 'B', 'value' => 'B'],
			['name' => 'C', 'value' => 'C'],
		];
	
		foreach ($items as $index => $item) {
			DictionaryItem::updateOrCreate(['name' => $item['name'], 'dictionary_id' => $dict->id], ["parent_id" => null, "value" => $item['value'], 'sort_order' => $index * -1]);
		}
	}
	```
3. 使用命令同步 Seeder
    ```bash
    php artisan db:seed
    ```

### 日志管理

日志管理功能提供了对系统中的操作日志进行记录，包括：操作人、操作时间、操作内容、操作结果等。

#### 开发规范

1. 日志管理的数据表为 `activity_log`, 继承自 `BaseModel` 的 Model 会在 `update`, `create` 和 `delete` 时自动触发日志记录。
2. 手动记录日志可以调用 `log_access` 辅助函数，如：
    ```php
    log_access('查看xxx资源', $item);
    ```

### 消息通知管理

消息通知管理功能集成了消息通知的获取、查看、标记已读等功能。API
由 `Modules\Starter\Http\Controllers\NotificationController` 提供，
前端页面由 `Modules\Starter\Resources\views\web\components\NotificationBox.vue`组件提供。

#### 开发规范

1. 消息通知管理的数据表为 `notifications`，详细的功能可以参考 [Laravel Notifications](https://laravel.com/docs/10.x/notifications)。
2. 在页面中使用 `NotificationBox` 组件，如：
    ```js
    import NotificationBox from "@modules/Starter/Resources/views/web/components/NotificationBox.vue"
    ```
    ```vue
    <NotificationBox></NotificationBox>
    ```

### 枚举

```php
// 基础状态码
enum State: string
{
    const SUCCESS = 'STATE_CODE_SUCCESS';
    const FAIL = 'STATE_CODE_FAIL';
    const DB_FAIL = 'STATE_CODE_DB_FAIL';
    const NOT_LOGIN = 'STATE_CODE_NOT_LOGIN';
    const NOT_FOUND = 'STATE_CODE_NOT_FOUND';
    const NOT_ALLOWED = 'STATE_CODE_NOT_ALLOWED';
    const INVALID_PARAMETERS = 'STATE_CODE_INVALID_PARAMETERS';
    const DUPLICATION = 'STATE_CODE_DUPLICATION';
    const USER_INVALID = 'STATE_CODE_USER_INVALID';
    const USER_INFO_INCOMPLETE = 'STATE_CODE_USER_INFO_INCOMPLETE';
    const TOO_FREQUENTLY = 'STATE_CODE_TOO_FREQUENTLY';
    const CAPTCHA_ERROR = 'STATE_CODE_CAPTCHA_ERROR';
    const VERIFY_ERROR = 'STATE_CODE_VERIFY_ERROR';
    const MOBILE_ERROR = 'STATE_CODE_MOBILE_ERROR';
    const EMAIL_ERROR = 'STATE_CODE_EMAIL_ERROR';
    const RISKY_CONTENT = 'STATE_CODE_RISKY_CONTENT';
    const RISKY_IMAGE = 'STATE_CODE_RISKY_IMAGE';
}
```

### 辅助函数

```php
/**
 * 获取用户相关内容 [菜单, 权限, 部门, 个人信息, 是否超级管理员]
 * @return array
 */
function starter_setup_user(): array


/**
* 获取用户菜单
* @param $user
* @param array $permissions
* @return array
*/
function starter_get_user_menu($user = null, array $permissions = []): array

/**
 * 获取字典
 * @param string|array $slug
 * @param bool $only_options
 * @param string $valueKey 值字段名
 * @return array|BaseModel|Dictionary|null
 */
function dict_get(string|array $slug, bool $only_options = true, string $valueKey = 'value'): BaseModel|array|Dictionary|null
	
  /**
 * @param string $log 动作
 * @param Model|null $object 对象
 * @param Model|int|string|null $causer 用户类型
 * @return void
 */
function log_access(string $log, Model|null $object = null, Model|int|string|null $causer = null): void

/**
* 获取配置，可能存在同名的多个配置，所以返回一个集合，再根据具体业务决定如何使用
* @param $module
* @param null $group
* @param null $name
* @param null $lang
* @return Collection
*/
function configuration_get($module, $group = null, $name = null, $lang = null): Collection

/**
* 获取单一配置
* @param $module
* @param $group
* @param $name
* @param string|null $lang
* @return mixed
*/
function configuration_get_first($module, $group, $name, string $lang = null): mixed

/**
* @param array $configuration
* @param bool $override 是否覆盖已存在的配置
* @return Configuration
*/
function configuration_save(array $configuration, bool $override = false): Configuration

```


### Controller

```bash
# 基础控制器，提供 `json`, `success`, `message` 方法   
Modules\Starter\Http\Controllers\BaseController
```

```php
// `json` 方法结果与状态都是自定的
public function json($result = null, string $status = State::SUCCESS, string $result_key = 'result'): JsonResponse

// `message` 方法状态为 `State::FAIL` 
public function message($message): JsonResponse

// `success` 方法状态为 `State::SUCCESS`
public function success($result): JsonResponse


//返回结果示例
[
    'status' => 'SUCCESS',
    'result' => [
        'id' => 1,
        'name' => 'name',
        'created_at' => '2021-08-17 16:22:49',
        'updated_at' => '2021-08-17 16:22:49',
    ],
]
```

#### PC 组件

```bash
web/components/NotificationBox.vue      # 消息通知组件
```
