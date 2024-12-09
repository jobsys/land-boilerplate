# **多端开发**

[`Laravel`](https://laravel.com/docs/10.x/vite) 本身与 [`Vite`](https://cn.vitejs.dev/) 的进行了深度整合，基础的使用方式参考各自的文档。

## 约定

1. PC 端的代码统一放置在 `resources/views/web` 目录下，移动端的代码统一放置在 `resources/views/mobile` 目录下
2. Build 出来的文件统一放置在 `public/build/{clientName}`

## 添加一个新的端

每个端会使用一个新的 `vite` 配置，默认的 `vite.config.js` 是用于管理后台开发的配置，假如需要新添加一个 `unit`
的移动端，操作如下:

1. 在项目根目录添加一份 `vite` 配置，如 `vite.mobile.unit.config.js` 示例
   ```js
   import { defineConfig } from "vite"
   import laravel from "laravel-vite-plugin"
   import vue from "@vitejs/plugin-vue"
   import eslintPlugin from "vite-plugin-eslint"
   import pixelToRem from "postcss-pxtorem"
   import { fileURLToPath, URL } from "url"
   
   export default defineConfig({
	   css: {
		   preprocessorOptions: {
			   less: {
				   modifyVars: {},
				   javascriptEnabled: true,
			   },
		   },
		   postcss: {
			   plugins: [
				   pixelToRem({
					   rootValue() {
						   return 37.5
					   },
					   propList: ["*"],
				   }),
			   ],
		   },
	   },
	   plugins: [
		   laravel({
			   input: ["resources/views/mobile/unit/main.js"],
			   buildDirectory: "build/mobile/unit",
			   hotFile: "public/mobile.unit.hot", // Customize the "hot" file...
			   refresh: false,
		   }),
		   vue(),
		   eslintPlugin({
			   include: ["resources/views/mobile/unit/**/*.{js,vue}"],
		   }),
	   ],
	   resolve: {
		   alias: [
			   { find: "@", replacement: fileURLToPath(new URL("./resources", import.meta.url)) },
			   { find: "@modules", replacement: fileURLToPath(new URL("./Modules", import.meta.url)) },
			   { find: "@public", replacement: fileURLToPath(new URL("./public", import.meta.url)) },
			   { find: "@mobile", replacement: fileURLToPath(new URL("./resources/views/mobile", import.meta.url)) },
			   { find: "@components", replacement: fileURLToPath(new URL("./resources/views/components", import.meta.url)) },
		   ],
	   },
	   build: {
		   rollupOptions: {
			   output: {
				   manualChunks: {
					   "vue-chunk": ["vue", "@inertiajs/vue3", "@vueuse/core", "pinia"],
					   "vendor-chunk": ["axios", "dayjs", "lodash-es", "jobsys-explore"],
				   },
			   },
		   },
	   },
	   server: {
		   host: "0.0.0.0",
		   hmr: {
			   host: "localhost",
		   },
	   },
   })
   ```

2. 在项目根目录 `jsconfig.json` （如果不存在则手动创建） 中添加别名：

   ```json
   {
      "compilerOptions": {
           "baseUrl": ".",
           "paths": {
           "@/*": [
                "resources/*"
            ],
            "@modules/*": [
                "modules/*"
            ],
            "@public/*": [
                "public/*"
            ],
            "@web/*": [
                "resources/views/web/*"
            ],
            "@mobile/*": [
                "resources/views/mobile/*"
            ],
            "@components/*": [
                "resources/views/components/*"
            ]
         }
      }
   }
   ```

3. 在项目根目录 `.eslintrc.js` 中添加别名：

   ```js
   module.exports = {
       settings: {
           'import/resolver': {
               alias: {
                   map: [
                       ['@web', './resources/views/web'],
                   ],
               },
           },
       },
   }
   ```

4. 在 `package.json` 中添加 `unit` 的 `script`，内容如下：

   ```json5
   {
      "scripts": {
           "dev.unit": "vite --config vite.unit.config.js",
           "build.unit": "vite build --config vite.unit.config.js",
       }
   }
   ```

5. 在 `resources/views`  目录下新建 `unit.blade.php`，并添加内容如下：

   ```php
   <!DOCTYPE html>
   <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
       <meta charset="utf-8">
       <meta http-equiv="X-UA-Compatible" content="IE=edge">
       <meta name="viewport"
             content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui, viewport-fit=cover">
       <meta name="apple-mobile/src-web-app-capable" content="yes">
       <meta name="apple-mobile/src-web-app-status-bar-style" content="black">
       <meta name="author" content="广州职迅信息科技有限公司(https://jobsys.cn)">
       <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
       {{-- 使用自定的 hot file，指定 build 目录， 指定 entry point --}}
       {{Vite::useHotFile('unit.hot')->useBuildDirectory('build/unit')->withEntryPoints(['resources/views/mobile/unit/main.js'])}}
       @inertiaHead
   </head>
   <body>
   @inertia
   </body>
   </html>
   ```

   > 不再直接使用 `@vite()` 指令，而是需要自定义 `hot` 文件，指定 `build` 目录，指定 `entry point`
   ，这样可以避免不同端的 `vite` 配置冲突。

6. 在 `resources/views/mobile`  目录下新建 `unit` 目录，并创建 `main.js` 文件，并添加相应的代码文件

7. 在 `config/ziggy.php` 中添加相应的路由分组:
	```php
	<?php
	return [
		'groups' => [
			'unit' => ['page.unit.*', 'api.unit.*'],
		],
	];
	```
