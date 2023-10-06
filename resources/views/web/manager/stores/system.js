import { defineStore } from "pinia"
import { ref } from "vue"
import { useFetch, useProcessStatusSuccess } from "jobsys-newbie/hooks"

const useSystemStore = defineStore("system", () => {
	const lang = ref({})

	const fetchSystemSettings = async () => {
		const res = await useFetch().get("api/manager/starter/system/settings")
		useProcessStatusSuccess(res, () => {
			lang.value = res.result.lang
		})
	}

	fetchSystemSettings()

	return { lang }
})

export default useSystemStore
