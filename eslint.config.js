//修改后使用 `npx eslint --clear-cache` 清理缓存
import globals from "globals"
import js from "@eslint/js"
import eslintConfigPrettier from "eslint-config-prettier"
//import importPlugin from "eslint-plugin-import"
import pluginVue from "eslint-plugin-vue"
import prettierPlugin from "eslint-plugin-prettier"
import { FlatCompat } from "@eslint/eslintrc"
import { fileURLToPath } from "url"
import path from "path"

const __filename = fileURLToPath(import.meta.url)

// 为了兼容 airbnb-base 的规则，需要使用 FlatCompat
const compat = new FlatCompat({
	baseDirectory: path.dirname(__filename),
})

export default [
	js.configs.recommended,
	...compat.extends("airbnb-base"),
	...pluginVue.configs["flat/recommended"],
	{
		languageOptions: {
			ecmaVersion: "latest",
			sourceType: "module",
			globals: {
				...globals.browser,
				myCustomGlobal: "readonly",
			},
		},
		plugins: {
			vue: pluginVue,
			//import: importPlugin,
			prettier: prettierPlugin,
		},
		ignores: ["public/*", "node_modules/*", "!.prettierrc.js", "vendor/*", "vite.config.js", "Modules/*", "eslint.config.js"],
		rules: {
			"import/prefer-default-export": "off",
			"import/no-extraneous-dependencies": "off",
			"import/no-duplicates": "off",
			"import/order": "off",
			"import/first": "off",
			"vue/valid-template-root": "off",
			"vue/no-mutating-props": "off",
			"vue/no-reserved-component-names": "off",
			"vue/no-v-html": "off",
			"vue/no-v-text-v-html-on-component": "off",
			"vue/html-indent": "off",
			"vue/attributes-order": "off",
			"vue/attribute-hyphenation": "off",
			"no-param-reassign": "off",
			"no-console": "off",
			"no-restricted-globals": "off",
			"no-debugger": "off",
			"no-use-before-define": "off",
			"no-promise-executor-return": "off",
			"spaced-comment": "off",
		},
		settings: {
			"import/resolver": {
				alias: {
					map: [
						["@", "./resources"],
						["@modules", "./Modules"],
						["@public", "./public"],
						["@mobile", "./resources/views/mobile"],
						["@web", "./resources/views/web"],
						["@manager", "./resources/views/web/manager"],
						["@components", "./resources/views/components"],
						["@student", "./resources/views/web/student"],
						["@mobile-student", "./resources/views/mobile/student"],
						["@mobile-manager", "./resources/views/mobile/manager"],
					],
				},
			},
		},
	},
	eslintConfigPrettier,
]
