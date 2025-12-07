<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('base_url');
            $table->string('endpoint');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_routes');
    }
};
