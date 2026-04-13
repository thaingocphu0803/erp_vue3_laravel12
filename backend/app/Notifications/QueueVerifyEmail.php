<?php

namespace App\Notifications;

use App\Trait\AutoGenerate;
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
	use AutoGenerate;

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

		$newVerificationUrl = $this->TransformVerifyEmailUrl($verificationUrl);

		if (parent::$toMailCallback) {
			return call_user_func(parent::$toMailCallback, $notifiable, $newVerificationUrl);
		}

		return $this->buildMailMessage($newVerificationUrl, $notifiable);
	}
}
