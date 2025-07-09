import { fileURLToPath, URL } from "url"
import { defineConfig } from "vite"
import laravel from "laravel-vite-plugin"
import vue from "@vitejs/plugin-vue"
import Components from "unplugin-vue-components/vite"
import { AntDesignVueResolver } from "unplugin-vue-components/resolvers"
import eslintPlugin from "vite-plugin-eslint"
import tailwindcss from "@tailwindcss/vite"

export default defineConfig((mode) => {
	const isDev = mode === "development"
	return {
		plugins: [
			tailwindcss(),
			laravel({
				input: ["resources/views/web/manager/main.js"],
				buildDirectory: "build/manager",
				refresh: false,
			}),
			vue(),
			Components({
				resolvers: [AntDesignVueResolver({ importStyle: "less" })],
			}),
			eslintPlugin({
				include: ["resources/views/web/manager/**/*.{js,vue}"],
			}),
		],
		resolve: {
			alias: [
				{ find: "@", replacement: fileURLToPath(new URL("./resources", import.meta.url)) },
				{ find: "@modules", replacement: fileURLToPath(new URL("./Modules", import.meta.url)) },
				{ find: "@public", replacement: fileURLToPath(new URL("./public", import.meta.url)) },
				{ find: "@web", replacement: fileURLToPath(new URL("./resources/views/web", import.meta.url)) },
				{
					find: "@manager",
					replacement: fileURLToPath(new URL("./resources/views/web/manager", import.meta.url)),
				},
				{
					find: "@student",
					replacement: fileURLToPath(new URL("./resources/views/web/student", import.meta.url)),
				},
				{
					find: "@components",
					replacement: fileURLToPath(new URL("./resources/views/components", import.meta.url)),
				},
			],
			dedupe: ["vue"], // 💡 保证只有一个 Vue 实例
		},
		css: {
			devSourcemap: true, // ✅ 开发环境启用 Source Map
		},
		optimizeDeps: {
			exclude: ["jobsys-newbie"],
		},
		build: {
			sourcemap: isDev,
			rollupOptions: {
				output: {
					manualChunks: {
						"base-chunk": ["lodash-es", "axios", "dayjs"],
						"vue-chunk": ["vue", "@inertiajs/vue3", "@vueuse/core", "pinia"],
						"vendor-chunk": ["@ant-design/icons-vue", "ant-design-vue", "jobsys-newbie", "simplebar-vue"],
					},
				},
			},
		},
		server: {
			cors: true,
			host: "0.0.0.0",
			hmr: {
				overlay: false,
				host: "localhost",
				//host: "192.168.50.28", //For LAN access
			},
			watch: {
				ignored: ["**/*.php"], // 💥 显式忽略 php 文件！
			},
		},
	}
})
