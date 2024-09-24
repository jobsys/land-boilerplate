<template>
	<div class="inner-layout-container flex">
		<div>
			<a-tabs v-model:activeKey="state.activeKey" tab-position="left" type="card" class="h-full">
				<a-tab-pane key="approval">
					<template #tab>
						<span>
							<AuditOutlined />
							审核待办
						</span>
					</template>
				</a-tab-pane>
			</a-tabs>
		</div>
		<div class="flex-1">
			<slot></slot>
		</div>
	</div>
</template>

<script setup>
import { reactive } from "vue"
import { AuditOutlined } from "@ant-design/icons-vue"

const state = reactive({
	activeKey: "approval",
})
</script>
