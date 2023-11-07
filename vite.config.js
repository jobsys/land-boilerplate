import { defineConfig } from "vite"
import laravel from "laravel-vite-plugin"
import vue from "@vitejs/plugin-vue"
import Components from "unplugin-vue-components/vite"
import { AntDesignVueResolver } from "unplugin-vue-components/resolvers"
import eslintPlugin from "vite-plugin-eslint"

export default defineConfig({
	plugins: [
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
			include: ["resources/**/*.{js,vue}"],
		}),
	],
	resolve: {
		alias: {
			"@": "/resources",
			"@modules": "/Modules",
			"@public": "/public",
			"@web": "/resources/views/web",
			"@manager": "/resources/views/web/manager",
		},
	},
	build: {
		sourcemap: true,
		rollupOptions: {
			output: {
				manualChunks: {
					"vue-chunk": ["vue", "@inertiajs/vue3", "@vueuse/core", "pinia"],
					"vendor-chunk": [ "@ant-design/icons-vue", "ant-design-vue", "axios", "dayjs", "jobsys-newbie", "lodash-es","simplebar-vue"],
				},
			},
		},
	},
	server: {
		host: "0.0.0.0",
		hmr: {
			host: "localhost",
			// host: '192.168.*.*', //For LAN access
		},
	},
})
