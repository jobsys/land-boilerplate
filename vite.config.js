import { defineConfig } from "vite"
import laravel from "laravel-vite-plugin"
import vue from "@vitejs/plugin-vue"
import Components from "unplugin-vue-components/vite"
import { AntDesignVueResolver } from "unplugin-vue-components/resolvers"
import eslintPlugin from "vite-plugin-eslint"

export default defineConfig({
	css: {
		preprocessorOptions: {
			less: {
				modifyVars: {
					"primary-color": "#247277",
					"link-color": "#247277",
					"border-radius-base": "0.25rem",
				},
				javascriptEnabled: true,
			},
		},
	},
	plugins: [
		laravel({
			input: ["resources/views/web/manager/main.js"],
			buildDirectory: "build/manager",
			refresh: false,
		}),
		vue({
			template: {
				transformAssetUrls: {
					base: null,
					includeAbsolute: false,
				},
			},
		}),
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
					"web-chunk": ["@ant-design/icons-vue", "ant-design-vue", "simplebar-vue"],
					"vendor-chunk": ["axios", "dayjs", "lodash-es"],
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
