<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('teams', function (Blueprint $table): void {
            $table->foreignId('coordinator_employee_id')
                ->nullable()
                ->unique()
                ->after('description')
                ->constrained('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('coordinator_employee_id');
        });
    }
};
