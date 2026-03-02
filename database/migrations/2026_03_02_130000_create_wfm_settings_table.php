<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wfm_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('late_tolerance_minutes')->default(5);
            $table->unsignedInteger('early_leave_tolerance_minutes')->default(5);
            $table->unsignedInteger('approval_threshold_hours')->default(8);
            $table->unsignedInteger('max_overtime_minutes')->default(120);
            $table->boolean('allow_force_approval')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('wfm_settings');
    }
};
