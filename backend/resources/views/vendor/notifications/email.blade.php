<x-mail::message>
{{-- Header --}}
<x-slot:header>
	<x-mail::header :url="config('app.url')">
		<img src="{{ asset('favicon.ico') }}" alt="{{ config('app.name') }}" style="width: 48px; height: 48px;">
	</x-mail::header>
</x-slot:header>

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# {{ __('mail.whoops') }}
@else
# {{ __('mail.hello') }}
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
{{ __('mail.regards') }}<br>
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
{{ __('mail.verify_email.trouble_clicking', ['actionText' => $actionText]) }}
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('mail.all_rights_reserved') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::message>
