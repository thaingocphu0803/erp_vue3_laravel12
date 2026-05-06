<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueueVerifyEmail extends VerifyEmail implements ShouldQueue
{
	use Queueable;

	public bool $resend;

	public function __construct(bool $resend = false)
	{
		$this->resend = $resend;
	}

	protected function buildMailMessage($url, $notifiable = null)
	{
		$locale = $notifiable->locale ?? app()->getLocale();
		app()->setLocale($locale);

		return (new MailMessage)
			->subject($this->resend ? __('mail.resend_verify_email.subject') : __('mail.verify_email.subject'))
			->greeting($this->resend ? __('mail.resend_verify_email.greeting', ['name' => $notifiable->name]) : __('mail.verify_email.greeting', ['name' => $notifiable->name]))
			->line($this->resend ? __('mail.resend_verify_email.line1') : __('mail.verify_email.line1'))
			->line(__('mail.verify_email.login_email', ['email' => $notifiable->email]))
			->action(__('mail.verify_email.action'), $url)
			->line(__('mail.verify_email.line2', ['count' => config('mail.verification_expire_days')]))
			->salutation(new \Illuminate\Support\HtmlString(__('mail.verify_email.salutation')));
	}

	public function toMail($notifiable)
	{
		$verificationUrl = $this->verificationUrl($notifiable);

		if (parent::$toMailCallback) {
			return call_user_func(parent::$toMailCallback, $notifiable, $verificationUrl);
		}

		return $this->buildMailMessage($verificationUrl, $notifiable);
	}
}
