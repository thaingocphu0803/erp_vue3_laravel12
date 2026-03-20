import noAvatar from '@/public/images/no-avatar.webp'

const defaultConfig =  {
	avatar: noAvatar as string,
	perPage: [5, 10, 15, 20] as number[],
	page: 1,
	pageVisible: 5,
	itemPerPage: 10,
	debounceTimeout: 1000,
	maxWidthForm: 800,
	maxLengthName: 100,
	maxLengthCode: 20
}

export default defaultConfig
