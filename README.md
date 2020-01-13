[English](#php-library-for-communicating-with-mobizon-sms-http-api) | [Русский](#Библиотека-для-работы-с-АПИ-mobizon)
# PHP library for communicating with Mobizon SMS HTTP API

[PHP class](https://github.com/mobizon/mobizon-php/blob/master/src/MobizonApi.php) for interaction with Mobizon SMS API - mobizon-php.

Minimal required PHP version - 5.3.3

To start sending SMS messages through Mobizon API you should do three simple steps:

1. [Register account](https://mobizon.com/registrationcountries) at [Mobizon](https://mobizon.com/) website
2. [Enable API](https://help.mobizon.com/help/api-docs/sms-api#how-to-set-up-api) in your Mobizon account settings and get API key provided
3. Setup your software to send SMS or make any other activity to fulfil your company business requirements. See [examples](https://github.com/mobizon/mobizon-php/tree/master/docs/examples) for typical scenarios.

API quick start guide is available at [Mobizon knowledge base website](https://help.mobizon.com/help/api-docs/sms-api).

# Composer

[Composer package](https://getcomposer.org/) is available through [packagist](https://packagist.org/packages/mobizon/mobizon-php).

# API methods documentation

You could review online documentation for detailed description of all currently available [API methods](https://help.mobizon.com/help/api-docs), 
input parameters, result formats, [API result codes](https://help.mobizon.com/help/api-docs/other#ApiCode) at [https://help.mobizon.com/help/api-docs](https://help.mobizon.com/help/api-docs).

# Typical cases example code (PHP)

We have started development of typical usage scenarios in [docs/examples](https://github.com/mobizon/mobizon-php/tree/master/docs/examples).
If you have own examples of integration, please help us in improving our code base - just send us some code examples to support@mobizon.com and we will glad to put your code to our repo. 

* [Get balance amount](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/balance.php)
* [Send SMS message](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/send_message.php)
* [Get your alphanames list](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/alphanames.php)
* [Send massive SMS campaign](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/send_mass_sms_campaign.php)
* [Generate SMS messages report](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/generate_messages_report_csv.php)
* [Generate SMS campaigns summary report](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/generate_campaigns_report_csv.php)

[![Latest Stable Version](https://poser.pugx.org/mobizon/mobizon-php/v/stable)](https://packagist.org/packages/mobizon/mobizon-php)
[![Total Downloads](https://poser.pugx.org/mobizon/mobizon-php/downloads)](https://packagist.org/packages/mobizon/mobizon-php)

---

# Библиотека для работы с АПИ Mobizon

[PHP класс](https://github.com/mobizon/mobizon-php/blob/master/src/MobizonApi.php) для работы с Mobizon API - mobizon-php.

Минимальная версия PHP - 5.3.3

Для начала работы с API Вам необходимо сделать три простых шага:

1. [Зарегистрироваться](https://mobizon.com/registrationcountries) в сервисе [Mobizon](https://mobizon.com/)
2. [Включить](https://help.mobizon.com/help/api-docs/sms-api#how-to-set-up-api) в своем аккаунте доступ к API и получить ключ API.
3. Настроить ваше ПО для отправки СМС или других необходимых Вашему бизнес-процессу действий через наше API. См. [документацию API](https://help.mobizon.com/help/api-docs) и [примеры реализации](https://github.com/mobizon/mobizon-php/tree/master/docs/examples).

Подробнее процесс подключения к API описан [на нашем сайте](https://help.mobizon.com/help/api-docs/sms-api).

# Composer

Пакет для [composer](https://getcomposer.org/) доступен в [packagist](https://packagist.org/packages/mobizon/mobizon-php).

# Документация по методам API

Документация с описанием всех доступных на данный момент [методов API](https://help.mobizon.com/help/api-docs), 
их входных параметров, формата возвращаемых данных, списка возможных 
[кодов ответа API](https://help.mobizon.com/help/api-docs/other#ApiCode) доступна по адресу [https://help.mobizon.com/help/api-docs](https://help.mobizon.com/help/api-docs).

# Примеры реализации типичных сценариев

В разделе [docs/examples](https://github.com/mobizon/mobizon-php/tree/master/docs/examples) мы начали собирать для Вас
готовые примеры реализации типичных сценариев работы с API на Вашем сайте. Если у Вас есть собственные рабочие
варианты использования, будем рады разместить их в нашем репо.

* [Отправка одного SMS сообщения](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/send_message.php)
* [Отправка массовой SMS рассылки](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/send_mass_sms_campaign.php)
* [Получение состояния баланса](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/balance.php)
* [Получение списка доступных для использования подписей (альфаимен)](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/alphanames.php)
* [Сохранение отчета о СМС сообщениях по заданным критериям поиска](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/generate_messages_report_csv.php)
* [Сохранение сводного отчета о СМС кампаниях по заданным критериям поиска](https://github.com/mobizon/mobizon-php/blob/master/docs/examples/generate_campaigns_report_csv.php)
