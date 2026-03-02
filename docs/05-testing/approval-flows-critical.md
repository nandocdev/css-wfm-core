# Pruebas críticas de flujos de aprobación

Fecha: 2026-03-02
Módulo: Workflow + Intelligence

## Objetivo

Validar los flujos críticos de aprobación que impactan continuidad operativa y trazabilidad:

- Aprobación de permisos por jerarquía.
- Rechazo de permisos por jerarquía.
- Aprobación forzada institucional.

## Cobertura implementada

### 1) Unitarias (Workflow)

Archivo: `tests/Unit/Modules/Workflow/Actions/LeaveApprovalActionsTest.php`

Casos:

- `test_approve_leave_request_changes_status_and_creates_approval`
- `test_reject_leave_request_changes_status_and_creates_approval`

Validaciones principales:

- Cambio de estado de `leave_requests` (`pending` -> `approved|rejected`).
- Registro de trazabilidad en `leave_request_approvals` (step, action, approver).
- Emisión de notificación al solicitante.

### 2) Integración HTTP (Intelligence)

Archivo: `tests/Feature/Modules/Intelligence/ForceApproveInstitutionalExceptionFeatureTest.php`

Caso:

- `test_administrador_can_force_approve_institutional_exception`

Validaciones principales:

- Endpoint real: `POST /intelligence/exceptions/{leaveRequest}/force-approve`.
- Cambio de estado a `approved`.
- Registro de aprobación en `leave_request_approvals`.
- Auditoría en `audit_logs` con acción `force_approved`.

## Estrategia técnica

- Se usa `RefreshDatabase` para aislamiento por prueba.
- Se generan catálogos mínimos requeridos (posición, distrito, incidentes, etc.) por test.
- Se usan roles reales de Spatie para validar autorización de negocio.

## Ejecución

```bash
php artisan test
```

## Criterio de aceptación

Se considera aprobado si:

- Todas las pruebas críticas pasan.
- No hay regresión en pruebas existentes del repositorio.
- Se mantiene la trazabilidad en aprobaciones y auditoría.
