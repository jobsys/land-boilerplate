export function useLandCustomerAsset(path) {
	if (window.landAppInit?.customer?.code) {
		return path.replace("default", window.landAppInit?.customer?.code)
	}
	return path
}
