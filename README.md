# Библиотека для работы с АПИ Mobizon

[PHP класс](https://github.com/mobizon/mobizon-php/blob/master/src/MobizonApi.php) для работы с API Mobizon - mobizon-php.

Минимальная версия PHP - 5.3.3

Для начала работы с API Вам необходимо сделать три простых шага:

1. [Зарегистрироваться](https://mobizon.kz/) в системе [Mobizon](https://mobizon.kz/)
2. [Включить](https://mobizon.kz/bulk-sms/gateway/api#1) в своем аккаунте доступ к API
3. Получить ключ API - после включения доступа к API Вам будет сгенерирован и показан ключ доступа.

Подробнее процесс подключения к API описан [на нашем сайте](https://mobizon.kz/bulk-sms/gateway/api)

# Composer

Пакет для [composer](https://getcomposer.org/) доступен в [packagist](https://packagist.org/packages/mobizon/mobizon-php).

# Документация по методам API

Документация с описанием всех доступных на данный момент [методов API](http://docs.mobizon.com/api/), 
их входных параметров, формата возвращаемых данных, списка возможных 
[кодов ответа API](http://docs.mobizon.com/api/class-codes.ApiCodes.html) доступна по адресу [http://docs.mobizon.com/api/](http://docs.mobizon.com/api/).

Руководство пользователя API в формате PDF можно скачать [здесь](http://docs.mobizon.com/mobizon-api-guide.pdf)

# Примеры реализации типичных сценариев

В разделе [docs/examples](https://github.com/mobizon/mobizon-php/tree/master/docs/examples) мы начали собирать для Вас
готовые примеры реализации типичных сценариев работы с API на Вашем сайте. Если у Вас есть собственные рабочие
варианты использования, будем рады разместить их в нашем репо.

* [Получение баланса текущего пользователя](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/balance.php)
* [Отправка SMS сообщения](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/send_message.php)
* [Получение списка доступных для использования подписей (альфаимен)](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/alphanames.php)
