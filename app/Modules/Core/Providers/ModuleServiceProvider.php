<?php

declare(strict_types=1);

namespace App\Modules\Core\Providers;

use App\Contracts\Core\CoreModuleContract;
use App\Contracts\Core\NotificationDispatcherContract;
use App\Modules\Core\Actions\CoreModuleAction;
use App\Modules\Core\Actions\DispatchSystemNotificationAction;
use App\Modules\Attendance\Models\AttendanceIncident;
use App\Modules\Core\Models\IncidentType;
use App\Modules\Core\Observers\CriticalModelAuditObserver;
use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\EmployeeDependent;
use App\Modules\Employee\Models\EmployeeDisease;
use App\Modules\Employee\Models\EmployeeDisability;
use App\Modules\Organization\Models\Department;
use App\Modules\Organization\Models\Directorate;
use App\Modules\Organization\Models\Position;
use App\Modules\Planning\Models\IntradayActivity;
use App\Modules\Planning\Models\WeeklySchedule;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Schedule\Models\BreakTemplate;
use App\Modules\Schedule\Models\Schedule;
use App\Modules\Security\Models\Permission;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use App\Modules\Team\Models\Team;
use App\Modules\Workflow\Models\LeaveRequest;
use App\Modules\Workflow\Models\ShiftSwapRequest;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(CoreModuleContract::class, CoreModuleAction::class);
        $this->app->bind(NotificationDispatcherContract::class, DispatchSystemNotificationAction::class);
    }

    public function boot(): void {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'core');
        $this->registerCriticalAuditObservers();
    }

    private function registerCriticalAuditObservers(): void {
        $observer = $this->app->make(CriticalModelAuditObserver::class);

        foreach ($this->criticalModels() as $modelClass) {
            $modelClass::observe($observer);
        }
    }

    /**
     * @return array<int, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    private function criticalModels(): array {
        return [
            User::class,
            Role::class,
            Permission::class,
            Directorate::class,
            Department::class,
            Position::class,
            Team::class,
            Employee::class,
            EmployeeDependent::class,
            EmployeeDisability::class,
            EmployeeDisease::class,
            IncidentType::class,
            Schedule::class,
            BreakTemplate::class,
            WeeklySchedule::class,
            WeeklyScheduleAssignment::class,
            IntradayActivity::class,
            LeaveRequest::class,
            ShiftSwapRequest::class,
            AttendanceIncident::class,
        ];
    }
}
