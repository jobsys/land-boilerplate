import { h } from "vue"
import { ApartmentOutlined, HomeOutlined, SettingOutlined, UserOutlined, ToolOutlined } from "@ant-design/icons-vue"

// 主要是为了让后台配置的MENU ICON能正确渲染
export default {
	HomeOutlined: h(HomeOutlined),
	ApartmentOutlined: h(ApartmentOutlined),
	UserOutlined: h(UserOutlined),
	SettingOutlined: h(SettingOutlined),
	ToolOutlined: h(ToolOutlined),
}
