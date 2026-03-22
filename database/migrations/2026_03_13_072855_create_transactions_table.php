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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->foreignUuid('store_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('buyer_id')->constrained()->cascadeOnDelete();
            $table->string('address_id');
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('shipping');
            $table->string('shipping_type');
            $table->decimal('shipping_cost', 26, 2);
            $table->string('tracking_number')->nullable();
            $table->decimal('tax', 26, 2);
            $table->decimal('grand_total', 26, 2);
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->string('delivery_proof')->nullable();
            $table->enum('status', ['unpaid', 'pending', 'processing', 'delivering', 'cancelled', 'completed'])->default('unpaid');
            $table->string('snap_token')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
