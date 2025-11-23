<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountRequestItemsTable extends Migration
{
    public function up()
    {
        Schema::create('discount_request_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('discount_request_id')->index();
            $table->unsignedBigInteger('temp_cart_item_id')->nullable(); // original POS temp item id
            $table->unsignedBigInteger('temp_cart_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('barcode', 40)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('price', 12, 2)->nullable(); // original item price
            $table->decimal('line_discount', 12, 2)->nullable(); // requested line discount
            $table->text('meta')->nullable(); // JSON snapshot (color/size, etc.)
            $table->timestamps();

            $table->foreign('discount_request_id')->references('id')->on('discount_requests')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_request_items');
    }
}
