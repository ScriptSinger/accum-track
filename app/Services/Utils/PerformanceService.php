<?php

namespace App\Services\Utils;

use Symfony\Component\Console\Output\OutputInterface;

class PerformanceService
{
    /**
     * Замеряет время выполнения и потребление памяти.
     *
     * @param callable $callback Функция для измерения
     * @param OutputInterface|null $output (необязательно) Для логирования в консоль
     * @param string|null $operationName (необязательно) Название операции
     * @return mixed Результат выполнения функции
     */
    public function measure(callable $callback, ?OutputInterface $output = null, ?string $operationName = null)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $result = $callback();

        $executionTime = round(microtime(true) - $startTime, 4);
        $memoryUsage = round((memory_get_usage() - $startMemory) / 1024 / 1024, 2);

        if ($output) {
            $this->logPerformance($output, $executionTime, $memoryUsage, $operationName);
        }

        return $result;
    }

    /**
     * Логирует результаты измерений в консоль.
     *
     * @param OutputInterface $output
     * @param float $executionTime
     * @param float $memoryUsage
     * @param string|null $operationName
     */
    protected function logPerformance(OutputInterface $output, float $executionTime, float $memoryUsage, ?string $operationName = null): void
    {
        if ($operationName) {
            $output->writeln("<info>{$operationName} выполнено за {$executionTime} секунд</info>");
        } else {
            $output->writeln("<info>Обработка завершена за {$executionTime} секунд</info>");
        }
        $output->writeln("<info>Потребление памяти: {$memoryUsage} МБ</info>");
    }
}
