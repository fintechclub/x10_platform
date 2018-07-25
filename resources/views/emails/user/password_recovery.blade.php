@component('mail::message')

Здравствуйте, {{$name}}

Недавно вы сделали запрос на сброс пароля для учетной записи X10.Fund.
Нажмите кнопку Сбросить пароль, чтобы задать новый пароль.

@component('mail::button', ['url' => $url])
Сбросить пароль
@endcomponent

С уважением,<br>
Команда {{ config('app.name') }}
@endcomponent
