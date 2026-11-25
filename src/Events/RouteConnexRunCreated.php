<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Events;

use Illuminate\Queue\SerializesModels;
use RouteConnex\Laravel\Models\RouteConnexRun;

/**
 * A Run was stored after the API call that started it.
 */
class RouteConnexRunCreated
{
    use SerializesModels;

    public function __construct(
        public RouteConnexRun $run,
    ) {
        //
    }
}
