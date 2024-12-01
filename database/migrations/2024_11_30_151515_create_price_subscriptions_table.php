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
        Schema::create('price_subscriptions', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('url');
            $table->string('email');
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->string('confirmation_token')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->index('user_id');
            $table->index('is_confirmed');

            $table->unique(['user_id', 'url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_subscriptions');
    }
};
