import { defineStore } from "pinia"
import { ref } from "vue"
import { find, findIndex } from "lodash-es"
import { useFetch, useProcessStatusSuccess } from "jobsys-newbie/hooks"
import { useIntervalFn } from "@vueuse/core"

const useMessageStore = defineStore("message", () => {
	const messageTabs = ref([
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
			messageTabs.value.forEach((tab) => {
				tab.unread = res.result[tab.key]
			})
			totalUnread.value = res.result.all
		})
		isFetching.value = false
	}

	const messageMap = ref({
		notification: {
			pagination: {},
			initPagination(data) {
				messageMap.value.notification.pagination = data
			},
		},

		todo: {
			pagination: {},
			initPagination(data) {
				messageMap.value.todo.pagination = data
			},
		},
		all: {
			pagination: {},
			initPagination(data) {
				messageMap.value.all.pagination = data
			},
		},
	})

	const getMessageType = (item) => {
		if (item.type === "notification") {
			return "notification"
		}
		if (item.type === "todo") {
			return "todo"
		}
		return "all"
	}

	const findItemInType = (item, type) => find(messageMap.value[type].pagination.items, (it) => it.id === item.id)

	const findItemIndexInType = (item, type) => findIndex(messageMap.value[type].pagination.items, (it) => it.id === item.id)

	const read = async (item, type) => {
		if (type === "all") {
			const notify = findItemInType(item, type)
			notify.read_at = true
			const otherType = getMessageType(item)
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
				messageMap.value[ty].pagination.items?.forEach((item) => {
					item.read_at = true
				})
			})
		} else {
			messageMap.value[type].pagination.items?.forEach((item) => {
				item.read_at = true
			})

			messageMap.value.all.pagination.items?.forEach((item) => {
				if (getMessageType(item) === type) {
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
			messageMap.value.all.pagination.items?.splice(index, 1)
			const otherType = getMessageType(item)
			const otherIndex = findItemIndexInType(item, otherType)
			if (otherIndex < 0) {
				return
			}
			messageMap.value[otherType].pagination.items?.splice(otherIndex, 1)
		} else {
			const index = findItemIndexInType(item, type)
			if (index < 0) {
				return
			}

			messageMap.value[type].pagination.items?.splice(index, 1)
			const otherIndex = findItemIndexInType(item, "all")
			if (otherIndex < 0) {
				return
			}
			messageMap.value.all.pagination.items?.splice(otherIndex, 1)
		}
		await fetchBrief()
	}

	const deleteAll = async (type) => {
		if (type === "all") {
			messageMap.value.all.pagination.items = []
			messageMap.value.notification.pagination.items = []
			messageMap.value.todo.pagination.items = []
		} else {
			messageMap.value[type].pagination.items = []
			messageMap.value.all.pagination.items = messageMap.value.all.pagination.items?.filter((item) => getMessageType(item) !== type)
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
	return { messageMap, messageTabs, totalUnread, read, readAll, deleteItem, deleteAll, setBriefUrl }
})

export default useMessageStore
