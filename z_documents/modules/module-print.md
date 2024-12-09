# **Print** 基本模块 (**Optional**)

该模板提供了打印模板管理，打印模板设计，批量打印等功能。

## 模块安装

```bash
# 安装依赖
composer require jobsys/starter-print --dev

# 启用模块
php artisan module:enable Print && php artisan migrate
```

### 依赖

#### PHP 依赖 （无）

#### JS 依赖

  ```json5
  {
      "vue-plugin-hiprint": "0.0.56"        //打印插件
  }
  ```

### 配置
#### 权限配置

`app/Boot/BootPermission::permisions()`

```php
// Permission 添加权限
"page.manager.print.index" => [
	"display_name" => "打印设置",
	"children" => [
		"api.manager.print.template.edit" => "编辑打印模板",
		"api.manager.print.template.variable.edit" => "编辑打印模板参数",
		"api.manager.print.template.design.edit" => "设计打印模板",
		"api.manager.print.template.items" => "查看打印模板列表",
		"api.manager.print.template.item" => "查看打印模板详情",
		"api.manager.print.template.delete" => "删除打印模板",
		"api.manager.print.template.copy" => "复制打印模板",
	]
],

```

#### 菜单配置
`config/default/menus.php`

```php
[
	"displayName" => "打印设置",
	"page" => "page.manager.print.index",
],
```

```bash
# 同步权限
php artisan permission:sync
```

## 模块功能

### 打印模板管理

提供了打印模板的增删改查、复制功能并可以设置业务参数。

#### 开发规范

1. 提供了打印模板的增删改查和复制功能。打印模板有一个 `parent_id`（原型模板）的属性，没有 `parent_id` 的模板为原型模板，有 `parent_id` 的模板为原型模板的副本。

   > 注意：如果创建的时候选择了一个复制出来的模板（即有 `parent_id`）做原型模板，那么创建出来的模板 `parent_id` 为原型模板的 `parent_id`，而不是原型模板的 `id`。

2. 打印模板可以设置业务参数，设置的业务参数将会在设计打印模板时列出并可以拖拽使用。

### 打印模板设计

打印模板设计使用的是 [vue-plugin-hiprint](https://gitee.com/CcSimple/vue-plugin-hiprint) 插件，该插件是基于 [hiprint](http://hiprint.io/docs/start) 开发的，具体使用方法请参考 [vue-plugin-hiprint](https://gitee.com/CcSimple/vue-plugin-hiprint) 插件的文档。
设计好的打印模板将会以 `JSON` 保存在数据库中。

1. 需要在页面引入 [`print-lock.css`](https://gitee.com/sinceow/land-docs/raw/master/attachments/print-lock.css ':ignore :target=_blank')文件，否则在打印时可能会出现多页内容重叠的情况。可以参考 [【vue-plugin-hiprint】使用-入门篇](https://mp.weixin.qq.com/s/4N4f7CkxodA-fuTJ_FbkOQ)

2. 引用方式 `<link rel="stylesheet" media="print" type="text/css" href="{{asset('print-lock.css')}}">`，使用 `import` 引入方式无效。

3. `main.js` 

	```js
	import { hiPrintPlugin } from "vue-plugin-hiprint"
 
 	//禁止链接打印机
	hiPrintPlugin.disAutoConnect()
 
 	//在 setup 函数中挂载打印插件
 	app.use(hiPrintPlugin, "$pluginName")
	```

4. 设计自定义 `table`
   1. 先在 `Excel` 中设计好表格，变量使用 `\${}` 包裹，如 `\${name}`。
   2. 复制 `Excel` 表格，粘贴到 [表格转换](https://www.lingdaima.com/table/) 得到 `HTML` 代码
   3. 在设计窗口添加一个 `HTML` 功能，选择 `高级` - `格式化函数` 并使用以下代码
   ```js
   function(title,value,options,templateData,target){
    var html =  `替换成上面的HTML内容`

    return options && Object.keys(options).length 
    ? html.replace(/\${(\S+)}/g, (match, key) =>  options[key])
    : html
	}
   ```

### 打印

打印功能是依赖模板设计功能完成，由于打印模板插件本身就集成了设计、预览、打印功能，所以在打印的时候将会隐藏设计功能，直接进行预览和打印。

#### 开发规范

1. 准备打印模板的打印参数，如果只有一个 `object` 表示只打印一份，如果是一个 `array` 表示批量打印。

2. 调用 `PrintService` 的 `print` 方法，传入打印模板的 `id` 和打印参数即可。

   ```php
     return $print_service->print($template_id, $print_data);
   ```

## 模块代码

### 数据表

```bash
2014_10_12_000010_create_print_tables                     # 新闻资讯数据表
```

### 数据模型/Scope

```bash
Modules\Print\Entities\PrintTemplate                # 打印模板
```

### Controller

```bash
Modules\Print\Http\Controllers\PrintController        # CRUD API
```

### UI

#### PC 端页面

```bash
web/PagePrint.vue                        # 打印模板管理页面
web/NudePagePrintDesigner.vue            # 打印模板设计页面
```

#### PC 组件

```bash
web/components/PrintDesigner.vue         # 打印模板设计组件，集成了设计、预览、打印功能
```

### Service

+ **`PrintService`**

	- `print` 打印

	  ```php
	  /**
	  * 打印
	  * @param int $template_id 打印模板id
	  * @param object|array $data 打印数据, 如果为数组，表示打印多份
	  * @return Response
	   */
	   public function print(int $template_id, object|array $data): Response
	  ```

	- `createTemplate` 初始化打印模板，主要是在系统初始化的时候在 Seeder 中生成打印模板

	  ```php
	  /**
	  * 初始化打印模板
	  * @param string $display_name
	  * @param string $description
	  * @return void
	   */
	   public function createTemplate(string $display_name, string $description): void
