<?php

namespace Toropchin\YandexPdd;

use Illuminate\Database\Eloquent\Model;

class YandexPdd extends Model
{
    /**
     * Получает список почтовых ящиков
     * @return mixed
     */
    public static function listEmail()
    {
        $json = file_get_contents('https://pddimp.yandex.ru/api2/admin/email/list', false, stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(
                    [
                        'domain' => config('yandexpdd.domain.name'),
                        'page' => 1,
                        'on_page' => 1000,
                        'token' => config('yandexpdd.domain.token'),
                    ]
                )
            )
        )));

        return self::parseResult($json);
    }

    /**
     * Добавляет email
     * @param $login
     * @param $password
     * @return object
     */
    public static function addEmail($login, $password)
    {
        $json = file_get_contents('https://pddimp.yandex.ru/api2/admin/email/add', false, stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(
                    [
                        'domain' => config('yandexpdd.domain.name'),
                        'login' => $login,
                        'password' => $password,
                        'token' => config('yandexpdd.domain.token'),
                    ]
                )
            )
        )));

        return self::parseResult($json);
    }

    /**
     * Удаляет email
     * @param $uid
     * @return object
     */
    public static function removeEmail($uid)
    {
        $json = file_get_contents('https://pddimp.yandex.ru/api2/admin/email/del', false, stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(
                    [
                        'domain' => config('yandexpdd.domain.name'),
                        'uid' => $uid,
                        'token' => config('yandexpdd.domain.token'),
                    ]
                )
            )
        )));

        return self::parseResult($json);
    }


    /**
     * @param $uid
     * @param $new_pass
     * @return object
     */
    public static function editPass($uid, $new_pass)
    {
        $json = file_get_contents('https://pddimp.yandex.ru/api2/admin/email/edit', false, stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(
                    [
                        'domain' => config('yandexpdd.domain.name'),
                        'uid' => $uid,
                        'password' => $new_pass,
                        'token' => config('yandexpdd.domain.token'),
                    ]
                )
            )
        )));

        return self::parseResult($json);
    }


    /**
     * @param $json
     * @return object
     */
    public static function parseResult($json)
    {
        $encode = json_decode($json);

        if($encode->success == 'ok')
        {
            $result = [
                'success' => true,
            ];

            if(isset($encode->accounts))
            {
                $result = [
                    'success' => true,
                    'accounts' => $encode->accounts,
                ];
            }
        }
        elseif($encode->success == 'error')
        {
            $result = [
                'success' => false,
                'error_id' => $encode->error,
                'error_str' => YandexPdd::parseErrors($encode->error),
            ];
        }

        return (object)$result;
    }

    /**
     * Разбирает ошибки, полученные от сервера
     * @param $error
     * @return string
     */
    public static function parseErrors($error)
    {
        $errors = [
            'unknown' => 'Произошел временный сбой или ошибка работы API (повторите запрос позже)',
            'no_token' => 'Не передан токен',
            'no_ip' => 'Не передан IP',
            'no_domain' => 'Не передан домен',
            'no_password' => 'Не передан пароль',
            'bad_domain' => 'Имя домена не указано, либо не соответствует RFC',
            'passwd-tooshort' => 'Пароль слишком короткий',
            'prohibited' => 'Передано запрещённое имя домена',
            'bad_token' => 'Передан не верный ПДД токен',
            'bad_login' => 'Передан не верный логин',
            'badpasswd' => 'Нельзя использовать такой пароль',
            'no_auth' => 'Не передан заголовок ПДД токен',
            'not_allowed' => 'Пользователю недоступна данная операция (он не является администратором этого домена)',
            'blocked' => 'Домен заблокирован',
            'occupied' => 'Такой почтовый ящик уже существует',
            'domain_limit_reached' => 'Превышено допустимое число подключённых доменов',
            'no_reply' => 'Яндекс.Почта для домена не может установить соединение с сервером-источником для импорта',
            'account_not_found' => 'Ящик не существует',
            'no_uid_or_login' => 'Не передан идентификатор ящика',
        ];

        if(isset($errors[$error]))
        {
            return $errors[$error];
        }
        else return 'Незивестная ошибка';
    }
}
