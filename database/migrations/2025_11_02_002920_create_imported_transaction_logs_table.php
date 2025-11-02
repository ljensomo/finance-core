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
        Schema::create('imported_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->onDelete('cascade');
            $table->integer('row_number');
            $table->dateTime('transaction_timestamp')->nullable();
            $table->tinyInteger('status')->default(0); // 0 = failed, 1 = success
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_transaction_logs');
    }
};
