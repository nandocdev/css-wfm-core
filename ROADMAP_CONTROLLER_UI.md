# Modulos disponibles

- app/Modules/Analytics/Http/Controllers:
    - AnalyticsMonitoringController.php

- app/Modules/Attendance/Http/Controllers:
    - CoordinatorAttendanceController.php
    - OperatorAttendanceController.php
    - SupervisorEscalationController.php

- app/Modules/Core/Http/Controllers:
    - NotificationCenterController.php

- app/Modules/Employee/Http/Controllers:
    - EmployeeManagementController.php
    - MyEmployeeProfileController.php

- app/Modules/Intelligence/Http/Controllers:
    - IntelligenceController.php

- app/Modules/Organization/Http/Controllers:
    - OrganizationStructureController.php

- app/Modules/Planning/Http/Controllers:
    - CoordinatorBreakController.php
    - IntradayPlanningController.php
    - MyPlanningController.php
    - WeeklyPlanningController.php

- app/Modules/Schedule/Http/Controllers:
    - ScheduleEngineController.php

- app/Modules/Security/Http/Controllers:
    - AuthController.php
    - ForcePasswordChangeController.php
    - UserAdministrationController.php

- app/Modules/Team/Http/Controllers:
    - MyTeamController.php
    - TeamManagementController.php

- app/Modules/Workflow/Http/Controllers:
    - LeaveWorkflowController.php

## Checklist funcional/UI

### Matriz Controller → Método → Vista → Funcionalidad

| Módulo       | Controller                      | Método                   | Vista Blade                          | Funcionalidad de la vista                                                      |
| ------------ | ------------------------------- | ------------------------ | ------------------------------------ | ------------------------------------------------------------------------------ |
| Analytics    | AnalyticsMonitoringController   | `index`                  | `analytics::monitoring.index`        | Dashboard de analytics y monitoreo con KPIs, reportes por rol y exportación.   |
| Attendance   | CoordinatorAttendanceController | `index`                  | `attendance::coordinator.incidents`  | Bandeja del coordinador para consultar y registrar incidencias de asistencia.  |
| Attendance   | OperatorAttendanceController    | `index`                  | `attendance::operator.index`         | Historial personal de asistencia del operador con filtros por fecha.           |
| Attendance   | SupervisorEscalationController  | `index`                  | `attendance::supervisor.escalations` | Vista de escalaciones operativas para supervisor/operador II.                  |
| Core         | NotificationCenterController    | `index`                  | `core::notifications.index`          | Centro de notificaciones del usuario (listado paginado y no leídas).           |
| Employee     | EmployeeManagementController    | `manage`                 | `employee::admin.manage`             | Gestión administrativa de fichas de empleado y carga masiva.                   |
| Employee     | MyEmployeeProfileController     | `show`                   | `employee::profile.my-profile`       | Vista de perfil del empleado autenticado con sus datos laborales/personales.   |
| Intelligence | IntelligenceController          | `index`                  | `intelligence::operations.index`     | Panel de inteligencia operativa para resolución y reproceso de información.    |
| Organization | OrganizationStructureController | `index`                  | `organization::structure.index`      | Visualización de estructura organizacional y jerarquía de puestos.             |
| Planning     | CoordinatorBreakController      | `index`                  | `planning::coordinator.breaks`       | Gestión de sobrescrituras de pausas para miembros del equipo del coordinador.  |
| Planning     | IntradayPlanningController      | `index`                  | `planning::intraday.index`           | Planificación intradía de actividades y asignación de operadores.              |
| Planning     | MyPlanningController            | `current`                | `planning::operator.current`         | Consulta del horario actual del operador.                                      |
| Planning     | MyPlanningController            | `history`                | `planning::operator.history`         | Historial de horarios del operador.                                            |
| Planning     | MyPlanningController            | `myDay`                  | `planning::operator.my_day`          | Timeline operativo del día para el operador.                                   |
| Planning     | MyPlanningController            | `exceptions`             | `planning::operator.exceptions`      | Listado de excepciones que afectan al operador.                                |
| Planning     | WeeklyPlanningController        | `index`                  | `planning::weekly.index`             | Panel semanal para crear, asignar y publicar planificación.                    |
| Schedule     | ScheduleEngineController        | `index`                  | `schedule::engine.index`             | Motor de horarios: CRUD, plantillas de descansos y settings WFM.               |
| Security     | AuthController                  | `showLoginForm`          | `security::auth.login`               | Pantalla de inicio de sesión.                                                  |
| Security     | AuthController                  | `showForgotPasswordForm` | `security::auth.forgot-password`     | Solicitud de enlace de recuperación de contraseña.                             |
| Security     | AuthController                  | `showResetForm`          | `security::auth.reset-password`      | Formulario de restablecimiento con token.                                      |
| Security     | AuthController                  | `showChangePasswordForm` | `security::auth.change-password`     | Cambio de contraseña del usuario autenticado.                                  |
| Security     | AuthController                  | `showProfile`            | `security::auth.profile`             | Perfil del usuario y roles asignados.                                          |
| Security     | ForcePasswordChangeController   | `show`                   | `security::auth.force-password`      | Pantalla de cambio de contraseña obligatorio.                                  |
| Security     | UserAdministrationController    | `manage`                 | `security::admin.users.manage`       | Administración de usuarios, roles y permisos.                                  |
| Team         | MyTeamController                | `show`                   | `team::coordinator.my-team`          | Vista de equipo asignado para el coordinador.                                  |
| Team         | TeamManagementController        | `manage`                 | `team::admin.manage`                 | Gestión administrativa de equipos, miembros y coordinadores.                   |
| Workflow     | LeaveWorkflowController         | `index`                  | `workflow::leave.index`              | Dashboard de workflow: permisos, aprobaciones, cambios de turno y excepciones. |
