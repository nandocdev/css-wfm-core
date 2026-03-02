<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Modules\Security\Providers\ModuleServiceProvider::class,
    App\Modules\Core\Providers\ModuleServiceProvider::class,
    App\Modules\Organization\Providers\ModuleServiceProvider::class,
    App\Modules\Employee\Providers\ModuleServiceProvider::class,
    App\Modules\Team\Providers\ModuleServiceProvider::class,
    App\Modules\Schedule\Providers\ModuleServiceProvider::class,
    App\Modules\Planning\Providers\ModuleServiceProvider::class,
    App\Modules\Intelligence\Providers\ModuleServiceProvider::class,
    App\Modules\Attendance\Providers\ModuleServiceProvider::class,
    App\Modules\Workflow\Providers\ModuleServiceProvider::class,
];
