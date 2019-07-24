<?php

namespace BedMaker\Log;

use SimpleLogger\Logger as SimpleLogger;

class Logger
{
    public static function info($message, $extra = null) {
        $logfile = 'bedmaker.log';
        $channel = 'events';
        $logger  = new SimpleLogger($logfile, $channel);
        $logger->info($message, $extra);
    }

    public static function error($message, $extra = null) {
        $logfile = 'bedmaker.log';
        $channel = 'events';
        $logger  = new SimpleLogger($logfile, $channel);
        $logger->error($message, $extra);
    }
}
