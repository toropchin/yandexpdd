# YandexPdd API для Laravel 5
==========================================================
Пакет для работы с API pdd.yandex.ru для фреймворака Laravel 5


## УСТАНОВКА

Выполните команду:
composer require "toropchin/yandexpdd": "dev-stable"

В файле config/app.php добавьте сервис провайдер в массив providers:
Toropchin\YandexPdd\YandexPddServiceProvider::class

## В терминале:
php artisan vendor:publish

Отредактируйте файл настроек config/yandexpdd.php:


```php
'domain' => [
        'name'      => 'Укажите ваш домен',
        'token'     => 'Укажите токен для вашего домена',
    ]
```

[Токен для домена брать здесь](https://pddimp.yandex.ru/api2/admin/get_token)

## ИСПОЛЬЗОВАНИЕ

```php
//Получение списка email адресов зарегистрированных в домене:
YandexPdd::listEmail()

//Добавление нового email:
YandexPdd::addEmail('login', pass)

//Удаление email (uid получаем при вызове YandexPdd::listEmail().):
YandexPdd::removeEmail('uid')

//Установка нового пароля для почтового ящика с uid.
YandexPdd::editPass('uid', 'new_pass')
```