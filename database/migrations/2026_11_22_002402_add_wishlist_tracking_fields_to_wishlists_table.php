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
        Schema::table('wishlists', function (Blueprint $table) {
            $table->decimal('saved_amount', 12, 2)->default(0)->after('estimated_cost');
            $table->decimal('actual_cost', 12, 2)->nullable()->after('saved_amount');
            $table->date('end_date')->nullable()->after('target_date');
            $table->date('completed_at')->nullable()->after('end_date');
            $table->unsignedBigInteger('wishlist_category_id')->nullable()->after('category');
            $table->unsignedBigInteger('expense_id')->nullable()->after('notes');

            $table->index(['user_id', 'status']);
            $table->index('wishlist_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['wishlist_category_id']);
            $table->dropColumn([
                'saved_amount',
                'actual_cost',
                'end_date',
                'completed_at',
                'wishlist_category_id',
                'expense_id',
            ]);
        });
    }
};
