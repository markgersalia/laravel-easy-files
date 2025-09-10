<?php

namespace Markgersalia\LaravelEasyFiles\Commands;

use Illuminate\Console\Command;

class LaravelEasyFilesCommand extends Command
{
    public $signature = 'laravel-easy-files';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
