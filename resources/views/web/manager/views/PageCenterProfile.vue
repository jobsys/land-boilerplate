<template>
	<NewbieForm
		ref="edit"
		title="个人设置"
		:submit-url="route('api.manager.center.profile.edit')"
		:data="form"
		:form="getForm()"
		@success="onSuccess"
	/>
</template>
<script setup>
import { inject } from "vue"
import { cloneDeep } from "lodash-es"

const route = inject("route")

const props = defineProps({
	user: {
		type: Object,
		default: () => {},
	},
})

const form = cloneDeep(props.user)

const getForm = () => {
	return [
		{
			key: "name",
			title: "用户名",
			defaultProps: {
				disabled: true,
			},
		},
		{
			key: "work_num",
			title: "工号",
			defaultProps: {
				disabled: true,
			},
		},
		{
			key: "avatar",
			title: "头像",
			type: "uploader",
			tips: "不超过10M",
			defaultProps: {
				accept: ".png,.jpg,.jpeg",
				action: route("api.manager.tool.uploadFile"),
				maxSize: 10,
				multipart: true,
				type: "picture-card",
			},
		},
		{
			key: "phone",
			title: "手机号码",
			required: true,
		},
		{
			key: "email",
			title: "电子邮箱",
		},
	]
}

const onSuccess = () => location.reload()
</script>
