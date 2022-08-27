<?php

namespace Gdronov\ExampleCom;

class ApiClient
{
    // Адрес прописан внутри, так как библиотека написана под конкретный сервис
    const BASE_URI = 'http://example.com/'; // для отладки - https://dgrigory.free.beeceptor.com/

    const STATUS_UNAUTHORIZED = 401;
    const STATUS_OK = 200;
    const STATUS_CREATED = 201;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';

    private $handle;
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->handle = curl_init();
        $this->apiKey = $apiKey;
    }

    public function __destruct()
    {
        if ($this->handle) {
            curl_close($this->handle);
        }
    }

    /**
     * Запрос к сервису Examples.com
     * @param string $httpMethod
     * @param string $apiPath
     * @param array $params
     * @throws ApiException
     */
    public function request(string $httpMethod, string $apiPath, array $params)
    {
        $this->prepareRequest($httpMethod, $apiPath, $params);

        $response = curl_exec($this->handle);
        if ($response === false) {
            throw new ApiException(curl_error($this->handle), ApiException::REQUEST_ERROR);
        }

        $responseCode = curl_getinfo($this->handle, CURLINFO_RESPONSE_CODE);
        if ($responseCode == self::STATUS_UNAUTHORIZED) {
            throw new ApiException('Access denied', ApiException::ACCESS_DENIED);
        }

        if (!in_array($responseCode, [self::STATUS_OK, self::STATUS_CREATED])) {
            throw new ApiException('Operation error', ApiException::OPERATION_ERROR);
        }

        /**
         * Предполагаем, что сервис example.com всегда возвращает json-структуру,
         * в которой обязательно должно быть свойство "result" (м.б. пустым)
         * Например:
         *  {
         *      "result": {
         *          "comments": [
         *              {
         *                  "id": 1,
         *                  "name": "Василий",
         *                  "text": "Первый комментарий"
         *              },
         *              {
         *                  "id": 2,
         *                  "name": "Пётр",
         *                  "text": "Ещё один комментарий"
         *              }
         *          ]
         *      }
         *  }
         */
        $data = json_decode($response, false);
        if (is_object($data) && property_exists($data, 'result')) {
            return $data->result;
        }

        throw new ApiException('Incorrect response', ApiException::INCORRECT_RESPONSE);
    }

    /**
     * @param string $httpMethod
     * @param string $apiPath
     * @param array $params
     */
    private function prepareRequest(string $httpMethod, string $apiPath, array $params): void
    {
        $url = self::BASE_URI . $apiPath;
        $headers = [
            // токен авторизации в сервисе example.com
            'Api-key: ' . $this->apiKey
        ];
        curl_setopt_array(
            $this->handle,
            [
                CURLOPT_CUSTOMREQUEST => $httpMethod,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers
            ]
        );

        switch ($httpMethod) {
            case self::METHOD_GET:
                curl_setopt($this->handle, CURLOPT_URL, $url . ($params ? '?' . http_build_query($params) : ''));
                break;

            case self::METHOD_POST:
            case self::METHOD_PUT:
                curl_setopt($this->handle, CURLOPT_URL, $url);
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($params));
                break;
        }
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
