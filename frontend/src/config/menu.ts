const navMenu = [
		{
		title: "Home",
		value: "home",
		icon: "mdi-home",
		routeName:"dashboard"
	},
	{
		title: "Attendance",
		value: "attendance",
		icon: "mdi-clock-time-ten",
		routeName:"dashboard.attendance"
	},
	{
		title: "My Account",
		value: "account",
		icon: "mdi-account",
		routeName:"dashboard.account"
	},
	{
		title: "Human Resources",
		value: "employee",
		icon: "mdi-account-multiple",
		routeName:"dashboard.employee"
	},
	{
		title: "Setting",
		value: "Setting",
		icon: "mdi-cog",
		routeName:"dashboard.setting"
	},
]

const languageMenu = [
	{
		title: 'English',
		value: 'en'
	},
	{
		title: 'Vietnamese',
		value: 'vi'
	}
]

export {navMenu, languageMenu}