import noAvatar from '@/public/images/no-avatar.webp'
import { getYesterdayISO } from '@/utils/dateFormat'

const CONFIG = {
	appName: 'REZE HRM',
	avatar: noAvatar as string,
	perPage: [5, 10, 15, 20],
	page: 1,
	pageVisible: 5,
	itemPerPage: 10,
	debounceTimeout: 1000,
	maxWidthForm: 800,
	maxWidthAuthForm: 500,
	maxLengthName: 100,
	maxLengthCode: 20,
	sizePhone: 10,
	maxLengthAddress: 255,
	currentDate: getYesterdayISO(),
	maxSizeAvatar: 2 * 1024 * 1024,
	validTypesAvatar: ['image/jpeg', 'image/png', 'image/jpg'],
	minLengthPassword: 8,
	retryAfter: 60,
}

export default CONFIG

