<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('store_tokens')) {
            return;
        }

        Schema::table('store_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('store_tokens', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }

            if (! Schema::hasColumn('store_tokens', 'meta')) {
                $table->json('meta')->nullable()->after('expires_at');
            }
        });

        Schema::table('store_tokens', function (Blueprint $table) {
            $table->index(['store_id', 'user_id'], 'store_tokens_store_user_idx');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('store_tokens')) {
            return;
        }

        Schema::table('store_tokens', function (Blueprint $table) {
            try {
                $table->dropIndex('store_tokens_store_user_idx');
            } catch (\Throwable $e) {
            }
        });
    }
};
