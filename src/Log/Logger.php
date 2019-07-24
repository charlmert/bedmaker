<?php

namespace BedMaker\Log;

use SimpleLogger\Logger;

class Logger
{
    public static info($message, $extra) {
        $logfile = 'bedmaker.log';
        $channel = 'events';
        $logger  = new Logger($logfile, $channel);
        $logger->info($message, $extra);
    }

    public static error($message, $extra) {
        $logfile = 'bedmaker.log';
        $channel = 'events';
        $logger  = new Logger($logfile, $channel);
        $logger->error($message, $extra);
    }
}
