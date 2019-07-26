#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use BedMaker\Console\Console;
use BedMaker\Console\Commands\RefactorCommand;
use BedMaker\Console\Commands\SqlToElloquentCommand;

$console = new Console();

$console->add(new RefactorCommand());
$console->add(new SqlToElloquentCommand());

$console->run();
