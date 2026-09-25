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
        Schema::create('service_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->string('type')->comment('service or sparepart');
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sparepart_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name')->comment('snapshot of name');
            $table->integer('quantity')->default(1);
            $table->integer('price')->default(0);
            $table->integer('subtotal')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_items');
    }
};
