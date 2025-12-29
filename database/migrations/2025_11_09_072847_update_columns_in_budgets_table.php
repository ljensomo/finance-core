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
        Schema::table('budgets', function (Blueprint $table) {
            $table->renameColumn('month_year', 'start_date');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->string('start_date')->change();
            $table->string('end_date')->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->renameColumn('start_date', 'month_year');
            $table->dropColumn('end_date');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->string('month_year')->change();
        });
    }
};
