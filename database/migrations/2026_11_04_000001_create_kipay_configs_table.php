<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kipay_configs', function (Blueprint $table) {
            $table->id();
            $table->text('api_key')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->string('mode')->default('sandbox')->comment('sandbox, production');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kipay_configs');
    }
};

