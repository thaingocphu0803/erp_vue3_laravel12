const navMenu = [
	{
		title: 'common.module.dashboard',
		value: 'dashboard',
		icon: 'mdi-home',
		routeName: 'dashboard',
	},
	{
		title: 'common.module.humanResource',
		value: 'hr',
		icon: 'mdi-account-group',
		children: [
			{
				title: 'common.subModule.department',
				value: 'department',
				icon: 'mdi mdi-sitemap',
				routeName: 'hr.department',
			},
			{
				title: 'common.subModule.employee',
				value: 'employee',
				icon: 'mdi-account-details',
				routeName: 'hr.employee',
			},
		],
	},
	{
		title: 'common.module.myProfile',
		value: 'profile',
		icon: 'mdi-account',
		routeName: 'profile',
	},
	{
		title: 'common.module.attendance',
		value: 'attendance',
		icon: 'mdi-clock-time-ten',
		routeName: 'attendance',
	},
	{
		title: 'common.module.message',
		value: 'message',
		icon: 'mdi-message-text',
		routeName: 'message',
	},
	{
		title: 'common.module.setting',
		value: 'Setting',
		icon: 'mdi-cog',
		routeName: 'setting',
	},
]

const languageMenu = [
	{
		title: 'common.language.english',
		value: 'en',
	},
	{
		title: 'common.language.vietnamese',
		value: 'vi',
	},
]

export { navMenu, languageMenu }
