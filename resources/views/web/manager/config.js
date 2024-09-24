import { get } from "lodash-es"

/**
 *
 * @param {String} [key] 键路径
 * @param {*} [defaultValue] 默认值
 * @return {*}
 */
export function useAppConfig(key, defaultValue) {
	let config = {}

	const code = window.landAppInit?.customer?.code

	switch (code) {
		default:
			config = {
				theme: {
					token: {
						colorPrimary: "#fe862f",
					},
				},
			}
	}
	if (key) {
		return get(config, key, defaultValue)
	}
	return config
}
