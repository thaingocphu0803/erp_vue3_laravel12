/**
 * Authentication related types
 */

export interface User {
	name: string
	email: string
	avatar: string
}

export interface LoginFormData {
	email: string
	password: string
	rememberMe: Boolean
}

export interface LoginFormError {
	unauthorized: string
	email: string
	password: string
	rememberMe: string
}

export interface VerifyEmailParam {
	id: number | null
	hash: string
}

export interface NewPasswordFormData extends VerifyEmailParam {
	password: string
	password_confirmation: string
}

export interface NewPasswordFormError {
	password: string
	id: string
	hash: string
}

