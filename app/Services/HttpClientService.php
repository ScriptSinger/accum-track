<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class HttpClientService
{
    /**
     * Экземпляр GuzzleHttp\Client.
     *
     * @var Client
     */
    protected $client;

    /**
     * HttpClientService constructor.
     *
     * Конфигурирует клиента с базовым URI, таймаутами, заголовками и опциональным прокси.
     */

    public function __construct()
    {
        $config = [
            // Базовый URI для запросов (настройка в config/services.php или .env)
            'base_uri' => config('services.http.base_uri', 'https://ufamasters.ru'),

            // Таймаут запроса в секундах
            'timeout'  => config('services.http.timeout', 1.0),

            // Заголовки для эмуляции запроса от реального браузера
            'headers'  => [
                'User-Agent'      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
                'Accept'          => 'application/json, text/html, */*',
                'Accept-Encoding' => 'gzip, deflate',
            ],
        ];

        // Если настроен прокси-сервер, добавляем его в конфигурацию
        if ($proxy = config('services.http.proxy')) {
            $config['proxy'] = $proxy;
        }

        $this->client = new Client($config);
    }

    /**
     * Выполняет GET-запрос.
     *
     * @param string $uri URL или относительный путь
     * @param array  $options Дополнительные параметры запроса
     * @return ResponseInterface
     *
     * @throws RequestException При возникновении ошибки запроса
     */
    public function get(string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->client->get($uri, $options);
        } catch (RequestException $e) {
            // Логирование ошибки для последующего анализа
            logger()->error('Ошибка GET-запроса', [
                'uri'     => $uri,
                'options' => $options,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Выполняет POST-запрос.
     *
     * @param string $uri URL или относительный путь
     * @param array  $options Дополнительные параметры запроса
     * @return ResponseInterface
     *
     * @throws RequestException При возникновении ошибки запроса
     */
    public function post(string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->client->post($uri, $options);
        } catch (RequestException $e) {
            // Логирование ошибки для последующего анализа
            logger()->error('Ошибка POST-запроса', [
                'uri'     => $uri,
                'options' => $options,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
