import { defineStore } from "pinia"
import { ref } from "vue"

const useUserStore = defineStore("user", () => {
	const profile = ref({})
	const permissions = ref([])
	const menus = ref([])
	const departments = ref([])
	const isSuperAdmin = ref(false)

	const init = (data) => {
		profile.value = data.profile
		permissions.value = data.permissions
		menus.value = data.menus
		departments.value = data.departments
		isSuperAdmin.value = data.is_super_admin
	}

	return { profile, permissions, menus, departments, isSuperAdmin, init }
})

export default useUserStore
