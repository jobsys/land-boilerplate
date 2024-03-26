import { defineStore } from "pinia"
import { theme } from "ant-design-vue"
import { merge } from "lodash-es"
import { useAppConfig } from "@manager/config"

const useAppStore = defineStore("app", {
	state: () => ({
		appSetting: {
			theme: merge(
				{
					algorithm: theme.defaultAlgorithm, //是否紧凑模式
					token: {}, //设计元素
					components: {}, //组件设计元素
				},
				useAppConfig("theme"),
			),
		},
		appRuntime: {
			isCompactMode: false,
		},
	}),
})

export default useAppStore
