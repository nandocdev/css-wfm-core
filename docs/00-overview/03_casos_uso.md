# Casos de Uso — Sistema WFM Call Center CSS

**Proyecto:** Sistema de Gestión de Horarios (WFM) — Call Center CSS
**Versión:** v1.0
**Fecha:** Febrero 2026
**Stack:** PHP 8.3 · Laravel 12 · PostgreSQL 16
**Fase RUP:** Elaboración

---

## 1. Convenciones

| Prefijo | Rol                                             |
| ------- | ----------------------------------------------- |
| UC-COM  | Casos comunes — todos los usuarios autenticados |
| UC-OP   | Operador                                        |
| UC-SUP  | Supervisor                                      |
| UC-COOR | Coordinador                                     |
| UC-JEF  | Jefe                                            |
| UC-WFM  | Analista Workforce                              |
| UC-DIR  | Director                                        |
| UC-ADM  | Administrador del Sistema                       |
| UC-INT  | Intrínsecos del Sistema (automáticos)           |

Cada caso de uso incluye: **actor principal**, **precondición**, **postcondición**, **flujo principal** y **flujo alternativo/excepción** cuando aplica.

---

## 2. Casos de Uso Comunes

### UC-COM-01 — Iniciar sesión

- **Actor:** Usuario autenticado
- **Precondición:** Usuario existe y está activo
- **Postcondición:** Sesión iniciada, `last_login_at` actualizado

**Flujo principal:**

1. El usuario accede a la URL de login
2. Ingresa email y contraseña
3. El sistema valida credenciales contra la tabla `users`
4. Si es correcto: genera token Sanctum y redirige al dashboard
5. Se registra `last_login_at` en la base de datos

**Flujo alternativo:**
Credenciales incorrectas → el sistema muestra error y registra intento fallido. Tras 5 intentos: bloqueo temporal por rate limiting.

---

### UC-COM-02 — Cerrar sesión

- **Actor:** Usuario autenticado
- **Precondición:** Sesión activa
- **Postcondición:** Token Sanctum revocado, sesión finalizada

**Flujo principal:**

1. El usuario accede a "Cerrar sesión"
2. El sistema revoca el token Sanctum activo
3. Redirige al login

---

### UC-COM-03 — Recuperar contraseña

- **Actor:** Usuario
- **Precondición:** Email registrado en el sistema
- **Postcondición:** Token de recuperación enviado por correo (expira en 60 minutos)

**Flujo principal:**

1. El usuario accede a "¿Olvidaste tu contraseña?"
2. Ingresa su email registrado
3. El sistema genera un token temporal y envía el correo
4. El usuario accede al link y define nueva contraseña

---

### UC-COM-04 — Cambiar contraseña propia

- **Actor:** Usuario autenticado
- **Precondición:** Sesión activa
- **Postcondición:** Contraseña actualizada, sesiones anteriores invalidadas

**Flujo principal:**

1. El usuario accede a "Mi perfil" > "Cambiar contraseña"
2. Ingresa contraseña actual, nueva contraseña y confirmación
3. El sistema valida la contraseña actual con `Hash::check()`
4. Valida que la nueva contraseña cumpla reglas de complejidad
5. Actualiza el campo `password` con bcrypt (cost 12)
6. Invalida todos los tokens Sanctum anteriores

**Flujo alternativo:**
Contraseña actual incorrecta → error de validación. Nueva contraseña no cumple reglas → error específico por regla.

---

### UC-COM-05 — Ver perfil de usuario

- **Actor:** Usuario autenticado
- **Precondición:** Sesión activa
- **Postcondición:** Perfil visualizado (solo lectura)

---

### UC-COM-06 — Ver notificaciones

- **Actor:** Usuario autenticado
- **Precondición:** Sesión activa
- **Postcondición:** Lista de notificaciones consultada (aprobaciones, rechazos, cambios)

---

## 3. Casos de Uso — Operador

> El Operador incluye todos los casos comunes (UC-COM-\*) más los siguientes.

### UC-OP-01 — Ver mi información laboral

- **Actor:** Operador
- **Precondición:** Sesión activa, empleado asociado al usuario
- **Postcondición:** Ficha laboral visualizada (cargo, equipo, estado, jerarquía)

---

### UC-OP-02 — Ver mi horario actual

- **Actor:** Operador
- **Precondición:** Sesión activa como Operador
- **Postcondición:** Se muestra el horario efectivo del día/semana actual

**Flujo principal:**

1. El operador accede a "Mi Horario"
2. El sistema consulta `employee_schedules` para su `employee_id`
3. Aplica la lógica de prioridades: excepción > individual > equipo
4. Muestra horario vigente con fechas de inicio/fin y descansos

**Flujo alternativo:**
El empleado no tiene horario asignado → se muestra mensaje informativo.

---

### UC-OP-03 — Ver mi historial de horarios

- **Actor:** Operador
- **Postcondición:** Lista histórica de asignaciones de horario visualizada

---

### UC-OP-04 — Ver mis excepciones

- **Actor:** Operador
- **Postcondición:** Lista de vacaciones, incapacidades y licencias visualizada con estado

---

### UC-OP-05 — Solicitar permiso total (día completo)

- **Actor:** Operador
- **Precondición:** No existe solicitud pendiente para la misma fecha
- **Postcondición:** Solicitud creada en estado `pending`, notificación enviada al supervisor

**Flujo principal:**

1. El operador accede a "Solicitar Permiso"
2. Selecciona tipo "Total" y la fecha deseada
3. Ingresa motivo (campo obligatorio)
4. El sistema valida que no exista solicitud duplicada ni excepción activa para esa fecha
5. Crea registro en `leave_requests` con `status='pending'`
6. Envía notificación al supervisor directo

**Flujo alternativo:**
Fecha ya tiene excepción o solicitud aprobada → el sistema bloquea y muestra el conflicto.

---

### UC-OP-06 — Solicitar permiso parcial (rango horario)

- **Actor:** Operador
- **Precondición:** No existe permiso parcial que se solape en el mismo rango horario
- **Postcondición:** Solicitud creada con `type='parcial'`, horas específicas registradas

---

### UC-OP-07 — Consultar estado de permiso

- **Actor:** Operador
- **Postcondición:** Estado de la solicitud visualizado (pending / approved / rejected)

---

### UC-OP-08 — Solicitar cambio de turno

- **Actor:** Operador (requester)
- **Precondición:** Ambos empleados tienen horario asignado en la fecha objetivo
- **Postcondición:** Solicitud creada, notificación enviada al empleado destino para aceptación

**Flujo principal:**

1. El operador accede a "Cambio de Turno"
2. Busca y selecciona al empleado con quien desea cambiar
3. Indica la fecha del cambio
4. El sistema valida que ambos empleados tengan horario ese día
5. Valida que no existan excepciones activas en esa fecha para ninguno
6. Crea registro en `shift_change_requests` con `status='pending_acceptance'`
7. Notifica al empleado destino para su aceptación

**Flujo alternativo:**
El empleado destino tiene excepción ese día → cambio bloqueado con mensaje explicativo.

---

### UC-OP-09 — Aceptar/rechazar cambio de turno recibido

- **Actor:** Operador (target)
- **Precondición:** Existe solicitud de cambio en `status='pending_acceptance'` dirigida al operador
- **Postcondición:** Solicitud actualizada a `accepted` o `rejected_by_target`

---

### UC-OP-10 — Ver mi asistencia

- **Actor:** Operador
- **Postcondición:** Registro de asistencia del día consultado

---

### UC-OP-11 — Ver historial de asistencias

- **Actor:** Operador
- **Postcondición:** Lista de asistencias por rango de fechas visualizada

---

## 4. Casos de Uso — Supervisor

> El Supervisor incluye todos los casos del Operador más los siguientes.

### UC-SUP-01 — Ver equipo asignado

- **Actor:** Supervisor
- **Postcondición:** Lista de operadores bajo supervisión directa visualizada

---

### UC-SUP-02 — Ver horarios del equipo

- **Actor:** Supervisor
- **Postcondición:** Horarios vigentes de todos los operadores de su equipo visualizados

---

### UC-SUP-03 — Registrar asistencia de operador de su equipo

- **Actor:** Supervisor
- **Precondición:** El supervisor tiene empleados asignados a su supervisión directa
- **Postcondición:** Registro de asistencia creado con estado y supervisor registrado

**Flujo principal:**

1. El supervisor accede a "Asistencia del Día"
2. Selecciona la fecha (por defecto: hoy)
3. Ve la lista de sus operadores directos
4. Para cada uno marca: Asistió / No Asistió
5. El sistema valida que el empleado pertenezca a su equipo (Policy)
6. Crea o actualiza registros en `attendances` con `recorded_by = auth_employee_id`

**Flujo alternativo:**
El supervisor intenta registrar asistencia de empleado fuera de su jerarquía → Policy deniega la acción y registra el intento en `audit_logs`.

---

### UC-SUP-04 — Registrar inasistencia

- **Actor:** Supervisor
- **Postcondición:** Inasistencia registrada con posibilidad de vincular excepción justificante

---

### UC-SUP-05 — Editar asistencia registrada

- **Actor:** Supervisor
- **Precondición:** El registro existe y fue creado por este supervisor
- **Postcondición:** Asistencia actualizada, cambio registrado en `audit_logs`

---

### UC-SUP-06 — Aprobar permiso de operador de su equipo

- **Actor:** Supervisor
- **Precondición:** Existe solicitud en `status='pending'` de un empleado de su equipo
- **Postcondición:** Solicitud aprobada, notificación al operador, excepción creada si aplica

**Flujo principal:**

1. El supervisor accede a "Solicitudes Pendientes"
2. Selecciona la solicitud a revisar
3. El sistema valida que el operador pertenece a su equipo
4. El supervisor indica aprobación o rechazo con justificación
5. El sistema actualiza `leave_requests.status`
6. Si es aprobado: crea registro en `exceptions` con el tipo correspondiente
7. Notifica al operador el resultado

**Flujo alternativo:**
Rechazo sin justificación → validación obliga a ingresar motivo de rechazo.

---

### UC-SUP-07 — Rechazar permiso de operador

- **Actor:** Supervisor
- **Precondición:** Solicitud en estado `pending` de su equipo
- **Postcondición:** Solicitud marcada como `rejected` con motivo obligatorio registrado

---

### UC-SUP-08 — Aprobar cambio de turno (nivel 1)

- **Actor:** Supervisor
- **Precondición:** Ambos empleados pertenecen al equipo del supervisor, y ambos aceptaron
- **Postcondición:** Cambio aprobado o rechazado a nivel supervisor

---

### UC-SUP-09 — Ver solicitudes pendientes del equipo

- **Actor:** Supervisor
- **Postcondición:** Lista de permisos y cambios de turno pendientes de su equipo

---

### UC-SUP-10 — Crear excepción individual justificada

- **Actor:** Supervisor
- **Precondición:** El empleado pertenece a su equipo
- **Postcondición:** Excepción registrada directamente sin flujo de solicitud

---

## 5. Casos de Uso — Coordinador

> El Coordinador incluye todos los casos del Supervisor más los siguientes.

### UC-COOR-01 — Ver supervisores a cargo

- **Actor:** Coordinador
- **Postcondición:** Lista de supervisores bajo su coordinación visualizada

---

### UC-COOR-02 — Ver operadores indirectos

- **Actor:** Coordinador
- **Postcondición:** Lista de todos los operadores bajo su área (a través de sus supervisores)

---

### UC-COOR-03 — Asignar horario a un equipo completo

- **Actor:** Coordinador
- **Precondición:** El equipo existe y tiene al menos un empleado activo
- **Postcondición:** Horario asignado a todo el equipo, historial previo conservado

**Flujo principal:**

1. El coordinador accede a "Gestión de Horarios"
2. Selecciona el equipo objetivo
3. Elige el horario base de la lista de `schedules`
4. Define fecha de inicio (y fecha de fin opcional)
5. El sistema cierra el `team_schedule` anterior (`end_date = hoy - 1`)
6. Crea nuevo registro en `team_schedules`
7. Genera `audit_log` de la operación

**Flujo alternativo:**
El equipo ya tiene un horario activo → el sistema lo informa y solicita confirmación de reemplazo.

---

### UC-COOR-04 — Ajustar horario individual de un empleado

- **Actor:** Coordinador
- **Precondición:** El empleado pertenece al equipo del coordinador
- **Postcondición:** Horario individual creado con `is_custom=true`, sobreescribe asignación de equipo

**Flujo principal:**

1. El coordinador busca el empleado
2. Accede a "Horario Individual"
3. El sistema muestra el horario vigente (de equipo o individual)
4. El coordinador selecciona o define el nuevo horario con fechas
5. El sistema crea registro en `employee_schedules` con `is_custom=true`

---

### UC-COOR-05 — Aprobar cambio de turno (nivel 2)

- **Actor:** Coordinador
- **Precondición:** Cambio aprobado por Supervisor, pendiente de aprobación de Coordinador
- **Postcondición:** Cambio aprobado o rechazado a nivel coordinador

---

### UC-COOR-06 — Aprobar permisos de supervisores

- **Actor:** Coordinador
- **Postcondición:** Permiso del supervisor aprobado o rechazado

---

### UC-COOR-07 — Crear excepciones por equipo

- **Actor:** Coordinador
- **Postcondición:** Excepción registrada masivamente para todos los empleados del equipo

---

### UC-COOR-08 — Ver reportes de cumplimiento del equipo

- **Actor:** Coordinador
- **Postcondición:** Reporte de asistencia y cumplimiento de horarios visualizado

---

## 6. Casos de Uso — Jefe

> El Jefe incluye todos los casos del Coordinador más los siguientes.

### UC-JEF-01 — Ver estructura jerárquica completa

- **Actor:** Jefe
- **Postcondición:** Árbol organizacional completo de su unidad visualizado

---

### UC-JEF-02 — Aprobar vacaciones largas

- **Actor:** Jefe
- **Precondición:** Solicitud de excepción de tipo vacaciones con duración > umbral configurado
- **Postcondición:** Vacaciones aprobadas o rechazadas con justificación

---

### UC-JEF-03 — Aprobar incapacidades prolongadas

- **Actor:** Jefe
- **Postcondición:** Incapacidad registrada y aprobada a nivel jefatura

---

### UC-JEF-04 — Aprobar permisos de coordinadores

- **Actor:** Jefe
- **Postcondición:** Permiso del coordinador aprobado o rechazado

---

### UC-JEF-05 — Ver reportes consolidados

- **Actor:** Jefe
- **Postcondición:** Reportes de toda su unidad generados y visualizados

---

### UC-JEF-06 — Autorizar excepciones especiales

- **Actor:** Jefe
- **Postcondición:** Excepción fuera del flujo estándar aprobada con justificación registrada

---

## 7. Casos de Uso — Analista Workforce (WFM)

> Rol transversal, no jerárquico. Acceso funcional amplio al sistema.

### UC-WFM-01 — Crear horario base del sistema

- **Actor:** Analista Workforce
- **Precondición:** El analista tiene permiso `schedules.create`
- **Postcondición:** Nuevo horario disponible para asignación a equipos o individuos

**Flujo principal:**

1. El analista accede a "Catálogo de Horarios"
2. Crea nuevo horario: nombre, hora inicio, hora fin, minutos de descanso
3. El sistema calcula `total_minutes` automáticamente
4. El analista guarda el horario
5. El sistema valida que `start_time < end_time` y que `total_minutes > 0`

**Flujo alternativo:**
Horario con nombre duplicado → error de validación.

---

### UC-WFM-02 — Editar horario base

- **Actor:** Analista Workforce
- **Precondición:** Horario existe y no tiene asignaciones activas (o se confirma el cambio)
- **Postcondición:** Horario actualizado, cambio auditado

---

### UC-WFM-03 — Definir tolerancias y configuraciones

- **Actor:** Analista Workforce
- **Postcondición:** Parámetros del sistema actualizados (umbrales de aprobación, etc.)

---

### UC-WFM-04 — Asignar horarios masivos

- **Actor:** Analista Workforce
- **Postcondición:** Horario asignado a múltiples equipos o empleados en una sola operación

---

### UC-WFM-05 — Crear excepciones masivas

- **Actor:** Analista Workforce
- **Postcondición:** Excepción aplicada a un grupo de empleados o equipo completo

---

### UC-WFM-06 — Forzar aprobación de excepción institucional

- **Actor:** Analista Workforce
- **Precondición:** El analista tiene permiso `exceptions.force_approve`
- **Postcondición:** Excepción aprobada independientemente del flujo jerárquico normal

**Flujo principal:**

1. El analista localiza la excepción pendiente
2. Activa "Aprobación directa" (disponible solo para su rol)
3. Ingresa justificación obligatoria
4. El sistema aprueba la excepción y la marca con `approved_by = analista`
5. Se genera `audit_log` especial con flag `force_approved`

---

### UC-WFM-07 — Ver todos los reportes del sistema

- **Actor:** Analista Workforce
- **Postcondición:** Acceso a todos los reportes disponibles: asistencia, horarios, excepciones

---

### UC-WFM-08 — Exportar información (CSV / Excel)

- **Actor:** Analista Workforce
- **Postcondición:** Archivo exportado con los datos del reporte seleccionado

---

## 8. Casos de Uso — Director

> El Director incluye todos los casos del Jefe más los siguientes.

### UC-DIR-01 — Ver toda la operación

- **Actor:** Director
- **Postcondición:** Vista global de todos los equipos, asistencias y horarios activos

---

### UC-DIR-02 — Ver indicadores globales

- **Actor:** Director
- **Postcondición:** Dashboard con KPIs de asistencia, ausentismo y cobertura de horarios

---

### UC-DIR-03 — Aprobar permisos de jefes

- **Actor:** Director
- **Postcondición:** Permiso del jefe aprobado o rechazado con registro completo

---

### UC-DIR-04 — Autorizar excepciones institucionales

- **Actor:** Director
- **Postcondición:** Excepción de alto impacto autorizada con justificación institucional

---

## 9. Casos de Uso — Administrador del Sistema

### UC-ADM-01 — Crear usuario

### UC-ADM-02 — Editar usuario

### UC-ADM-03 — Activar / desactivar usuario

### UC-ADM-04 — Asignar rol de sistema

### UC-ADM-05 — Gestionar permisos

### UC-ADM-06 — Gestionar catálogos (cargos, equipos, estados)

### UC-ADM-07 — Importar empleados desde archivo CSV

- **Actor:** Administrador del Sistema
- **Precondición:** El administrador tiene permiso `employees.import`
- **Postcondición:** Empleados importados, errores reportados en log de importación

**Flujo principal:**

1. El administrador descarga la plantilla CSV del sistema
2. Completa los datos de empleados según la estructura definida
3. Sube el archivo al sistema
4. El sistema valida cada fila: `employee_code` único, `position` y `team` existentes
5. Importa las filas válidas como registros en `employees`
6. Genera reporte de filas con error y motivo específico

**Flujo alternativo:**
Archivo con formato incorrecto → rechazo total con mensaje explicativo. Filas con errores → se omiten y se reportan sin detener la importación.

---

### UC-ADM-08 — Reprocesar información

### UC-ADM-09 — Auditar acciones del sistema

---

## 10. Casos de Uso Intrínsecos del Sistema

> Estos casos son ejecutados **automáticamente** por el sistema. Son críticos para la integridad y no deben omitirse en la implementación.

| ID        | Descripción                                                    | Implementación en Laravel                                                              |
| --------- | -------------------------------------------------------------- | -------------------------------------------------------------------------------------- |
| UC-INT-01 | Validar jerarquía antes de aprobar solicitudes                 | `Policy` + `Service` con verificación de `hierarchy_level`                             |
| UC-INT-02 | Validar solapamiento de horarios antes de guardar              | `Rule` con scope de fechas en `ScheduleService`                                        |
| UC-INT-03 | Validar conflictos entre excepciones existentes                | Consulta en `ExceptionService` antes de insert                                         |
| UC-INT-04 | Bloquear acciones fuera de jerarquía organizacional            | `Policy::before()` + `HierarchyService::isDescendantOf()` usando `employees.parent_id` |
| UC-INT-05 | Registrar auditoría de cambios críticos                        | `Observer` o Trait `Auditable` en modelos críticos                                     |
| UC-INT-06 | Notificar cambios relevantes a los actores                     | Laravel `Notifications` (mail / database)                                              |
| UC-INT-07 | Calcular horario efectivo diario (prioridad)                   | `ScheduleResolverService` con lógica de precedencia                                    |
| UC-INT-08 | Resolver prioridad de reglas (excepción > individual > equipo) | `ScheduleResolverService::resolve(employee, date)`                                     |
| UC-INT-09 | Prevenir eliminación de registros históricos                   | `SoftDeletes` + `Policy::delete()` siempre retorna false                               |
| UC-INT-10 | Manejar usuarios sin empleado asociado (roles de sistema)      | Middleware que verifica `employee_id nullable`                                         |
