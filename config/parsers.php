
<?php

return [
    'akb-ms' => \App\Services\Parsers\AkbMsParser::class,
    'start-stop' => \App\Services\Parsers\StartStopParser::class,
];

//При изменении config обновлять кэш
 
// sail artisan config:cache