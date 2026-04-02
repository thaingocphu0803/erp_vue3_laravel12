import noAvatar from '@/public/images/no-avatar.webp'

const defaultConfig =  {
	avatar: noAvatar as string,
	perPage: [5, 10, 15, 20],
	page: 1,
	pageVisible: 5,
	itemPerPage: 10,
	debounceTimeout: 1000,
	maxWidthForm: 800,
	maxLengthName: 100,
	maxLengthCode: 20,
	minLengthPhone: 10,
	maxLengthAddress: 255,
	currentDate: new Date().toISOString().split('T')[0],
	maxSizeAvatar: 2 * 1024 * 1024,
	validTypesAvatar: ['image/jpeg', 'image/png', 'image/jpg'],
}

export default defaultConfig
