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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->integer('subtotal')->default(0);
            $table->integer('tax')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('grand_total')->default(0);
            $table->string('payment_method')->nullable()->comment('cash, transfer');
            $table->string('payment_status')->default('unpaid')->comment('unpaid, paid');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
