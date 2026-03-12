import { t } from "@/plugins/vueI18n"


// required rule
export const required = (msg: string) => (v: any) => !!v || msg

// valid email rule
export const email = (msg: string) =>  (v: string) =>
	/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || msg

// min length rule
export const minLength = (msg: string, min: number) => (v: any) => v.length >= min || msg

//  has lowercase letter rule
export const hasLowerLetter = (msg: string) => (v: any) => /[a-z]/.test(v) || msg

//  has uppercase letter rule
export const hasUpperLetter = (msg: string) => (v: any) => /[A-Z]/.test(v) || msg

// has number rule
export const hasNumber = (msg: string) => (v: any) => /\d/.test(v) || msg

// has special char rule
export const hasSpecialChar = (msg: string) => (v: any) => /[^A-Za-z0-9]/.test(v) || msg

// alphanumeric only (letters and digits, no spaces or special chars)
export const alphanumeric = (msg: string) => (v: any) => /^[A-Za-z0-9]+$/.test(v) || msg

// unicode-friendly name: allows UTF-8 letters, digits, spaces, hyphens and underscores
export const noSpecialChars = (msg: string) => (v: any) =>
	/^[\p{L}\p{M}\p{N}\s\-_]+$/u.test(v) || msg

// match target rule
export const sameAs = (msg: string, target: any) => (v: any) => v === target || msg

