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
        Schema::create('wishlist_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->tinyInteger('type')->default(1)->comment('1 = Purchase, 2 = Travel');
            $table->string('icon', 50)->nullable()->comment('FontAwesome class, e.g. fa-solid fa-laptop');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_categories');
    }
};
