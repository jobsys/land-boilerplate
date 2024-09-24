/**
 * 返回逻辑
 * @param [defaultUrl]
 */
const useGoBack = (defaultUrl) => {
	if (document.referrer === "" && defaultUrl) {
		window.location.href = defaultUrl
	} else {
		window.history.back()
	}
}

export { useGoBack }
