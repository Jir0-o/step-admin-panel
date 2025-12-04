<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_requests', function (Blueprint $table) {
            $table->id();
            $table->string('temp_cart_id', 64)->nullable();
            $table->string('store_login_id', 100)->nullable();
            $table->string('store_name', 255)->nullable();
            $table->string('salesman', 255)->nullable();
            $table->string('customer_mobile', 50)->nullable();
            $table->integer('sales_type')->nullable();
            $table->longText('items_json')->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->decimal('total_vat', 12, 2)->nullable();
            $table->decimal('total_payable', 12, 2)->nullable();
            $table->decimal('discount_requested', 12, 2)->nullable();
            $table->decimal('total_after_discount', 12, 2)->nullable();
            $table->string('pos_callback_url', 255)->nullable();
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->integer('admin_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_requests');
    }
};
