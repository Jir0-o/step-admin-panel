<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('discount_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pos_store_name')->nullable();      // optional human name
            $table->unsignedInteger('pos_store_id')->nullable(); // store id from POS
            $table->unsignedBigInteger('temp_cart_id')->nullable(); // pos temp_cart_id
            $table->unsignedInteger('requested_by')->nullable(); // pos user id (salesman)
            $table->decimal('requested_amount', 12, 2)->nullable();
            $table->text('note')->nullable();
            $table->string('pos_callback_url')->nullable(); // POS endpoint for callback
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->unsignedInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_requests');
    }
}

