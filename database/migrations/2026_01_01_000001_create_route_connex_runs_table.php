<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_connex_runs', function (Blueprint $table) {
            $table->id();
            // Not unique: Runs of several Tenants can share one table.
            $table->string('run_id')->index();
            // Exactly one of them is set, checked by the model.
            $table->string('app_name')->nullable();
            // A string, so integer, UUID and MongoDB ObjectId keys all fit.
            $table->string('app_id')->nullable()->index();
            $table->string('service');
            $table->string('type')->nullable();
            // A string, not an enum, so a new status needs no migration.
            $table->string('status');
            $table->json('meta')->nullable();
            $table->json('request')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('purged_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'finished_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_connex_runs');
    }
};
