<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class QueueVerifyEmail extends VerifyEmail
{
	// use Queueable;

	public function __construct() {}

	protected function buildMailMessage($url, $notifiable = null)
	{
		$locale = $notifiable && $notifiable->locale ? $notifiable->locale : app()->getLocale();

		$greeting = $locale === 'vi' ? 'Xin chào ' . $notifiable->name . ',' : 'Hello ' . $notifiable->name . ',';
		$subject = $locale === 'vi' ? 'Xác thực địa chỉ Email' : 'Verify Email Address';
		$line1 = $locale === 'vi' ? 'Vui lòng click vào nút bên dưới để xác thực địa chỉ email của bạn.' : 'Please click the button below to verify your email address.';
		$action = $locale === 'vi' ? 'Xác thực Email' : 'Verify Email Address';
		$line2 = $locale === 'vi' ? 'Nếu bạn không tạo tài khoản, xin vui lòng bỏ qua email này.' : 'If you did not create an account, no further action is required.';

		return (new MailMessage)
			->subject($subject)
			->greeting($greeting)
			->line($line1)
			->action($action, $url)
			->line($line2)
			->salutation(new \Illuminate\Support\HtmlString("Regards,<br>Reze HR"));
	}

	public function toMail($notifiable)
	{
		$verificationUrl = $this->verificationUrl($notifiable);

		$parse = parse_url($verificationUrl);
		parse_str($parse['query'], $query);

		$verificationUrl = str_replace(config('app.url') . '/api', '', $verificationUrl);

		$newUrl = config('app.frontend_url') . '?verify_url=' . urlencode($verificationUrl);

		dd($newUrl);
		if (parent::$toMailCallback) {
			return call_user_func(parent::$toMailCallback, $notifiable, $newUrl);
		}

		return $this->buildMailMessage($newUrl, $notifiable);
	}

	protected function verificationUrl($notifiable)
	{
		if (static::$createUrlCallback) {
			return call_user_func(static::$createUrlCallback, $notifiable);
		}

		return URL::temporarySignedRoute(
			'verification.verify',
			Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
			[
				'id' => $notifiable->getKey(),
				'hash' => sha1($notifiable->getEmailForVerification()),
			]
		);
	}
}
