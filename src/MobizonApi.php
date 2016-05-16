<?php

namespace Mobizon;

/**
 * Class MobizonApi
 * @package Mobizon
 */
class MobizonApi
{
    /**
     * @var string API key - copy it from your Mobizon account
     */
    protected $apiKey;

    /**
     * @var string HTTP(S) API server address
     */
    protected $apiServer = 'api.mobizon.com';

    /**
     * @var bool Force use HTTP connection instead of HTTPS. Not recommended, but if your system does not support secure connections,
     * then you don't have any other choice.
     */
    protected $forceHTTP = false;

    /**
     * @var bool Set true to force client bypass SSL certificate checks. Changing this option is not recommended,
     * but you could use it in case, if some temporary problems with sertificate transmission takes place.
     * If forceHTTP is true, then this option will be ignored
     */
    protected $skipVerifySSL = false;

    /**
     * @var string API version - don't change it if you are not sure
     */
    protected $apiVersion = 'v1';

    /**
     * @var string Время ожидания ответа API в секундах
     */
    protected $timeout = 30;

    /**
     * @var string Формат ответа API - возможные форматы см. в allowedFormats
     */
    protected $format = 'json';

    /**
     * @var array Возможные форматы ответа API
     */
    protected $allowedFormats = array('xml', 'json');

    /**
     * @var resource
     */
    protected $curl;

    /**
     * @var integer Код последней выполненной операции call
     */
    protected $code = -1;

    /**
     * @var mixed Данные, возвращенные последним запросом к API
     */
    protected $data = array();

    /**
     * @var string Сообщение, полученное с последним ответом сервера
     */
    protected $message = '';

    /**
     * @param string $apiKey Ключ API
     * @param array  $params Параметры API (все параметры не обязательные и установлены в значение по умолчанию
     * <dl>
     *   <dt>(string) format</dt><dd>Формат ответа API, по умолчанию - json</dd>
     *   <dt>(int) timeout</dt><dd>Время ожидания ответа API в секундах, по умолчанию - 30 сек</dd>
     *   <dt>(string) apiVersion</dt><dd>Версия API, по умолчанию - v1</dd>
     *   <dt>(string) apiServer</dt><dd>Сервер API, по умолчанию - api.mobizon.com</dd>
     *   <dt>(string) skipVerifySSL</dt><dd>Не проверять SSL сертификат, по умолчанию - false, проверять</dd>
     *   <dt>(string) forceHTTP</dt><dd>Принудительно использовать обычное HTTP соединение, по умолчанию - false</dd>
     * </dl>
     * @throws Mobizon_ApiKey_Required
     * @throws Mobizon_Curl_Required
     * @throws Mobizon_Error
     * @throws Mobizon_OpenSSL_Required
     */
    public function __construct($apiKey, array $params = array())
    {
        if (empty($apiKey))
        {
            throw new Mobizon_ApiKey_Required('You must provide API key');
        }

        if (!function_exists('curl_init'))
        {
            throw new Mobizon_Curl_Required('The curl extension is required but not currently enabled');
        }

        $this->apiKey = $apiKey;
        foreach ($params as $key => $value)
        {
            $this->__set($key, $value);
        }

        if (!$this->forceHTTP && !in_array('openssl', get_loaded_extensions()))
        {
            throw new Mobizon_OpenSSL_Required('The OpenSSL extension is required but not currently enabled. Install OpenSSL or set forceHTTP=true in params to switch to insecure connection.');
        }

        $this->curl = curl_init();
        if (!$this->forceHTTP && $this->skipVerifySSL)
        {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        }
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mobizon-PHP/1.0.0');
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 600);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
    }

    /**
     * Установка параметров
     * @param string $key Параметр
     * @param mixed  $value Значение параметра
     * @throws Mobizon_Error
     */
    public function __set($key, $value)
    {
        switch ($key)
        {
            case 'format':
                $value = strtolower($value);
                if (!in_array($value, $this->allowedFormats))
                {
                    throw new Mobizon_Error('Format should be one of the following: ' . implode(', ',
                            $this->allowedFormats) . '; ' . $value . ' provided');
                }
                break;
            case 'timeout':
                $value = (int)$value;
                if ($value < 0)
                {
                    throw new Mobizon_Error('Timeout can not be less than 0, ' . $value . ' provided');
                }
                break;
            case 'apiVersion':
                if (substr($value, 0, 1) !== 'v' || (int)substr($value, 1) < 1)
                {
                    throw new Mobizon_Error('Incorrect api version: ' . $value);
                }
                break;
            case 'apiServer':
                if (!preg_match('/^[a-z0-9][-a-z0-9]+(?:\.[a-z0-9][-a-z0-9]*)+$/i', $value))
                {
                    throw new Mobizon_Error('Incorrect api server: ' . $value);
                }
                break;
            case 'code':
                $value = (int)$value;
                if ($value < 0 || $value > 999)
                {
                    throw new Mobizon_Error('Result code can not be handled: ' . $value);
                }
                break;
            case 'skipVerifySSL':
                $value = (bool)$value;
                break;
            case 'forceHTTP':
                $value = (bool)$value;
                break;
            default:
                throw new Mobizon_Error('Incorrect class param or you can not set protected param: ' . $key);
                break;
        }

        $this->{$key} = $value;
    }

    /**
     * Вызов методов API
     * @param string $provider Название провайдера
     * @param string $method Название метода
     * @param array  $postParams Передаваемый в API массив параметров POST
     * @param array  $queryParams Передаваемый в API массив GET параметров
     * @param bool   $returnData Возвращать data вместо code
     * @throws Mobizon_Http_Error
     * @throws Mobizon_Param_Required
     * @return mixed
     */
    public function call(
        $provider,
        $method,
        array $postParams = array(),
        array $queryParams = array(),
        $returnData = false
    )
    {
        $this->code = -1;
        $this->data = array();
        $this->message = '';

        if (empty($provider))
        {
            throw new Mobizon_Param_Required('You must provide "provider" parameter to MobizonApi::call');
        }

        if (empty($method))
        {
            throw new Mobizon_Param_Required('You must provide "method" parameter to MobizonApi::call');
        }

        $queryDefaults = array(
            'api'    => $this->apiVersion,
            'apiKey' => $this->apiKey,
            'output' => $this->format
        );

        $queryParams = $this->applyParams($queryDefaults, $queryParams);
        $url = ($this->forceHTTP ? 'http' : 'https') . '://' . $this->apiServer . '/service/' . strtolower($provider) . '/' . strtolower($method) . '?';
        curl_setopt($this->curl, CURLOPT_URL, $url . http_build_query($queryParams));
        if (!empty($postParams))
        {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($postParams));
        }
        else
        {
            curl_setopt($this->curl, CURLOPT_POST, false);
        }

        $result = curl_exec($this->curl);
        $error = curl_error($this->curl);
        if ($error)
        {
            throw new Mobizon_Http_Error('API call failed: ' . $error);
        }

        $result = $this->decode($result);

        $this->code = $result->code;
        $this->data = $result->data;
        $this->message = $result->message;

        return $returnData ? $this->getData() : (in_array($this->getCode(), array(0, 100)));
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * Накладывает новые значения на дефолтные параметры настроек API
     *
     * @param $defaults
     * @param $params
     * @return array
     */
    protected function applyParams($defaults, $params)
    {
        return array_merge(
            $defaults,
            array_intersect_key(
                $params,
                $defaults
            )
        );
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * Возвращает все данные, полученные из API во время последнего запроса
     * или определенную их часть, если такой элемент существует
     *
     * @param string $subParam
     * @return mixed
     */
    public function getData($subParam = null)
    {
        if (!empty($subParam))
        {
            if (!is_object($this->data))
            {
                return false;
            }

            $subQuery = explode('.', $subParam);
            $data = $this->data;
            foreach ($subQuery as $subKey)
            {
                if (is_object($data) && property_exists($data, $subKey))
                {
                    $data = $data->{$subKey};
                }
                elseif (is_array($data) && array_key_exists($subKey, $data))
                {
                    $data = $data[$subKey];
                }
                else
                {
                    return null;
                }
            }

            return $data;
        }

        return !empty($this->data) ? $this->data : null;
    }

    /**
     * Проверяет, есть ли такие данные в ответе
     *
     * Можно проверить как весь ответ на наличие данных, так и определенный элемент данных
     *
     * @param bool $subParam
     * @return bool
     */
    public function hasData($subParam = null)
    {
        return null !== $this->getData($subParam);
    }

    /**
     * @return string Сообщение, полученное с последним ответом сервера
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Возвращает декодированный ответ API в зависимости от формата обмена
     * @param mixed $responseData
     * @return mixed Декодированный ответ API
     */
    public function decode($responseData)
    {
        switch ($this->format)
        {
            case 'json':
                $result = $this->jsonDecode($responseData);
                break;

            case 'xml':
                $result = $this->xmlDecode($responseData);
                break;

            default:
                $result = false;
                break;
        }

        return $result;
    }

    /**
     * Возвращает объект ответа API из JSON
     * @param string $json
     * @return mixed
     */
    protected function jsonDecode($json)
    {
        return json_decode($json);
    }

    /**
     * Возвращает объект ответа API из XML
     * @param string $string
     * @return object
     * @throws Mobizon_XML_Error
     */
    protected function xmlDecode($string)
    {
        $xml = simplexml_load_string($string);
        if (!$xml)
        {
            throw new Mobizon_XML_Error('Incorrect XML response');
        }

        return $xml;
    }
}

class Mobizon_Error extends \Exception
{
}

class Mobizon_Param_Required extends Mobizon_Error
{
}

class Mobizon_Http_Error extends Mobizon_Error
{
}

class Mobizon_OpenSSL_Required extends Mobizon_Error
{
}

class Mobizon_Curl_Required extends Mobizon_Error
{
}

class Mobizon_ApiKey_Required extends Mobizon_Error
{
}

class Mobizon_XML_Error extends Mobizon_Error
{
}
