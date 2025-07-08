<template>
	<a-card title="修改密码">
		<a-alert v-if="tip" :message="tip" type="error" class="mb-4!"></a-alert>

		<a-form :model="form" :label-col="{ span: 4 }" class="w-[460px] mx-auto" @finish="onSubmit">
			<a-form-item label="原密码" required name="old_password" :rules="[{ required: true, message: '请输入原密码' }]">
				<a-input-password v-model:value="form.old_password" />
			</a-form-item>
			<a-form-item
				label="新密码"
				required
				name="password"
				:rules="[{ required: true, message: '请输入新密码' }, { validator: passwordValidator }]"
			>
				<NewbiePassword ref="newPassword" v-model:value="form.password" />
			</a-form-item>
			<a-form-item
				label="确认密码"
				required
				name="confirm_password"
				:rules="[{ required: true, message: '请输入新密码' }, { validator: confirmPasswordValidator }]"
			>
				<NewbiePassword ref="confirmPassword" v-model:value="form.confirm_password" />
			</a-form-item>

			<a-form-item :wrapper-col="{ offset: 4 }">
				<NewbieButton :fetcher="fetcher" :button-props="{ htmlType: 'submit' }" type="primary">修改 </NewbieButton>
			</a-form-item>
		</a-form>
	</a-card>
</template>
<script setup>
import { inject, reactive, ref } from "vue"
import { useSm2 } from "jobsys-newbie/hooks"
import { useFetch, useProcessStatusSuccess } from "jobsys-newbie/hooks"
import { message } from "ant-design-vue"
import { cloneDeep } from "lodash-es"

const props = defineProps({
	tip: { type: String, default: "" },
	sm2PublicKey: { type: String, default: "" },
})

const route = inject("route")

const form = reactive({
	old_password: "",
	password: "",
	confirm_password: "",
})

const newPassword = ref(null)
const confirmPassword = ref(null)

const fetcher = ref({ loading: false })

const passwordValidator = async () => {
	if (newPassword.value.strength < 4) {
		return Promise.reject(new Error("密码强度不够"))
	}
	return Promise.resolve()
}

const confirmPasswordValidator = async () => {
	if (confirmPassword.value.strength < 4) {
		return Promise.reject(new Error("密码强度不够"))
	}
	return Promise.resolve()
}

const onSuccess = () => {
	message.success("修改成功，请牢记您的新密码")
}

const onBeforeSubmit = ({ formatForm }) => {
	if (formatForm.password !== formatForm.confirm_password) {
		message.error("两次密码输入不一致")
		return false
	}
	delete formatForm.confirm_password
	const sm2 = useSm2()
	formatForm.password = sm2.doEncrypt(formatForm.password, props.sm2PublicKey)
	formatForm.old_password = sm2.doEncrypt(formatForm.old_password, props.sm2PublicKey)

	return formatForm
}

const onSubmit = async () => {
	const data = onBeforeSubmit({ formatForm: cloneDeep(form) })

	if (!data) {
		return
	}

	const res = await useFetch(fetcher.value).post(route("api.manager.center.password.edit"), data)

	useProcessStatusSuccess(res, onSuccess)
}
</script>
