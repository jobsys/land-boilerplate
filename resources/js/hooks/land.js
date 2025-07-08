import { isArray, isNumber, isObject } from "lodash-es"
import { useTableActions } from "jobsys-newbie"

export function useLandCustomerAsset(path) {
	if (window.landAppInit?.customer?.code) {
		return path.replace("default", window.landAppInit?.customer?.code)
	}
	return path
}

/**
 * 返回逻辑
 * @param [defaultUrl]
 */
export function useGoBack(defaultUrl) {
	if (document.referrer === "" && defaultUrl) {
		window.location.href = defaultUrl
	} else {
		window.history.back()
	}
}

/**
 * 构建 NewbieQuery 查询参数
 * @param queryItem
 */
export function useNewbieQueryGenerate(queryItem) {
	const query = {}

	Object.keys(queryItem).forEach((key) => {
		if (isObject(queryItem[key])) {
			query[key] = queryItem[key]
		} else {
			query[key] = {
				t: "i", // 类型
				c: "=", // 条件
				v: queryItem[key], // 值
			}
		}
	})
	return { _q: query }
}

/**
 * 生成表格列配置
 * @param {string} title - 列标题
 * @param {string|array} dataIndex - 数据字段名
 * @param {number|"switch"|"number"|"datetime"|"date"|"datetimeRange"|"dateRange"|"yearRange"|"isOnlyForQuery"} [type] - 字段类型：
 *   - 数字：指定列宽度
 *   - 'number': 100px
 *   - 'switch': 100px
 *   - 'datetime': 140px
 *   - 'date': 120px
 *   - 'datetimeRange': 160px
 *   - 'dateRange': 140px
 *   - 'yearRange': 140px
 *   - 'isOnlyForQuery': 仅用于查询，不显示在表格中
 * @param {Object} [props] - 其它配置属性，会覆盖默认配置
 * @param {Object|boolean} [filterable] - 是否启用筛选，若为对象，则为筛选配置
 * @returns {Object} 表格列配置对象
 */
export function useTableColumn(title, dataIndex, type, props = {}, filterable = false) {
	const column = {
		title,
		dataIndex,
	}

	if (type === "isOnlyForQuery") {
		return {
			isOnlyForQuery: true,
			...column,
			...props,
			filterable,
		}
	}

	if (["switch", "datetime", "date", "number", "yearRange"].includes(type)) {
		column.align = "center"
	}

	//这些是内容的宽度
	const typeMap = {
		switch: 100,
		number: 100,
		datetime: 140,
		date: 120,
		datetimeRange: 160,
		dateRange: 140,
		yearRange: 140,
	}
	if (isNumber(type)) {
		column.width = type
	} else if (typeMap[type]) {
		//标题每个字按20px算宽度，取标题和内容的最大宽度
		column.width = Math.max(typeMap[type], title.length * 20)
	} else {
		column.width = 100
	}

	if (props.minWidth) {
		column.RC_TABLE_INTERNAL_COL_DEFINE = {
			style: {
				"min-width": `${props.minWidth}px`,
			},
		}
	}

	if (isObject(filterable)) {
		column.filterable = filterable
	}

	if (type === "switch") {
		column.customRender = ({ record }) =>
			useTableActions({
				type: "switch",
				name: props.options || ["是", "否"],
				value: record[dataIndex],
			})
		if (filterable === true) {
			column.filterable = {
				type: "switch",
				options: props.options || ["是", "否"],
			}
		}
	}

	if (type === "datetimeRange" || type === "dateRange" || type === "yearRange") {
		if (isArray(dataIndex) && dataIndex.length === 2) {
			column.customRender = ({ record }) =>
				`${record[dataIndex[0]] || " - "} ${type === "yearRange" ? "-" : "至"} ${record[dataIndex[1]] || " - "}`
		}
		if (filterable === true) {
			column.filterable = {
				type: "date",
			}
		}
	}

	if (type === "date" || type === "datetime") {
		if (filterable === true) {
			column.filterable = {
				type: "date",
			}
		}
	}

	return { ...column, ...props }
}
