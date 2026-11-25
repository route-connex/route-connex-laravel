<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_connex_apps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('client_id');
            // Encrypted values are longer than 255 characters.
            $table->text('client_secret');
            $table->text('callback_token');
            $table->string('base_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_connex_apps');
    }
};
