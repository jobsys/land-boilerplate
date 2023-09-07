<template>
	<ConfigProvider :locale="zhCN">
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
	},
	form: {
		afterFetched: (res) => res.result,
	},
})
</script>
