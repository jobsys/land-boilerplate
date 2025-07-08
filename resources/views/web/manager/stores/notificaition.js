import { defineStore } from "pinia"
import { ref } from "vue"
import { find, findIndex } from "lodash-es"
import { useFetch, useProcessStatusSuccess } from "jobsys-newbie/hooks"
import { useIntervalFn } from "@vueuse/core"

const useNotificationStore = defineStore("notification", () => {
	const notificationTabs = ref([
		{
			key: "todo",
			title: "待办消息",
			unread: 0,
		},
		{
			key: "notification",
			title: "消息通知",
			unread: 0,
		},
		{
			key: "all",
			title: "全部消息",
			unread: 0,
		},
	])
	const briefUrl = ref("")
	const totalUnread = ref(0)
	const isFetching = ref(false)
	const isIntervalIsActive = ref(false)

	const setBriefUrl = (url) => {
		briefUrl.value = url
	}

	const fetchBrief = async () => {
		isFetching.value = true
		const res = await useFetch().get(briefUrl.value)
		useProcessStatusSuccess(res, () => {
			notificationTabs.value.forEach((tab) => {
				tab.unread = res.result[tab.key]
			})
			totalUnread.value = res.result.all
		})
		isFetching.value = false
	}

	const notificationMap = ref({
		notification: {
			pagination: {},
			initPagination(data) {
				notificationMap.value.notification.pagination = data
			},
		},

		todo: {
			pagination: {},
			initPagination(data) {
				notificationMap.value.todo.pagination = data
			},
		},
		all: {
			pagination: {},
			initPagination(data) {
				notificationMap.value.all.pagination = data
			},
		},
	})

	const getNotificationType = (item) => {
		if (item.type.endsWith("Notification")) {
			return "notification"
		}
		if (item.type.endsWith("Todo")) {
			return "todo"
		}
		return "all"
	}

	const findItemInType = (item, type) => find(notificationMap.value[type].pagination.items, (it) => it.id === item.id)

	const findItemIndexInType = (item, type) => findIndex(notificationMap.value[type].pagination.items, (it) => it.id === item.id)

	const read = async (item, type) => {
		if (type === "all") {
			const notify = findItemInType(item, type)
			notify.read_at = true
			const otherType = getNotificationType(item)
			const otherNotify = findItemInType(item, otherType)
			if (otherNotify) {
				otherNotify.read_at = true
			}
		} else {
			const notify = findItemInType(item, type)
			notify.read_at = true
			const otherNotify = findItemInType(item, "all")
			if (otherNotify) {
				otherNotify.read_at = true
			}
		}
		await fetchBrief()
	}

	const readAll = async (type) => {
		if (type === "all") {
			;["all", "notification", "todo"].forEach((ty) => {
				notificationMap.value[ty].pagination.items?.forEach((item) => {
					item.read_at = true
				})
			})
		} else {
			notificationMap.value[type].pagination.items?.forEach((item) => {
				item.read_at = true
			})

			notificationMap.value.all.pagination.items?.forEach((item) => {
				if (getNotificationType(item) === type) {
					item.read_at = true
				}
			})
		}
		await fetchBrief()
	}

	const deleteItem = async (item, type) => {
		if (type === "all") {
			const index = findItemIndexInType(item, type)
			if (index < 0) {
				return
			}
			notificationMap.value.all.pagination.items?.splice(index, 1)
			const otherType = getNotificationType(item)
			const otherIndex = findItemIndexInType(item, otherType)
			if (otherIndex < 0) {
				return
			}
			notificationMap.value[otherType].pagination.items?.splice(otherIndex, 1)
		} else {
			const index = findItemIndexInType(item, type)
			if (index < 0) {
				return
			}

			notificationMap.value[type].pagination.items?.splice(index, 1)
			const otherIndex = findItemIndexInType(item, "all")
			if (otherIndex < 0) {
				return
			}
			notificationMap.value.all.pagination.items?.splice(otherIndex, 1)
		}
		await fetchBrief()
	}

	const deleteAll = async (type) => {
		if (type === "all") {
			notificationMap.value.all.pagination.items = []
			notificationMap.value.notification.pagination.items = []
			notificationMap.value.todo.pagination.items = []
		} else {
			notificationMap.value[type].pagination.items = []
			notificationMap.value.all.pagination.items = notificationMap.value.all.pagination.items?.filter(
				(item) => getNotificationType(item) !== type,
			)
		}
		await fetchBrief()
	}

	if (!isIntervalIsActive.value) {
		isIntervalIsActive.value = true
		useIntervalFn(async () => {
			if (window.landAppInit?.env !== "local" && !isFetching.value && window.isLogin) {
				await fetchBrief()
			}
		}, 60000)
	}
	return { notificationMap, notificationTabs, totalUnread, read, readAll, deleteItem, deleteAll, setBriefUrl }
})

export default useNotificationStore
