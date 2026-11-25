<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Enums;

/**
 * Where a Run is, as RouteConnex reports it.
 */
enum RouteConnexRunStatus: string
{
    case Created = 'created';
    case Queued = 'queued';
    case Running = 'running';
    case Succeeded = 'succeeded';
    case DryRun = 'dry-run';
    case Failed = 'failed';
    case Errored = 'errored';

    /**
     * Whether the Run is done: `succeeded`, `dry-run`, `failed` or `errored`.
     */
    public function isFinished(): bool
    {
        return match ($this) {
            self::Created, self::Queued, self::Running => false,
            self::Succeeded, self::DryRun, self::Failed, self::Errored => true,
        };
    }
}
