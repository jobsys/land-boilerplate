module.exports = {
	env: {
		browser: true,
		es2021: true,
	},
	extends: [
		"eslint-config-prettier",
		"plugin:import/recommended",
		"eslint:recommended",
		"plugin:vue/vue3-recommended",
		"airbnb-base",
		"plugin:prettier/recommended",
	],
	ignorePatterns: ["public/*", "node_modules/*", "!.prettierrc.js", "vendor/*", "vite.config.js"],
	overrides: [],
	parserOptions: {
		ecmaVersion: "latest",
		sourceType: "module",
	},
	plugins: ["vue"],
	rules: {
		"import/prefer-default-export": "off",
		"import/no-extraneous-dependencies": "off",
		"import/no-duplicates": "off",
		"import/order": "off",
		"import/first": "off",
		"vue/attributes-order": "off",
		"vue/valid-template-root": "off",
		"vue/no-mutating-props": "off",
		"vue/no-reserved-component-names": "off",
		"vue/no-v-html": "off",
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
					["@web", "./resources/views/web"],
					["@manager", "./resources/views/web/manager"],
					["@components", "./resources/views/components"],
				],
			},
		},
	},
}
