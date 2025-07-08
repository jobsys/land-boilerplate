import { defineStore } from "pinia"
import { ref } from "vue"
import { useFetch, useProcessStatusSuccess } from "jobsys-newbie/hooks"

const useSystemStore = defineStore("system", () => {
	const lang = ref({})
	const dictionaries = ref({})

	const fetchSystemSettings = async () => {
		const res = await useFetch().get("api/manager/starter/system/settings")
		useProcessStatusSuccess(res, () => {
			lang.value = res.result.lang
		})
	}

	const fetchDictionaries = async (keys) => {
		keys = keys.filter((key) => !dictionaries.value[key])

		const res = await useFetch().get("api/v1/open/app/dictionary", { params: { keys } })
		useProcessStatusSuccess(res, () => {
			dictionaries.value = { ...dictionaries.value, ...res.result }
		})
	}

	//fetchSystemSettings()

	//整个系统经常使用到的字典项可以先拿出来放在 Store 中
	//fetchDictionaries(["level_type"])

	return { lang, dictionaries, fetchDictionaries }
})

export default useSystemStore
