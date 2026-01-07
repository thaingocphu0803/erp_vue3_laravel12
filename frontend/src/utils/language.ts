// get local storage locale
export const getLanguage = () => localStorage.getItem('locale') || 'en'

// set local storage locale
export const setLanguage = (locale: string) => localStorage.setItem('locale', locale)