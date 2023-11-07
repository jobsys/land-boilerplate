<template>
	<ConfigProvider
		:theme="{
			token: {
				colorPrimary: appConfig.primaryColor,
			},
		}"
		:locale="zhCN">
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
import appConfig from "./config"

const route = inject("route")

const provider = reactive({
	table: {
		afterFetched: (res) => ({
			items: res.result.data,
			totalSize: res.result.total,
		}),
	},
	uploader: {
		uploadUrl: route("api.manager.tool.uploadFile"),
		defaultFileItem: {
			id: "id",
			name: "name",
			url: "url",
			path: "path",
			thumbUrl: "thumbUrl",
		},
	},
	editor: {
		uploadUrl: route("api.manager.tool.uploadFile"),
	},
	form: {
		afterFetched: (res) => res.result,
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
