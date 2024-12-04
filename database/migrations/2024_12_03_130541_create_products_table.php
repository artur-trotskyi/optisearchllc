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
        Schema::create('products', function (Blueprint $table): void {
            $table->uuid('id');
            $table->string('title');
            $table->foreignUuid('currency_id')->constrained('currencies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->unique('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
