import {h} from "vue"

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

/**
 * 使用 Lucide 图标
 * @see https://lucide.dev/guide/packages/lucide-vue-next
 * @param icon
 * @param props
 */
const useLucideIcon = (icon, props = {}) =>
	h(
		"span",
		{
			class: `anticon ${props.class || ""}`,
			style: props.style || "",
		},
		h(icon, { size: "1em", ...props }),
	)

export { useGoBack, useLucideIcon }
