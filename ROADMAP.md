# ROADMAP — Sistema WFM Call Center CSS

## Estados permitidos

- `[ ]` Pendiente
- `[~]` En proceso
- `[x]` Completado

## 🏁 Sprint 0: Infraestructura, Seguridad y Auditoría (Semanas 1-2)

**Objetivo:** Establecer la base técnica, seguridad y el sistema de trazabilidad.

- [~] **Configuración Base y Core**
    - [~] Inicializar Laravel 12 con PHP 8.4 y PostgreSQL 16.
    - [x] Estructura modular `app/Modules/*`.
    - [x] Catálogos base: `provinces`, `districts`, `townships`, `employment_statuses`, `incident_types`.
    - [x] **Auditoría Inmutable:** Modelo `AuditLog` y Observer Global para modelos críticos (UC-INT-05, UC-ADM-09).
    - [x] **Sistema de Notificaciones:** Infraestructura para alertas vía Mail/Database (UC-COM-06, UC-INT-06).

- [~] **Módulo: Security & Administration**
    - [x] Migración de `users` y roles/permisos (Spatie).
    - [~] Flujos de login/logout/password (UC-COM-01 al 05).
    - [~] ABM de usuarios y gestión de permisos (UC-ADM-01 al 05).
    - [ ] **Middleware de Jerarquía:** Validar usuarios sin empleado asociado (UC-INT-10).

---

## 🏢 Sprint 1: Estructura Organizacional y Gestión Humana (Semanas 3-4)

**Objetivo:** Modelar la jerarquía de la CSS y la ficha única del empleado.

- [~] **Módulo: Organization & Corporate**
    - [~] Estructura: `Directorate` -> `Department` -> `Position` (UC-ADM-06).
    - [ ] **Jerarquía Dinámica:** Lógica de recursividad en `Employee` para validación de descendencia (UC-INT-04).
    - [ ] Vistas de estructura jerárquica para roles superiores (UC-JEF-01).

- [~] **Módulo: Employee & Welfare**
    - [~] Ficha del empleado: Datos laborales y vinculación organizacional.
    - [~] Submódulos de Bienestar: Dependientes, discapacidades y enfermedades (UC-OP-13, UC-ADM-10).
    - [ ] **Carga Masiva:** `ImportEmployeesAction` con validaciones de integridad (UC-ADM-07).
    - [ ] Dashboard "Mi Perfil" para Operadores (UC-OP-01).

- [~] **Módulo: Team Management**
    - [~] Gestión de equipos (`teams`) y miembros.
    - [ ] Asignación de Coordinadores a equipos únicos (UC-COOR-01).

---

## ⏰ Sprint 2: Catálogos de Tiempo y Planificación Base (Semanas 5-6)

**Objetivo:** Herramientas para que el Analista WFM defina la oferta de horarios.

- [~] **Módulo: Schedule Engine**
    - [ ] CRUD de Horarios con cálculo automático de jornada (UC-WFM-01, 02).
    - [~] Plantillas de Descansos (`break_templates`) y su asignación (UC-WFM-12).
    - [ ] **Configuraciones WFM:** Definición de tolerancias y umbrales (UC-WFM-03).

- [~] **Módulo: Planning (Fase 1 - Semanal)**
    - [ ] Motores de asignación masiva de turnos base (UC-WFM-04).
    - [ ] Interfaz de grilla para WFM (estado `draft`).
    - [ ] Flujo de publicación de planificación (UC-WFM-09).
    - [ ] **Consumo Operativo:** Vista de "Mi Horario" (UC-OP-02), Historial (UC-OP-03) y Excepciones (UC-OP-04) para Operadores.

---

## ⚡ Sprint 3: Operación en Piso e Intradía (Semanas 7-8)

**Objetivo:** Micro-gestión del tiempo real y control de asistencia.

- [~] **Módulo: Planning (Fase 2 - Intradía)**
    - [ ] Gestión de Actividades Intradía: Capacitaciones, Coaching, Reuniones (UC-WFM-10, 11).
    - [ ] Timeline "Mi Día" para Operadores (UC-OP-12).
    - [ ] **Gestión de Pausas:** Sobrescritura de descansos por el Coordinador (UC-COOR-07).

- [~] **Módulo: Attendance & Incidents**
    - [ ] Registro de incidencias de asistencia por Coordinador (UC-COOR-02).
    - [ ] Consulta de Asistencia e Historial para el Operador (UC-OP-10, 11).
    - [ ] Pantalla de escalación operativa para Supervisores / Op II (UC-SUP-03).

---

## 🔄 Sprint 4: Workflow, Aprobaciones y Motor de Reglas (Semanas 9-10)

**Objetivo:** Resolucion de conflictos y flujos de aprobación multinivel.

- [~] **Módulo: Workflow (Permisos y Excepciones)**
    - [ ] Solicitudes de Permiso (Total/Parcial) con prevención de solapamiento (UC-OP-05, UC-OP-06, UC-OP-07, UC-INT-02).
    - [ ] **Bandeja Administrativa del Equipo:** Vista consolidada para el Coordinador (UC-COOR-05).
    - [ ] Aprobaciones multinivel (Coordinador -> Jefe -> Director) según tipo/duración (UC-COOR-03, UC-JEF-02 al 04, UC-DIR-03).
    - [ ] **Reglas de aprobación:** Validar coordinador directo y flujo de un solo paso (UC-INT-01, UC-INT-11).
    - [ ] Creación de excepciones directas por Coordinador/WFM (UC-COOR-06, UC-WFM-05).
    - [ ] **Motor de conflictos de negocio:** Resolver solapamientos lógicos entre permisos, excepciones e incidencias (UC-INT-03).

- [~] **Módulo: Workflow (Cambios de Turno)**
    - [ ] Flujo completo: Solicitud -> Aceptación -> Aprobación (UC-OP-08, 09, UC-COOR-04).

- [ ] **Módulo: Intelligence & Persistence**
    - [ ] **ScheduleResolverService:** Lógica de prioridad (Excepción > Intradía > Semanal) (UC-INT-07, 08).
    - [ ] **WFM Advanced:** Forzar aprobaciones institucionales y ajustes masivos (UC-WFM-06, UC-DIR-04, UC-JEF-06).
    - [ ] Mantenimiento: Herramientas de reprocesamiento (UC-ADM-08) y prevención de borrado (UC-INT-09).

---

## 📊 Sprint 5: Explotación de Datos y Calidad Final (Semanas 11-12)

**Objetivo:** Dashboards ejecutivos, reportes avanzados y cierre de proyecto.

- [ ] **Módulo: Analytics & Monitoring**
    - [ ] Dashboard de Director/Jefe: KPIs de ausentismo y cobertura global (UC-DIR-01, 02).
    - [ ] Reportería consolidada por Jefatura para toma de decisiones (UC-JEF-05).
    - [ ] Reportes de cumplimiento para Coordinadores (UC-COOR-08).
    - [ ] Vistas de monitoreo "Solo Lectura" para Supervisores/Coordinadores (UC-SUP-01, 02, UC-COOR-09).
    - [ ] Motor de Exportación (CSV/Excel) para WFM (UC-WFM-07, 08).

- [ ] **Calidad y Entrega**
    - [ ] Pulido de UI con AdminLTE y componentes Blade consistentes.
    - [ ] Pruebas unitarias/integración de flujos críticos de aprobación.
    - [ ] Documentación técnica y despliegue final.

---

> [!NOTE]
> **Aseguramiento de Casos de Uso Especiales:**
>
> - El rol **Supervisor (Operador II)** se limita a visualización y escalación (UC-SUP-01 al 03).
> - El **Analista WFM** tiene control total de la planificación pero no de la jerarquía organizacional.
> - Se garantiza la integridad histórica mediante `SoftDeletes` y políticas restrictivas (UC-INT-09).
> - Los casos **UC-SUP-04..UC-SUP-11** se consideran **reasignados/no vigentes** según el catálogo maestro y no generan backlog independiente.
