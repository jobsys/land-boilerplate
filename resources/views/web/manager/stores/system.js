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

	fetchSystemSettings()

	//fetchDictionaries(["level_type"])

	return { lang, dictionaries }
})

export default useSystemStore
