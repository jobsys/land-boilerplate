import axios from "axios"
//import dayjs from "dayjs"
//import { cloneDeep } from "lodash-es"
import { closeNotify, closeToast, showFailToast, showNotify } from "vant"

/*const formatData = (data) => {
	if (Object.prototype.toString.call(data) === "[object Array]") {
		data.forEach((item, index) => {
			data[index] = formatData(item)
		})
	}

	if (Object.prototype.toString.call(data) === "[object Object]") {
		Object.keys(data).forEach((key) => {
			data[key] = formatData(data[key])
		})
	}

	if (Object.prototype.toString.call(data) === "[object Date]") {
		return dayjs(data).unix()
	}
	if (Object.prototype.toString.call(data) === "[object Boolean]") {
		return data ? 1 : 0
	}
	return data
}*/

export default {
	install(app, options) {
		options = options || {}

		axios.defaults.baseURL = options.baseUrl || "/"
		axios.defaults.withCredentials = true
		axios.defaults.headers.post["Content-Type"] = "multipart/form-data"

		axios.interceptors.request.use(
			(config) => {
				/*const method = config.method.toLocaleLowerCase()

				//这里是 axios 的 Request 请求拦截器，每个请求都会经过这里
				if (method === "post") {
				} else if (method === "get") {
				}*/

				return config
			},
			(error) => {
				// Do something with request error
				return Promise.reject(error)
			},
		)

		axios.interceptors.response.use(
			(response) => {
				if (response.headers["x-inertia"]) {
					return response
				}

				closeToast(true)
				//这里是 axios 的 Response 请求拦截器，每个请求的返回都会经过这里
				if (response.data.status === "NO_PERMISSION") {
					showFailToast("没有权限")
					return Promise.reject(response.data.status)
				}
				if (response.data.status === "NOT_LOGIN") {
					showFailToast("没有登录")
					const redirect = encodeURIComponent(app.config.globalProperties.$route.fullPath)
					app.config.globalProperties.$router.push({ name: "Login", query: { redirect } })
					return Promise.reject(response.data.status)
				}
				return response.data
			},
			(error) => {
				closeToast(true)
				closeNotify()
				showNotify({
					message: `请求出错 :${error.response.status}`,
				})
				return Promise.reject(error)
			},
		)

		app.config.globalProperties.$http = axios
	},
}
