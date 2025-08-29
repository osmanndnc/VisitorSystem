<?php

// app/Logging/CustomizeFormatter.php
namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Illuminate\Log\Logger as IlluminateLogger;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class CustomizeFormatter
{
    public function __invoke(IlluminateLogger $logger): void
    {
        $monolog = $logger->getLogger();

        // okunur tek satır format
        $format = "[%datetime%] %level_name%: %message% %context% %extra%\n";
        $formatter = new LineFormatter($format, 'Y-m-d H:i:s', true, true);

        foreach ($monolog->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
        }

        // Her log satırına istek bilgilerini ekle
        $monolog->pushProcessor(function (array $record) {
            try {
                $record['extra']['request_id'] = request()?->headers->get('X-Request-Id') ?? request()?->id();
                $record['extra']['ip']         = Request::ip();
                $record['extra']['url']        = Request::fullUrl();
                $record['extra']['user_id']    = Auth::id();
            } catch (\Throwable $e) {
                // cli/migration gibi durumlarda request yok olabilir
            }
            return $record;
        });
    }
}
