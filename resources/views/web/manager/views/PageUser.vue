<template>
	<div class="main-content">
		<a-row :gutter="10">
			<a-col span="5">
				<a-card size="small" :loading="state.departmentLoading.loading">
					<template #title>
						<a-input-search v-model:value="state.searchValue" allow-clear placeholder="搜索部门" size="small" />
					</template>
					<template #extra>
						<a-dropdown>
							<MoreOutlined style="font-size: 16px"></MoreOutlined>
							<template #overlay>
								<a-menu @click="toggleTree">
									<a-menu-item key="open">展开全部</a-menu-item>
									<a-menu-item key="close">折叠全部</a-menu-item>
								</a-menu>
							</template>
						</a-dropdown>
					</template>
					<a-tree
						block-node
						:tree-data="state.departmentOptions"
						:expanded-keys="state.expandedKeys"
						:field-names="{ children: 'children', title: 'name', key: 'id' }"
						@expand="onExpand"
						@select="onSelect"
					>
						<template #title="{ name }">
							<span v-if="name.indexOf(state.searchValue) > -1">
								{{ name.substring(0, name.indexOf(state.searchValue)) }}
								<span style="color: #f50">{{ state.searchValue }}</span>
								{{ name.substring(name.indexOf(state.searchValue) + state.searchValue.length) }}
							</span>
							<span v-else>{{ name }}</span>
						</template>
					</a-tree>
				</a-card>
			</a-col>
			<a-col span="19">
				<NewbieTable ref="list" title="账号列表" :columns="columns()" :url="route('api.manager.user.items', state.searchExtra)" row-selection>
					<template #functional>
						<NewbieButton v-if="$auth('api.manager.user.edit')" type="primary" :icon="h(PlusOutlined)" @click="onEdit(false)"
							>新增账号
						</NewbieButton>

						<NewbieButton
							v-if="$auth('api.manager.user.department')"
							:icon="h(ApartmentOutlined)"
							@click="onBeforeEditDepartment"
							class="ml-2"
							>分配部门
						</NewbieButton>
					</template>
				</NewbieTable>
			</a-col>
		</a-row>
		<NewbieModal v-model:visible="state.showUserEditor" title="账号编辑" :width="600">
			<NewbieForm
				ref="edit"
				:fetch-url="state.url"
				:auto-load="!!state.url"
				:submit-url="route('api.manager.user.edit')"
				:card-wrapper="false"
				:before-submit="onBeforeSubmit"
				:after-fetched="onAfterFetch"
				:form="getForm()"
				:close="closeEditor"
				:submit-disabled="!$auth('api.manager.user.edit')"
				@success="closeEditor(true)"
			/>
		</NewbieModal>

		<NewbieModal v-model:visible="state.showDepartmentEditEditor" title="分配部门" :width="600">
			<a-form :model="departmentEditForm" class="py-4">
				<a-form-item label="分配部门" name="department_ids" required>
					<a-tree-select
						v-model:value="departmentEditForm.department_ids"
						style="width: 400px"
						:tree-data="state.departmentSelectableOptions"
						tree-checkable
						tree-check-strictly
						allow-clear
						:show-checked-strategy="SHOW_ALL"
						placeholder="请选择部门，可多选"
					/>
				</a-form-item>

				<a-form-item label="分配方式" required>
					<a-radio-group v-model:value="departmentEditForm.mode" button-style="solid">
						<a-radio-button value="append">添加</a-radio-button>
						<a-radio-button value="overwrite">覆盖</a-radio-button>
					</a-radio-group>
					<template #help>
						<p class="mt-1">添加：在用户原属部门的基础上追加本次的部门设置<br />覆盖：将移除用户原有部门信息，以本次设置为准</p>
					</template>
				</a-form-item>

				<a-divider />

				<a-form-item :wrapper-col="{ offset: 10 }">
					<NewbieButton :fetcher="state.editDepartmentFetcher" type="primary" @click="onSaveDepartment">保存 </NewbieButton>
				</a-form-item>
			</a-form>
		</NewbieModal>
	</div>
</template>

<script setup>
import { ApartmentOutlined, DeleteOutlined, EditOutlined, MoreOutlined, PlusOutlined } from "@ant-design/icons-vue"
import { NewbiePassword, useTableActions } from "jobsys-newbie"
import { useFetch, useDateFormat, useModalConfirm, useProcessStatusSuccess } from "jobsys-newbie/hooks"
import { h, inject, nextTick, reactive, ref, watch } from "vue"
import { Button, message, Tag, TreeSelect } from "ant-design-vue"
import { cloneDeep } from "lodash-es"
import { useSm3 } from "jobsys-newbie/hooks"

const { SHOW_ALL } = TreeSelect

const props = defineProps({
	departments: {
		type: Array,
		default: () => [],
	},
	roles: {
		type: Array,
		default: () => [],
	},
})

const auth = inject("auth")
const route = inject("route")

const list = ref(null)
const edit = ref(null)

const departmentOptions = cloneDeep(props.departments)
departmentOptions.unshift({ id: "-1", name: "未分配" })

const departmentSelectableOptions = cloneDeep(props.departments)

const state = reactive({
	departmentLoading: { loading: false },
	departmentOptions,
	departmentSelectableOptions,
	searchValue: "",
	expandedKeys: [],
	searchExtra: {},
	roleOptions: props.roles.map((item) => ({ label: item.display_name, value: item.id })),
	showUserEditor: false,
	url: "",
	showPassword: false,

	showDepartmentEditEditor: false,
	editDepartmentFetcher: { loading: false },
})

const departmentEditForm = reactive({
	user_ids: [],
	department_ids: [],
	mode: "append",
})

const allKeys = []
let inExpandKey = []

const toggleTree = ({ key }) => {
	if (key === "open") {
		state.expandedKeys = cloneDeep(allKeys)
	} else {
		state.expandedKeys = []
	}
}

const onExpand = (keys) => {
	state.expandedKeys = keys
}

const onSelect = (keys) => {
	state.searchExtra = {
		department_id: keys.length ? keys[0] : "",
	}
	nextTick(() => {
		list.value.doFetch()
	})
}

const getAllKey = (tree) => {
	for (let i = 0; i < tree.length; i += 1) {
		const node = cloneDeep(tree[i])
		allKeys.push(node.id)
		if (node.children) {
			getAllKey(node.children)
		}
	}
}

getAllKey(props.departments)

const searchChildrenName = (name, tree) => {
	const result = []
	for (let i = 0; i < tree.length; i += 1) {
		const node = cloneDeep(tree[i])
		let has = false
		let hasC = false
		if (node.name.indexOf(name) > -1) {
			has = true
		}
		if (node.children) {
			node.children = searchChildrenName(name, node.children)
			if (!node.children || node.children.length === 0) {
				delete node.children
			} else {
				hasC = true
			}
		}
		if (has || hasC) {
			if (hasC) {
				inExpandKey.push(node.id)
			}
			result.push(node)
		}
	}
	return result
}

watch(
	() => state.searchValue,
	(value) => {
		inExpandKey = []
		state.departmentOptions = searchChildrenName(value, props.departments)
		nextTick(() => {
			state.expandedKeys = cloneDeep(inExpandKey)
		})
	},
)

const onBeforeEditDepartment = () => {
	const selectedRows = list.value.getSelection()

	if (!selectedRows.length) {
		message.error("请先勾选数据")
		return
	}
	departmentEditForm.user_ids = selectedRows.map((item) => item.id)
	departmentEditForm.mode = "append"
	departmentEditForm.department_ids = []

	state.showDepartmentEditEditor = true
}

const onSaveDepartment = async () => {
	const data = cloneDeep(departmentEditForm)

	data.department_ids = data.department_ids.map((item) => item.value)

	const res = await useFetch(state.editDepartmentFetcher).post(route("api.manager.user.department"), data)

	useProcessStatusSuccess(res, () => {
		message.success("保存成功")
		state.showDepartmentEditEditor = false
		list.value.doFetch()
	})
}

const getForm = () => {
	return [
		{
			key: "name",
			title: "用户名",
			required: true,
		},
		{
			key: "work_num",
			title: "工号",
		},
		{
			key: "phone",
			required: true,
			title: "手机号码",
		},
		{
			key: "departments",
			title: "所属部门",
			type: "tree-select",
			width: 200,
			options: props.departments,
			defaultProps: {
				treeCheckable: true,
				treeCheckStrictly: true,
				showCheckedStrategy: TreeSelect.SHOW_ALL,
				treeNodeFilterProp: "name",
				fieldNames: {
					children: "children",
					label: "name",
					value: "id",
				},
			},
			help: "可后续在账号管理中进行修改，可多选",
		},
		{
			key: "roles",
			title: "用户角色",
			type: "select",
			width: 200,
			options: state.roleOptions,
			defaultProps: {
				mode: "multiple",
			},
			help: "可后续在账号管理中进行修改，可多选",
		},

		{
			key: "password",
			title: "用户密码",
			message: "请填写密码",
			customRender: ({ submitForm }) => {
				if (submitForm.id) {
					// 显示一个按钮，点击后显示密码输入框
					return h("div", {}, [
						!state.showPassword
							? h(
									Button,
									{
										type: "primary",
										onClick() {
											state.showPassword = true
										},
									},
									{ default: () => "修改密码" },
							  )
							: null,
						state.showPassword
							? h(NewbiePassword, {
									modelValue: submitForm.password,
									placeholder: "请输入密码",
									style: { width: "200px" },
									onChange(e) {
										submitForm.password = e.target.value
									},
							  })
							: null,
					])
				}
				// 直接显示密码输入框
				return h(NewbiePassword, {
					modelValue: submitForm.password,
					placeholder: "请输入密码",
					style: { width: "200px" },
					onChange(e) {
						submitForm.password = e.target.value
					},
				})
			},
		},
		{
			key: "email",
			title: "电子邮箱",
		},
		{
			key: "is_active",
			title: "账号状态",
			type: "switch",
			options: ["正常", "禁用"],
			defaultValue: true,
		},
	]
}

const onEdit = (id) => {
	state.url = id ? route("api.manager.user.item", { id }) : ""
	state.showUserEditor = true
}

const closeEditor = (isRefresh) => {
	if (isRefresh) {
		list.value.doFetch()
	}
	state.showUserEditor = false
}

const onDelete = (item) => {
	const modal = useModalConfirm(
		`您确认要删除 ${item.name} 吗？`,
		async () => {
			try {
				const res = await useFetch().post(route("api.manager.user.delete"), { id: item.id })
				modal.destroy()
				useProcessStatusSuccess(res, () => {
					message.success("删除成功")
					list.value.doFetch()
				})
			} catch (e) {
				modal.destroy(e)
			}
		},
		true,
	)
}

const onBeforeSubmit = ({ formatForm }) => {
	if (formatForm.password) {
		formatForm.password = useSm3(formatForm.password)
	}
	formatForm.departments = formatForm.departments.map((item) => item.value)
	return formatForm
}

const onAfterFetch = (res) => {
	const data = res.result
	data.departments = data.departments.map((item) => ({ label: item.name, value: item.id }))
	data.roles = data.roles.map((item) => item.id)
	return data
}

/**
 * @return {Array.<TableColunmConfig>}
 */
const columns = () => {
	return [
		{
			title: "姓名",
			dataIndex: "name",
			filterable: true,
			width: 100,
		},
		{
			title: "工号",
			dataIndex: "work_num",
			filterable: true,
			width: 100,
		},
		{
			title: "手机号码",
			dataIndex: "phone",
			filterable: true,
			width: 130,
		},
		{
			title: "电子邮箱",
			dataIndex: "email",
			filterable: true,
			ellipsis: true,
			width: 130,
		},
		{
			title: "用户角色",
			key: "roles",
			width: 100,
			customRender({ record }) {
				return h(
					"div",
					{},
					record.roles.length
						? record.roles.map((item) => h(Tag, { color: "blue" }, { default: () => item.display_name }))
						: h(Tag, {}, { default: () => "未分配" }),
				)
			},
			filterable: {
				key: "role_id",
				type: "select",
				options: [{ label: "未分配", value: -1 }, ...state.roleOptions],
				conditions: ["equal"],
				expandable: true,
			},
		},
		{
			title: "所属部门",
			key: "departments",
			width: 100,
			customRender({ record }) {
				return h(
					"div",
					{},
					record.departments.length
						? record.departments.map((item) => h(Tag, { color: "cyan" }, { default: () => item.name }))
						: h(Tag, {}, { default: () => "未分配" }),
				)
			},
		},
		{
			title: "账号状态",
			key: "is_active",
			width: 80,
			align: "center",
			customRender({ record }) {
				return useTableActions({
					type: "tag",
					name: record.is_active ? "正常" : "禁用",
					props: { color: record.is_active ? "green" : "red" },
				})
			},
		},
		{
			title: "上次登录时间",
			key: "last_login_at",
			width: 100,
			customRender({ record }) {
				return h("span", {}, useDateFormat(record.last_login_at))
			},
		},
		{
			title: "上次登录IP",
			dataIndex: "last_login_ip",
			width: 100,
		},
		{
			title: "操作",
			width: 160,
			key: "operation",
			align: "center",
			fixed: "right",
			customRender({ record }) {
				const actions = []

				if (auth("api.manager.user.edit")) {
					actions.push({
						name: "编辑",
						props: {
							icon: h(EditOutlined),
							size: "small",
						},
						action() {
							onEdit(record.id)
						},
					})
				}

				if (auth("api.manager.user.delete")) {
					actions.push({
						name: "删除",
						props: {
							icon: h(DeleteOutlined),
							size: "small",
						},
						action() {
							onDelete(record)
						},
					})
				}

				return useTableActions(actions)
			},
		},
	]
}
</script>
