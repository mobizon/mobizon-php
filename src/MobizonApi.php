<?php

namespace Mobizon;

/**
 * Class MobizonApi
 * @package Mobizon
 */
class MobizonApi
{
    /**
     * @var array Allowed constructor params
     */
    private static $allowedConstructorParams = array(
        'apiServer',
        'apiKey',
        'forceHTTP',
        'skipVerifySSL',
        'apiVersion',
        'timeout',
        'format'
    );

    /**
     * @var string API key - copy it from your Mobizon account
     */
    protected $apiKey;

    /**
     * @var string HTTP(S) API server address. api.mobizon.com is deprecated and will be disabled soon.
     * @deprecated Default value will be removed soon. Only OLD keys will be accepted by this endpoint till it's final shutdown.
     * Correct API domain depending on user site of registration and could be found in 'API connection setup guide'.
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
     * @var string API response timeout in seconds
     */
    protected $timeout = 30;

    /**
     * @var string default API response format - possible formats see in allowedFormats
     */
    protected $format = 'json';

    /**
     * @var array possible API response formats
     */
    private static $allowedFormats = array('xml', 'json');

    /**
     * @var resource CURL internal pointer
     */
    protected $curl;

    /**
     * @var integer Latest API call response code
     */
    protected $code = -1;

    /**
     * @var mixed Latest API call response data
     */
    protected $data = array();

    /**
     * @var string Latest API call response message
     */
    protected $message = '';

    /**
     * @param string $apiKey User API key
     * @param string $apiServer User API server depends on user initial registration site. Correct API domain could be found in 'API connection setup guide'
     * @param array $params API parameters
     *     (string)  format API responce format. Available formats: xml|json. Default: json.
     *     (integer) timeout API response timeout in seconds. Default: 30.
     *     (string)  apiVersion API version. Default: v1.
     *     (string)  apiServer API server to send requests against. Mandatory parameter.
     *     (string)  skipVerifySSL Flag to disable SSL verification procedure during handshake with API server. Default: false (verification should be passed). Omitting if forceHTTP=true
     *     (string)  forceHTTP Flag to forcibly disable SSL connection. Default: false (means all API requests will be made over HTTPS).
     * @throws Mobizon_ApiKey_Required
     * @throws Mobizon_Curl_Required
     * @throws Mobizon_Error
     * @throws Mobizon_OpenSSL_Required
     */
    public function __construct($params = array())
    {
        if (!function_exists('curl_init')) {
            throw new Mobizon_Curl_Required('The curl extension is required but not currently enabled.');
        }
        $args = func_get_args();
        $argc = func_num_args();
        if ($argc > 1) {
            if (is_scalar($args[0])) {
                $this->apiKey = $args[0];
            } else {
                $params = $args[0];
            }
            if (is_scalar($args[1])) {
                $this->apiServer = $args[1];
            } else {
                $params = $args[1];
            }
            if (is_array($args[2])) {
                $params = $args[2];
            }
        }

        $params = array_intersect_key($params, array_fill_keys(static::$allowedConstructorParams, true));
        foreach ($params as $key => $value) {
            $this->__set($key, $value);
        }

        if (empty($this->apiKey)) {
            throw new Mobizon_ApiKey_Required('You must provide API key in constructor params.');
        }

        if (empty($this->apiServer)) {
            throw new Mobizon_Param_Required('You must provide API server host in constructor params.');
        }

        if (!$this->forceHTTP && !in_array('openssl', get_loaded_extensions())) {
            throw new Mobizon_OpenSSL_Required('The OpenSSL extension is required but not currently enabled. Install OpenSSL or set forceHTTP=true in params to switch to insecure connection.');
        }

        $this->curl = curl_init();
        if (!$this->forceHTTP && $this->skipVerifySSL) {
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
     * @param mixed $value Значение параметра
     * @throws Mobizon_Error
     */
    public function __set($key, $value)
    {
        switch ($key) {
            case 'format':
                $value = strtolower($value);
                if (!in_array($value, static::$allowedFormats)) {
                    throw new Mobizon_Error('Format should be one of the following: ' . implode(', ',
                            static::$allowedFormats) . '; ' . $value . ' provided.');
                }
                break;
            case 'timeout':
                $value = (int)$value;
                if ($value < 0) {
                    throw new Mobizon_Error('Timeout can not be less than 0, ' . $value . ' provided.');
                }
                break;
            case 'apiVersion':
                if (substr($value, 0, 1) !== 'v' || (int)substr($value, 1) < 1) {
                    throw new Mobizon_Error('Incorrect api version: ' . $value . '.');
                }
                break;
            case 'apiServer':
                if (!preg_match('/^[a-z0-9][-a-z0-9]+(?:\.[a-z0-9][-a-z0-9]*)+$/i', $value)) {
                    throw new Mobizon_Error('Incorrect api server: ' . $value . '.');
                }
                break;
            case 'code':
                $value = (int)$value;
                if ($value < 0 || $value > 999) {
                    throw new Mobizon_Error('Result code can not be handled: ' . $value . '.');
                }
                break;
            case 'skipVerifySSL':
                $value = (bool)$value;
                break;
            case 'forceHTTP':
                $value = (bool)$value;
                break;
            default:
                throw new Mobizon_Error('Incorrect class param or you can not set protected param: ' . $key . '.');
                break;
        }

        $this->{$key} = $value;
    }

    /**
     * Вызов методов API
     * @param string $provider Название провайдера
     * @param string $method Название метода
     * @param array $postParams Передаваемый в API массив параметров POST
     * @param array $queryParams Передаваемый в API массив GET параметров
     * @param bool $returnData Возвращать data вместо code
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
    ) {
        $this->code = -1;
        $this->data = array();
        $this->message = '';

        if (empty($provider)) {
            throw new Mobizon_Param_Required('You must provide "provider" parameter to MobizonApi::call.');
        }

        if (empty($method)) {
            throw new Mobizon_Param_Required('You must provide "method" parameter to MobizonApi::call.');
        }

        $queryDefaults = array(
            'api' => $this->apiVersion,
            'apiKey' => $this->apiKey,
            'output' => $this->format
        );

        $queryParams = $this->applyParams($queryDefaults, $queryParams);
        $url = ($this->forceHTTP ? 'http' : 'https') . '://' . $this->apiServer . '/service/' . strtolower($provider) . '/' . strtolower($method) . '?';
        curl_setopt($this->curl, CURLOPT_URL, $url . http_build_query($queryParams));
        if (!empty($postParams)) {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($postParams));
        } else {
            curl_setopt($this->curl, CURLOPT_POST, false);
        }

        $result = curl_exec($this->curl);
        $error = curl_error($this->curl);
        if ($error) {
            throw new Mobizon_Http_Error('API call failed: ' . $error . '.');
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
     * Applies new params to default API params
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
        if (!empty($subParam)) {
            if (!is_object($this->data)) {
                return false;
            }

            $subQuery = explode('.', $subParam);
            $data = $this->data;
            foreach ($subQuery as $subKey) {
                if (is_object($data) && property_exists($data, $subKey)) {
                    $data = $data->{$subKey};
                } elseif (is_array($data) && array_key_exists($subKey, $data)) {
                    $data = $data[$subKey];
                } else {
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
        switch ($this->format) {
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
        if (!$xml) {
            throw new Mobizon_XML_Error('Incorrect XML response.');
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
