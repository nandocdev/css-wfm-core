CAJA DE SEGURO SOCIAL --- CALL CENTER

Sistema de Gestión de Horarios (WFM)

**Modelo de Datos**

_RUP --- Diagrama de Entidad-Relación (ERD)_
Versión: v1.0
Fecha: Febrero 2026
Stack: PHP 8.3 · Laravel 12 · PostgreSQL 16

> **1. Introducción y Principios de Diseño**

Este documento define el modelo de datos completo del sistema WFM. Está diseñado para un monolito Laravel con persistencia en PostgreSQL, con énfasis en historial, auditoría e integridad referencial.

---

**Principio** **Implementación**

---

Separación Usuario / Empleado users y employees son entidades distintas, relacionadas 1:1 opcional

Jerarquía organizacional FK self-referential: parent_id en employees (adjacency list)

Historial antes que sobrescritura Columnas start_date / end_date en schedule assignments

Estados normalizados Enums PHP + columnas string; nunca valores hardcodeados en código

Soft deletes deleted_at en entidades críticas: employees, schedules, users

Auditoría como primer nivel Tabla audit_logs transversal, inmutable

Zona horaria Timestamps en UTC, presentación en America/Panama (UTC-5)

---

> **2. Módulo: Seguridad y Acceso**

**2.1 Tabla: users**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK Identificador autoincremental

username VARCHAR(50) UNIQUE, NOT NULL Nombre de usuario único en el sistema

email VARCHAR(150) UNIQUE, NOT NULL Correo electrónico para autenticación

password VARCHAR(255) NOT NULL Hash bcrypt con costo mínimo 12

is_active BOOLEAN DEFAULT true Permite desactivar sin eliminar

last_login_at TIMESTAMPTZ NULLABLE Fecha y hora del último acceso

deleted_at TIMESTAMPTZ NULLABLE Soft delete --- null = activo

created_at TIMESTAMPTZ DEFAULT now() Timestamp de creación

updated_at TIMESTAMPTZ DEFAULT now() Timestamp de última actualización

---

**2.2 Tabla: roles**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

code VARCHAR(30) UNIQUE, NOT NULL operador \| supervisor \| coordinador \| jefe \| analista \| director \| admin

name VARCHAR(100) NOT NULL Nombre legible del rol

hierarchy_level SMALLINT NOT NULL 1=Operador \... 6=Director. Usado para validar jerarquía

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

**2.3 Tabla: permissions**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

code VARCHAR(80) UNIQUE, NOT NULL Ej: employees.create, schedules.bulk_assign

description VARCHAR(255) NOT NULL Descripción legible de la acción

created_at TIMESTAMPTZ DEFAULT now()

---

**2.4 Tabla pivote: role_permission**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

role_id BIGINT FK → roles.id, NOT NULL

permission_id BIGINT FK → permissions.id, NOT NULL

\[PK compuesta\] role_id + permission_id Índice único compuesto

---

**2.5 Tabla pivote: user_role**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

user_id BIGINT FK → users.id, NOT NULL

role_id BIGINT FK → roles.id, NOT NULL

\[PK compuesta\] user_id + role_id

---

> **3. Módulo: Organización**

**3.1 Tabla: positions (cargos)**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

name VARCHAR(100) NOT NULL Ej: Agente Telefónico, Supervisor de Turno

role_id BIGINT FK → roles.id, NULLABLE Rol por defecto para este cargo

is_active BOOLEAN DEFAULT true

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

**3.2 Tabla: teams (equipos)**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

name VARCHAR(100) NOT NULL Nombre del equipo operativo

is_active BOOLEAN DEFAULT true

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

**3.3 Tabla: employment_statuses**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

code VARCHAR(30) UNIQUE, NOT NULL activo \| suspendido \| retirado \| licencia

description VARCHAR(255) NOT NULL

created_at TIMESTAMPTZ DEFAULT now()

---

**3.4 Tabla: employees (entidad central del sistema)**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

user_id BIGINT FK → users.id, UNIQUE, NULLABLE NULL = empleado sin acceso al sistema

employee_code VARCHAR(20) UNIQUE, NOT NULL Código institucional del empleado

full_name VARCHAR(150) NOT NULL Nombre completo

hire_date DATE NOT NULL Fecha de ingreso a la institución

position_id BIGINT FK → positions.id, NOT NULL Cargo actual

team_id BIGINT FK → teams.id, NOT NULL Equipo operativo asignado

employment_status_id BIGINT FK → employment_statuses.id Estado laboral actual

parent_id BIGINT FK → employees.id, NULLABLE Jefe inmediato en árbol jerárquico

deleted_at TIMESTAMPTZ NULLABLE Soft delete

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

> _La FK self-referential `parent_id` representa la jerarquía organizacional. Es nullable para permitir nodos raíz (ej. dirección/jefatura superior)._
>
> **4. Módulo: Horarios**

**4.1 Tabla: schedules (plantillas base)**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

name VARCHAR(100) NOT NULL Ej: Turno Mañana 7-3, Turno Tarde 3-11

start_time TIME NOT NULL Hora de inicio del turno

end_time TIME NOT NULL Hora de fin del turno

break_minutes SMALLINT DEFAULT 0 Minutos de descanso incluidos

total_minutes SMALLINT NOT NULL Calculado: (end-start) - break_minutes

deleted_at TIMESTAMPTZ NULLABLE Soft delete --- no eliminar plantillas activas

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

**4.2 Tabla: employee_schedules (asignación individual)**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

employee_id BIGINT FK → employees.id, NOT NULL

schedule_id BIGINT FK → schedules.id, NOT NULL

start_date DATE NOT NULL Fecha desde la que aplica este horario

end_date DATE NULLABLE NULL = vigente. Fecha al cerrar el período

is_custom BOOLEAN DEFAULT false true = sobrescribe asignación de equipo

created_by BIGINT FK → users.id Usuario que realizó la asignación

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

**4.3 Tabla: team_schedules (asignación por equipo)**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

team_id BIGINT FK → teams.id, NOT NULL

schedule_id BIGINT FK → schedules.id, NOT NULL

start_date DATE NOT NULL

end_date DATE NULLABLE NULL = vigente

created_by BIGINT FK → users.id

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

> _Lógica de resolución de horario efectivo: ScheduleResolverService::resolve(int \$employeeId, Carbon \$date) aplica en orden: (1) exceptions con affects_schedule=true, (2) employee_schedules con is_custom=true, (3) team_schedules del equipo del empleado._
>
> **5. Módulo: Excepciones y Permisos**

**5.1 Tabla: exception_types**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

code VARCHAR(30) UNIQUE, NOT NULL vacaciones \| incapacidad \| licencia \| feriado

name VARCHAR(100) NOT NULL Nombre legible

is_paid BOOLEAN DEFAULT false Si la excepción es con goce de salario

requires_approval BOOLEAN DEFAULT true Si requiere flujo de aprobación

created_at TIMESTAMPTZ DEFAULT now()

---

**5.2 Tabla: exceptions**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

employee_id BIGINT FK → employees.id, NOT NULL

exception_type_id BIGINT FK → exception_types.id, NOT NULL

start_date DATE NOT NULL Inicio del período de excepción

end_date DATE NOT NULL Fin del período de excepción

affects_schedule BOOLEAN DEFAULT true Si reemplaza el horario del período

status VARCHAR(20) DEFAULT \"pending\" pending \| approved \| rejected

notes TEXT NULLABLE Observaciones o justificación

created_by BIGINT FK → users.id, NOT NULL

approved_by BIGINT FK → users.id, NULLABLE

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

**5.3 Tabla: leave_requests (solicitudes de permiso)**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

employee_id BIGINT FK → employees.id, NOT NULL Empleado solicitante

type VARCHAR(10) NOT NULL total \| parcial

start_datetime TIMESTAMPTZ NOT NULL Inicio del permiso solicitado

end_datetime TIMESTAMPTZ NOT NULL Fin del permiso solicitado

reason TEXT NOT NULL Motivo obligatorio

status VARCHAR(20) DEFAULT \"pending\" pending \| approved \| rejected \| cancelled

reviewed_by BIGINT FK → users.id, NULLABLE Usuario que aprobó/rechazó

review_notes TEXT NULLABLE Motivo de rechazo (obligatorio si rejected)

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

**5.4 Tabla: shift_change_requests (cambios de turno)**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

requester_employee_id BIGINT FK → employees.id, NOT NULL Empleado que solicita el cambio

target_employee_id BIGINT FK → employees.id, NOT NULL Empleado con quien se quiere cambiar

change_date DATE NOT NULL Fecha del cambio propuesto

status VARCHAR(30) DEFAULT \"pending_acceptance\" pending_acceptance \| accepted \| rejected_by_target \| pending_approval \| approved \| rejected

target_response_notes TEXT NULLABLE

approved_by BIGINT FK → users.id, NULLABLE

approval_notes TEXT NULLABLE

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

> **6. Módulo: Asistencia**

**6.1 Tabla: attendances**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

employee_id BIGINT FK → employees.id, NOT NULL

date DATE NOT NULL Fecha del registro de asistencia

status VARCHAR(20) NOT NULL present \| absent \| late \| justified

exception_id BIGINT FK → exceptions.id, NULLABLE Excepción que justifica la ausencia

notes TEXT NULLABLE Observaciones del supervisor

recorded_by BIGINT FK → users.id, NOT NULL Supervisor que registró la asistencia

created_at TIMESTAMPTZ DEFAULT now()

updated_at TIMESTAMPTZ DEFAULT now()

---

> _Restricción: UNIQUE (employee_id, date) --- solo un registro de asistencia por empleado por día. Los updates quedan registrados en audit_logs._
>
> **7. Módulo: Auditoría**

**7.1 Tabla: audit_logs**

---

**Columna** **Tipo PostgreSQL** **Restricciones** **Descripción**

---

id BIGSERIAL PK

user_id BIGINT FK → users.id, NULLABLE NULL si la acción es del sistema

entity_type VARCHAR(80) NOT NULL Ej: App\\Modules\\Employee\\Models\\Employee

entity_id BIGINT NOT NULL ID de la entidad afectada

action VARCHAR(20) NOT NULL created \| updated \| deleted \| restored \| force_approved

before JSONB NULLABLE Estado previo de la entidad (null si created)

after JSONB NULLABLE Estado posterior (null si deleted)

ip_address INET NULLABLE IP del cliente que originó la acción

user_agent VARCHAR(255) NULLABLE

created_at TIMESTAMPTZ DEFAULT now() Inmutable --- sin updated_at

---

> _Esta tabla NO tiene updated_at. Los registros son append-only. No se permite UPDATE ni DELETE. Los índices recomendados: (entity_type, entity_id), (user_id), (created_at)._
>
> **8. Índices y Restricciones Clave**

---

**Tabla** **Índice / Restricción** **Propósito**

---

attendances UNIQUE (employee_id, date) Un registro por empleado/día

employees UNIQUE (employee_code) Código institucional único

employees INDEX (team_id), INDEX (parent_id) Consultas de equipo y jerarquía

employee_schedules INDEX (employee_id, start_date) Consulta de horario vigente

team_schedules INDEX (team_id, start_date) Consulta masiva de horario

exceptions INDEX (employee_id, start_date, end_date) Validación de conflictos

audit_logs INDEX (entity_type, entity_id) Trazabilidad por entidad

audit_logs INDEX (created_at) Consultas por rango de fechas

users UNIQUE (email), UNIQUE (username) Autenticación única

---

> **9. Resumen de Relaciones**

---

**Entidad A** **Relación** **Entidad B** **Notas**

---

users 1 : 0..1 employees Un user puede no tener empleado

users N : M roles Vía tabla user_role

roles N : M permissions Vía tabla role_permission

employees N : 1 positions Cargo actual del empleado

employees N : 1 teams Equipo operativo asignado

employees N : 0..1 employees Jerarquía recursiva por jefe inmediato (parent_id)

employees 1 : N employee_schedules Historial de asignaciones individuales

teams 1 : N team_schedules Historial de asignaciones de equipo

employees 1 : N exceptions Excepciones laborales del empleado

employees 1 : N leave_requests Solicitudes de permiso

employees 1 : N attendances Registros de asistencia

attendances N : 0..1 exceptions Ausencia justificada por excepción

---
