<template>
	<ConfigProvider :theme="theme" :locale="zhCN">
		<NewbieProvider v-bind="provider">
			<slot></slot>
		</NewbieProvider>
	</ConfigProvider>
</template>

<script setup>
import { ConfigProvider } from "ant-design-vue"
import { NewbieProvider } from "jobsys-newbie"
import zhCN from "ant-design-vue/es/locale/zh_CN"
import { inject, reactive } from "vue"
import { useAppStore } from "@manager/stores"

const route = inject("route")
const appStore = useAppStore()

const { theme } = appStore.appSetting

const provider = reactive({
	table: {
		pageSizeKey: "page_size",
		afterFetched: (res) => ({
			items: res.result.data,
			totalSize: res.result.total,
		}),
	},
	uploader: {
		uploadUrl: route("api.manager.tool.upload"),
		defaultFileItem: {
			id: "id",
			name: "name",
			url: "url",
			path: "path",
			thumbUrl: "thumbUrl",
		},
	},
	editor: {
		uploadUrl: route("api.manager.tool.upload"),
	},
	form: {
		afterFetched: (res) => res.result,
		format: {
			date: "YYYY-MM-DD HH:mm",
			attachment: (value) => {
				delete value.url
				delete value.thumbUrl
				return value
			},
		},
	},
	search: {
		valueFormatter: {
			date(value) {
				return value.format("YYYY-MM-DD")
			},
		},
	},
})
</script>
