<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('store_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id'); 
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->text('token')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); 
            $table->text('meta')->nullable(); 
            $table->index('store_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_tokens');
    }
};
