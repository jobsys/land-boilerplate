/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./resources/**/*.blade.php", "./resources/views/**/*.{vue,js}", "./Modules/**/*.blade.php", "./Modules/**/*.{vue,js}"],
	theme: {
		extend: {},
	},
	plugins: [],
	corePlugins: {
		preflight: false, // <== disable this!
	},
}
