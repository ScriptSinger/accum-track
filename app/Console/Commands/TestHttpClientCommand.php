<?php

namespace App\Console\Commands;

use App\Services\HttpClientService;
use Illuminate\Console\Command;

class TestHttpClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:test-http-client-command';
    protected $signature = 'test:http-client';



    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирование сервиса HTTP-клиента на основе Guzzle';

    /**
     * Execute the console command.
     */

    protected $httpClientService;

    public function __construct(HttpClientService $httpClientService)
    {
        parent::__construct();
        $this->httpClientService = $httpClientService;
    }


    public function handle()
    {
        $this->info('Выполняется тестовый запрос...');

        try {
            // Выполняем GET-запрос по корневому URL
            $response = $this->httpClientService->get('/');
            // $status = $response->getBody()->getContents();
            $status = $response->getStatusCode();
            $this->info("Запрос выполнен успешно. HTTP статус: {$status}");
        } catch (\Exception $e) {
            $this->error('Произошла ошибка при выполнении запроса: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
