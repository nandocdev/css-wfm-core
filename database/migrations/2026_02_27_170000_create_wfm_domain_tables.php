<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('directorates', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('disability_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('disease_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('employment_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('code', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('provinces', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('entity_type', 100);
            $table->unsignedBigInteger('entity_id');
            $table->string('action', 50);
            $table->jsonb('before')->nullable();
            $table->jsonb('after')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['entity_type', 'entity_id']);
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::create('departments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('directorate_id')->constrained('directorates')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['name', 'directorate_id']);
        });

        Schema::create('districts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['name', 'province_id']);
        });

        Schema::create('positions', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('position_code')->unique();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('townships', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['name', 'district_id']);
        });

        Schema::create('incident_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('color')->default('blue');
            $table->boolean('requires_justification')->default(false);
            $table->boolean('affects_availability')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('schedules', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('lunch_minutes')->default(45);
            $table->integer('break_minutes')->default(15);
            $table->integer('total_minutes')->default(480);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('weekly_schedules', function (Blueprint $table): void {
            $table->id();
            $table->date('week_start_date')->unique();
            $table->date('week_end_date');
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('break_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->time('lunch_start');
            $table->time('lunch_end');
            $table->time('break_start');
            $table->time('break_end');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['team_id', 'name']);
        });

        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->string('employee_number')->nullable()->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->date('birth_date');
            $table->string('gender')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('township_id')->constrained('townships')->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('position_id')->constrained('positions')->restrictOnDelete();
            $table->foreignId('employment_status_id')->constrained('employment_statuses')->restrictOnDelete();
            $table->date('hire_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_manager')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('parent_id');
        });

        Schema::create('intraday_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('weekly_schedule_id')->constrained('weekly_schedules')->cascadeOnDelete();
            $table->string('name');
            $table->date('activity_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_participants')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('activity_date');
            $table->index('weekly_schedule_id');
        });

        Schema::create('intraday_activity_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('intraday_activity_id')->constrained('intraday_activities')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['intraday_activity_id', 'employee_id']);
        });

        Schema::create('leave_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('incident_type_id')->constrained('incident_types')->restrictOnDelete();
            $table->string('type')->default('full');
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime');
            $table->text('justification')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('employee_id');
            $table->index('status');
        });

        Schema::create('team_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['team_id', 'employee_id', 'start_date']);
            $table->index('team_id');
            $table->index('employee_id');
        });

        Schema::create('weekly_schedule_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('weekly_schedule_id')->constrained('weekly_schedules')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules')->restrictOnDelete();
            $table->foreignId('break_template_id')->nullable()->constrained('break_templates')->nullOnDelete();
            $table->boolean('is_custom_break')->default(false);
            $table->timestamps();

            $table->unique(['weekly_schedule_id', 'employee_id'], 'weekly_schedule_assignments_weekly_schedule_id_employee_id_uniq');
            $table->index('weekly_schedule_id');
            $table->index('employee_id');
        });

        Schema::create('attendance_incidents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('incident_type_id')->constrained('incident_types')->restrictOnDelete();
            $table->date('incident_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('justification')->nullable();
            $table->foreignId('recorded_by')->constrained('employees')->restrictOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'incident_date']);
        });

        Schema::create('employee_break_overrides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->time('lunch_start');
            $table->time('lunch_end');
            $table->time('break_start');
            $table->time('break_end');
            $table->text('reason');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('employee_id');
        });

        Schema::create('employee_dependents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('relationship');
            $table->date('birth_date');
            $table->boolean('is_dependent')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_disabilities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('disability_type_id')->constrained('disability_types')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->date('diagnosis_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['employee_id', 'disability_type_id']);
        });

        Schema::create('employee_diseases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('disease_type_id')->constrained('disease_types')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->date('diagnosis_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['employee_id', 'disease_type_id']);
        });

        Schema::create('employee_positions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->decimal('fte_percentage', 5, 2)->default(100);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'position_id', 'start_date']);
            $table->index(['employee_id', 'is_primary']);
            $table->index('position_id');
        });

        Schema::create('leave_request_approvals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('leave_request_id')->constrained('leave_requests')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('employees')->restrictOnDelete();
            $table->integer('step');
            $table->string('action');
            $table->text('comments')->nullable();
            $table->timestamp('acted_at');
            $table->timestamps();

            $table->index('leave_request_id');
        });

        Schema::create('shift_swap_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('requester_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('target_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('weekly_schedule_id')->constrained('weekly_schedules')->restrictOnDelete();
            $table->date('swap_date');
            $table->foreignId('requester_assignment_id')->constrained('weekly_schedule_assignments')->restrictOnDelete();
            $table->foreignId('target_assignment_id')->constrained('weekly_schedule_assignments')->restrictOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('target_response_at')->nullable();
            $table->timestamps();

            $table->index('requester_id');
            $table->index('target_id');
            $table->index('swap_date');
            $table->index('status');
        });

        Schema::create('shift_swap_approvals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shift_swap_request_id')->constrained('shift_swap_requests')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('employees')->restrictOnDelete();
            $table->integer('step');
            $table->string('action');
            $table->text('comments')->nullable();
            $table->timestamp('acted_at');
            $table->timestamps();

            $table->index('shift_swap_request_id');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_gender_check CHECK (gender IS NULL OR gender IN ('male', 'female', 'other'))");
            DB::statement("ALTER TABLE weekly_schedules ADD CONSTRAINT weekly_schedules_status_check CHECK (status IN ('draft', 'published'))");
            DB::statement("ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_type_check CHECK (type IN ('partial', 'full'))");
            DB::statement("ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_status_check CHECK (status IN ('pending', 'approved', 'rejected', 'cancelled'))");
            DB::statement("ALTER TABLE leave_request_approvals ADD CONSTRAINT leave_request_approvals_action_check CHECK (action IN ('approved', 'rejected'))");
            DB::statement("ALTER TABLE shift_swap_requests ADD CONSTRAINT shift_swap_requests_status_check CHECK (status IN ('pending', 'accepted', 'rejected', 'approved', 'cancelled'))");
            DB::statement("ALTER TABLE shift_swap_approvals ADD CONSTRAINT shift_swap_approvals_action_check CHECK (action IN ('approved', 'rejected'))");
            DB::statement("ALTER TABLE team_members ADD CONSTRAINT team_members_dates_valid CHECK (end_date IS NULL OR end_date >= start_date)");
            DB::statement("ALTER TABLE employee_positions ADD CONSTRAINT employee_positions_dates_valid CHECK (end_date IS NULL OR end_date >= start_date)");
        }
        DB::statement("CREATE UNIQUE INDEX employee_positions_unique_primary_active_idx ON employee_positions (employee_id) WHERE is_primary = true AND is_active = true AND end_date IS NULL");
    }

    public function down(): void {
        Schema::dropIfExists('shift_swap_approvals');
        Schema::dropIfExists('shift_swap_requests');
        Schema::dropIfExists('leave_request_approvals');
        Schema::dropIfExists('employee_positions');
        Schema::dropIfExists('employee_diseases');
        Schema::dropIfExists('employee_disabilities');
        Schema::dropIfExists('employee_dependents');
        Schema::dropIfExists('employee_break_overrides');
        Schema::dropIfExists('attendance_incidents');
        Schema::dropIfExists('weekly_schedule_assignments');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('intraday_activity_assignments');
        Schema::dropIfExists('intraday_activities');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('break_templates');
        Schema::dropIfExists('weekly_schedules');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('incident_types');
        Schema::dropIfExists('townships');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('employment_statuses');
        Schema::dropIfExists('disease_types');
        Schema::dropIfExists('disability_types');
        Schema::dropIfExists('directorates');
    }
};
