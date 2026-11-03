<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Console\Commands;

use Illuminate\Console\Command;

class RouteConnexCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'route-connex-laravel:placeholder';

    /**
     * The command description.
     */
    protected $description = 'Placeholder Artisan command shipped by the package route-connex-laravel.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('RouteConnex placeholder command executed.');

        return self::SUCCESS;
    }
}
