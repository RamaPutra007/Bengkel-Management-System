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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('kipay_trx_id')->nullable()->index()->after('notes');
            $table->text('kipay_qr_url')->nullable()->after('kipay_trx_id');
            $table->text('kipay_qr_payload')->nullable()->after('kipay_qr_url');
            $table->integer('kipay_amount')->nullable()->after('kipay_qr_payload');
            $table->integer('kipay_fee')->nullable()->after('kipay_amount');
            $table->timestamp('kipay_expired_at')->nullable()->after('kipay_fee');
            $table->string('kipay_provider')->nullable()->after('kipay_expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['kipay_trx_id']);
            $table->dropColumn([
                'kipay_trx_id',
                'kipay_qr_url',
                'kipay_qr_payload',
                'kipay_amount',
                'kipay_fee',
                'kipay_expired_at',
                'kipay_provider',
            ]);
        });
    }
};

