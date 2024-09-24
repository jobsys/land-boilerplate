<template>
	<div class="min-h-[100px] flex items-center justify-center">
		<a-spin></a-spin>
	</div>
</template>

<script setup>
// 这是个中转页面，为了提供逻辑清楚的权限配置而存在

import { inject } from "vue"
import { router } from "@inertiajs/vue3"

const route = inject("route")

router.visit(route("page.manager.todo.approval"), { replace: true })
</script>
