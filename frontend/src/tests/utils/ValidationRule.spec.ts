import {
	email,
	hasLowerLetter,
	hasNumber,
	hasSpecialChar,
	hasUpperLetter,
	minLength,
	required,
	sameAs,
} from '@/utils/validationRule'
import { it, describe, expect } from 'vitest'

describe('validation rule', () => {
	const errorMessage = 'error_mesage'

	// testing the required rule
	describe('required rule', () => {
		it.each([
			['empty input', '', errorMessage],
			['null input', null, errorMessage],
			['undefined input', undefined, errorMessage],
			['isset input', 'ok', true],
		])('%s', (message, input, expected) => {
			expect(required(errorMessage)(input)).toBe(expected)
		})
	})

	// testing the email format rule
	describe('email rule', () => {
		it.each([
			['invalid email', 'test@gmail', errorMessage],
			['valid email', 'test@gmail.com', true],
		])('%s', (message, input, expected) => {
			expect(email(errorMessage)(input)).toBe(expected)
		})
	})

	// testing the min length rule
	describe('min length rule', () => {
		const length = 8
		it.each([
			['incorrect min length', 'test', length, errorMessage],
			['correct min length', 'test@gmail.com', length, true],
		])('%s', (message, input, length, expected) => {
			expect(minLength(errorMessage, length)(input)).toBe(expected)
		})
	})

	// testing the uppercase letter
	describe('uppercase rule', () => {
		it.each([
			['not uppercase letter', 'test', errorMessage],
			['uppercase letter', 'Test', true],
		])('%s', (message, input, expected) => {
			expect(hasUpperLetter(errorMessage)(input)).toBe(expected)
		})
	})

	// testing the lowercase letter
	describe('lowercase rule', () => {
		it.each([
			['not lowercase letter', 'TEST', errorMessage],
			['lowercase letter', 'tEST', true],
		])('%s', (message, input, expected) => {
			expect(hasLowerLetter(errorMessage)(input)).toBe(expected)
		})
	})

	// testing the number letter
	describe('number rule', () => {
		it.each([
			['not number letter', 'a', errorMessage],
			['number letter', '1', true],
		])('%s', (message, input, expected) => {
			expect(hasNumber(errorMessage)(input)).toBe(expected)
		})
	})

	// testing the special letter
	describe('special char rule', () => {
		it.each([
			['not special letter', 'a', errorMessage],
			['special letter', '@', true],
		])('%s', (message, input, expected) => {
			expect(hasSpecialChar(errorMessage)(input)).toBe(expected)
		})
	})

	// testing the matching rule
	describe('matching rule', () => {
		const target = 'admin'
		it.each([
			['not matched target', 'test', target, errorMessage],
			['matched target', 'admin', target, true],
		])('%s', (message, input, target, expected) => {
			expect(sameAs(errorMessage, target)(input)).toBe(expected)
		})
	})
})

