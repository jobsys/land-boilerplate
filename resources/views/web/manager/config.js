import { get } from "lodash-es"

/**
 *
 * @param {String} [key] ¼üÂ·¾¶
 * @param {*} [defaultValue] Ä¬ÈÏÖµ
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
