<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table): void {
            $table->dateTime('dispatched_at')->nullable()->after('notify_at');
            $table->index('dispatched_at');
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table): void {
            $table->dropIndex(['dispatched_at']);
            $table->dropColumn('dispatched_at');
        });
    }
};

