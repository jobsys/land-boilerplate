<template>
	<a-tabs v-model:activeKey="state.activeKey">
		<a-tab-pane key="export">
			<template #tab>
				<span> <ExportOutlined /> 导出记录 </span>
			</template>

			<NewbieTable
				ref="exportTableRef"
				:filterable="false"
				:url="route('api.manager.import-export.items', { mode: 'export' })"
				:columns="exportColumns()"
				class="hover-card"
				:table-props="{ size: 'small' }"
			>
			</NewbieTable>
		</a-tab-pane>
		<a-tab-pane key="import">
			<template #tab>
				<span>
					<ImportOutlined />
					导入记录
				</span>
			</template>
			<NewbieTable
				ref="importTableRef"
				:filterable="false"
				:url="route('api.manager.import-export.items', { mode: 'import' })"
				:columns="importColumns()"
				class="hover-card"
				:table-props="{ size: 'small' }"
			>
			</NewbieTable>
		</a-tab-pane>
	</a-tabs>

	<NewbieModal v-model:visible="state.showReviewModal" title="导出审核" :width="1200">
		<ApprovalBox
			@after-approved="onAfterApproved"
			:tasks="state.currentItem?.approval_tasks"
			:histories="state.currentItem?.approval_task_histories"
			:current-task="state.currentItem?.current_task"
		></ApprovalBox>
	</NewbieModal>
</template>

<script setup>
import { useTableActions } from "jobsys-newbie"
import { h, inject, nextTick, reactive, ref } from "vue"
import { message, Tag } from "ant-design-vue"
import { AuditOutlined, DownloadOutlined, ExportOutlined, ImportOutlined } from "@ant-design/icons-vue"
import { useFetch, useHiddenForm, useProcessStatusSuccess } from "jobsys-newbie/hooks"
import ApprovalBox from "@modules/Approval/Resources/views/web/components/ApprovalBox.vue"
import { useIntervalFn } from "@vueuse/core"

const props = defineProps({
	tab: { type: String, default: "" },
})

const exportTableRef = ref()
const importTableRef = ref()

const route = inject("route")

const statusMap = {
	approved: { color: "green", text: "审核通过" },
	rejected: { color: "red", text: "审核驳回" },
	pending: { color: "blue", text: "待审核" },
	skipped: { color: "gray", text: "跳过" },
	updated: { color: "#333", text: "审核对象更新" },
}

const state = reactive({
	activeKey: props.tab || "export",
	showReviewModal: false,
	currentItem: null,
})

useIntervalFn(() => {
	if (state.activeKey === "export") {
		if (exportTableRef.value) {
			const items = exportTableRef.value.getData()
			if (items?.length && items.filter((item) => item.status === "processing")?.length) {
				exportTableRef.value?.doFetch()
			}
		}
	}
}, 10000)

const onDownload = (item) => {
	if (item.approval_status !== "approved" && item.type === "export") {
		message.error("导出任务未通过审核，无法下载")
		return
	}
	useHiddenForm({ url: route("api.manager.import-export.download"), data: { task_id: item.task_id } }).submit()
}

const onReview = async (item) => {
	item.isLoading = true
	const res = await useFetch().get(route("api.manager.import-export.item.approve", { id: item.id }))
	item.isLoading = false

	useProcessStatusSuccess(res, () => {
		state.currentItem = res.result
		nextTick(() => {
			state.showReviewModal = true
		})
	})
}

const onAfterApproved = () => {
	state.showReviewModal = false
	exportTableRef.value?.doFetch(true)
}

const exportColumns = () => [
	{
		title: "任务名称",
		width: 400,
		dataIndex: "task_name",
	},
	{
		title: "审核状态",
		width: 100,
		align: "center",
		key: "approval_status",
		customRender({ record }) {
			return useTableActions({
				type: "tag",
				props: {
					color: statusMap[record.approval_status]?.color || "blue",
				},
				name: statusMap[record.approval_status]?.text || "待审核",
			})
		},
	},
	{
		title: "任务状态",
		width: 160,
		dataIndex: "status",
		align: "center",
		customRender({ record }) {
			if (record.status === "pending") {
				return h(Tag, { color: "blue" }, () => "未开始")
			}
			if (record.status === "failed") {
				return h(Tag, { color: "red" }, () => "导出失败")
			}
			if (record.status === "processing") {
				const elements = [h(Tag, { color: "orange" }, () => "处理中")]

				if (record.progress && record.progress.total_rows) {
					elements.push(h(Tag, { color: "cyan" }, () => `${record.progress.current_row}/${record.progress.total_rows}`))
				}
				return elements
			}
			return h(Tag, { color: "green" }, () => "已完成")
		},
	},
	{
		title: "导出数据量",
		width: 120,
		align: "center",
		dataIndex: "total_count",
	},
	{
		title: "操作人",
		width: 100,
		dataIndex: ["creator", "name"],
	},
	{
		title: "操作时间",
		width: 160,
		dataIndex: "created_at",
	},
	{
		title: "操作",
		width: 160,
		key: "operation",
		align: "center",
		fixed: "right",
		customRender({ record }) {
			const actions = []

			if (record.status === "processing") {
				actions.push({
					name: "导出中",
					props: {
						icon: h(ExportOutlined),
						size: "small",
						loading: true,
					},
				})
			}

			if (record.status === "done") {
				actions.push({
					name: "下载导出文件",
					props: {
						icon: h(DownloadOutlined),
						size: "small",
						disabled: record.approval_status !== "approved",
					},
					action() {
						onDownload(record)
					},
				})
			}

			if (record.can_approve) {
				actions.push({
					name: "审核",
					props: {
						icon: h(AuditOutlined),
						size: "small",
						loading: record.isLoading,
					},
					action() {
						onReview(record)
					},
				})
			}

			return useTableActions(actions)
		},
	},
]

const importColumns = () => [
	{
		title: "任务名称",
		width: 100,
		dataIndex: "task_name",
	},
	{
		title: "操作人",
		width: 60,
		dataIndex: ["creator", "name"],
	},
	{
		title: "操作时间",
		width: 100,
		dataIndex: "created_at",
	},
	{
		title: "任务状态",
		width: 60,
		dataIndex: "status",
		align: "center",
		customRender({ record }) {
			if (record.status === "pending") {
				return h(Tag, { color: "blue" }, () => "待处理")
			}
			if (record.status === "processing") {
				return h(Tag, { color: "yellow" }, () => "处理中")
			}
			return h(Tag, { color: "green" }, () => "已完成")
		},
	},
	{
		title: "任务结束时间",
		width: 100,
		dataIndex: "ended_at",
	},
	{
		title: "任务用时",
		width: 60,
		dataIndex: "duration",
		align: "center",
	},
	{
		title: "操作",
		width: 160,
		key: "operation",
		align: "center",
		fixed: "right",
		customRender({ record }) {
			const actions = []

			actions.push({
				name: "下载数据文件",
				props: {
					icon: h(DownloadOutlined),
					size: "small",
				},
				action() {
					onDownload(record)
				},
			})

			return useTableActions(actions)
		},
	},
]
</script>
