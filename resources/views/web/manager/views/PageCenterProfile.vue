<template>
	<NewbieForm
		ref="edit"
		title="个人设置"
		:submit-url="route('api.manager.center.profile.edit')"
		:data="form"
		:form="formColumns()"
		:close="() => useGoBack()"
		:columns="[12, 12]"
		@success="onSuccess"
	/>
</template>
<script setup>
import { inject } from "vue"
import { cloneDeep } from "lodash-es"
import { useGoBack } from "@manager/compositions/util"
import { useRegexRule } from "jobsys-newbie/hooks"

const route = inject("route")

const props = defineProps({
	user: {
		type: Object,
		default: () => {},
	},
})

const form = cloneDeep(props.user)

const formColumns = () => [
	{
		title: "头像",
		key: "avatar",
		type: "uploader",
		tips: "不超过10M",
		defaultProps: {
			accept: ".png,.jpg,.jpeg",
			action: route("api.manager.tool.upload"),
			maxSize: 10,
			multipart: true,
			type: "picture-card",
		},
	},
	{
		break: true,
		title: "用户名",
		key: "name",
		type: "text",
	},
	{
		title: "姓名",
		key: "nickname",
	},
	{
		title: "工号",
		key: "work_num",
		type: "text",
	},
	{
		title: "部门",
		key: "department",
		type: "plain",
		defaultValue: () => props.user.departments?.map((item) => item.name).join(","),
	},
	{
		title: "手机号码",
		key: "phone",
		required: true,
		columnIndex: 1,
		rules: [useRegexRule("phone")],
	},
	{
		title: "办公电话",
		key: "work_phone",
		columnIndex: 1,
	},
	{
		title: "电子邮箱",
		key: "email",
		columnIndex: 1,
		rules: [useRegexRule("email")],
	},
]

const onSuccess = () => location.reload()
</script>
