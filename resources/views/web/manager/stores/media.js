import { defineStore } from "pinia"
import { ref } from "vue"
import { usePage } from "jobsys-newbie/hooks"

const useMediaStore = defineStore("media", () => {
	const categories = ref([])
	const medias = ref({})
	const config = ref({})

	const setCategories = (cats) => {
		categories.value = cats
		cats.forEach((cat) => {
			medias.value[cat.value] = { pagination: {} }
		})
	}

	const setConfig = (conf) => {
		config.value = conf
	}

	const addMedia = (media, category) => {
		medias.value[category].pagination.items.unshift(media)
	}

	const deleteMedia = (media, category) => {
		medias.value[category].pagination.items = medias.value[category].pagination.items.filter((item) => item.id !== media.id)
	}

	const loadMore = (category, params, refresh) => {
		const { pagination } = medias.value[category]
		pagination.uri = config.value.fetchUrl
		Object.keys(params || {}).forEach((key) => {
			const value = params[key]
			if (!value && value !== 0 && value !== false && value !== "0") {
				delete params[key]
			}
		})
		params.category = category
		pagination.params = params
		usePage(pagination, refresh)
	}

	return { categories, medias, config, setCategories, setConfig, addMedia, deleteMedia, loadMore }
})

export default useMediaStore
