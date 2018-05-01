@component('mail::message')
# Восстановление пароля

Чтобы восстановить пароль нажмите на кнопку

@component('mail::button', ['url' => $url])
Восстановить пароль
@endcomponent

С уважением,<br>
{{ config('app.name') }}
@endcomponent
