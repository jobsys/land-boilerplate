<template>
	<a-card class="hover-card">
		<template #title>
			<div class="flex items-center">
				<component class="text-2xl mr-2" :is="useLucideIcon(ShieldCheck)"></component>
				安全设置
			</div>
		</template>
		<NewbieForm
			ref="edit"
			:submit-url="route('api.manager.configuration.edit')"
			:card-wrapper="false"
			:before-submit="onBeforeSubmit"
			:data="getData('security')"
			full-width
			:form="getForm('security')"
		/>
	</a-card>
</template>

<script setup>
import { inject, reactive } from "vue"
import { ShieldCheck } from "lucide-vue-next"
import { useLucideIcon } from "../compositions/util"

const props = defineProps({
	configurations: {
		type: Array,
		default: () => [],
	},
})

const route = inject("route")

const nameMap = {
	swiper_images: "首页轮播图",
}

const state = reactive({
	activeKey: "security",
})

const onBeforeSubmit = ({ formatForm }) => {
	const configs = []
	Object.keys(formatForm).forEach((key) => {
		configs.push({
			module: "system",
			group: state.activeKey,
			name: key,
			display_name: nameMap[key],
			value: formatForm[key],
		})
	})

	return { configurations: configs }
}

const getData = (group) => {
	const data = {}
	props.configurations.forEach((item) => {
		if (item.group === group) {
			data[item.name] = item.value
		}
	})

	return data
}

const getForm = (group) => {
	if (group === "security") {
		return [
			/*{
				title: "学生密码周期",
				key: "security_student_password_cycle",
				type: "number",
				optional: true,
				width: 150,
				help: "开启后，未改过密码或上次修改密码超过设定天数，则会提醒学生修改密码",
				defaultProps: {
					min: 0,
					addonAfter: "天",
				},
			},
			{
				title: "教职工密码周期",
				key: "security_user_password_cycle",
				type: "number",
				optional: true,
				width: 150,
				help: "开启后，未改过密码或上次修改密码超过设定天数，则会提醒教职工修改密码",
				defaultProps: {
					min: 0,
					addonAfter: "天",
				},
			},*/
			{
				title: "自动退出登录",
				key: "security_auto_logout_time",
				type: "number",
				optional: true,
				width: 150,
				help: "开启后，未操作超过设定时间，则自动退出登录；系统默认值为 120 分钟",
				defaultValue: 120,
				defaultProps: {
					min: 0,
					addonAfter: "分钟",
				},
			},
		]
	}

	return []
}
</script>
