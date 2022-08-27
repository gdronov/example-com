<?php

namespace Gdronov\ExampleCom;

use Exception;

class ApiException extends Exception
{
    const ACCESS_DENIED = 1;        // ошибка авторизации
    const REQUEST_ERROR = 2;        // ошибка запроса к сервису
    const INCORRECT_RESPONSE = 3;   // некорректное содержание ответа от сервиса
    const OPERATION_ERROR = 5;
}
