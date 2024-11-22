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
        Schema::create('orders', function (Blueprint $table): void {
            $table->uuid('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('product_name');
            $table->decimal('amount', 10, 2)->unsigned();
            $table->string('status');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
