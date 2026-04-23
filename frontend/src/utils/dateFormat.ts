// format date
export const formatDate = (date: Date | string | null) => {
	if (!date) return null

	const dateObj = new Date(date)

	if (isNaN(dateObj.getTime())) return null

	const year = dateObj.getFullYear()
	const month = String(dateObj.getMonth() + 1).padStart(2, '0')
	const day = String(dateObj.getDate()).padStart(2, '0')

	return `${year}/${month}/${day}`
}

// get yesterday ISO
export const getYesterdayISO = () => {
	const date = new Date()
	date.setDate(date.getDate() - 1)
	return date.toISOString().split('T')[0]
}

