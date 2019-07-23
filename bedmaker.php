#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use BedMaker\Console\Console;
use BedMaker\Console\Commands\BedMakerCommand;

$console = new Console();

$console->add(new BedMakerCommand());

$console->run();
