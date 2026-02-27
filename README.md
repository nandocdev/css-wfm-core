# Sistema WFM Call Center CSS

Plataforma web para planificar, publicar y controlar la operación de horarios de un Contact Center de forma auditable, con jerarquía organizacional y flujos de aprobación por rol.

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.3-blue)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791)
![Arquitectura](https://img.shields.io/badge/Arquitectura-Monolito%20Modular-6f42c1)
![Estado](https://img.shields.io/badge/Estado-En%20desarrollo-yellow)

---

## ¿Qué resuelve este proyecto?

El sistema WFM centraliza en una sola plataforma:

- Programación semanal e intradía de turnos.
- Gestión de permisos, excepciones y cambios de turno.
- Registro y trazabilidad de asistencia e incidencias.
- Gestión de usuarios, roles, permisos y alcance jerárquico.
- Auditoría de acciones críticas para cumplimiento institucional.

Resultado esperado: mayor cobertura operativa, menor conflicto de horarios y decisiones basadas en información confiable por rol (Operador, Coordinador, Jefe, WFM, Director y Administrador).

---

## Capacidades clave

- Seguridad y acceso: login, logout, recuperación/cambio de contraseña, control por roles y políticas.
- Gestión de personas: ficha laboral, equipos, estructura organizacional y catálogos.
- Planificación WFM: turnos base, publicación semanal, actividades intradía y descansos.
- Workflow operativo: permisos totales/parciales y cambios de turno con aprobación.
- Monitoreo y reportes: vistas por jerarquía, exportación y seguimiento histórico.
- Integridad del sistema: reglas intrínsecas, auditoría inmutable y notificaciones.

---

## Arquitectura

El proyecto sigue un enfoque de monolito modular en Laravel:

- Módulos de negocio en `app/Modules/*`.
- Contratos compartidos en `app/Contracts/*`.
- Vistas por módulo en `app/Modules/{Modulo}/Resources/views`.
- Documentación funcional/técnica en [docs/README.md](docs/README.md).

Esta estructura permite escalar por dominio sin perder cohesión del producto.

---

## Stack tecnológico

- Backend: Laravel 12, PHP 8.3, Eloquent ORM.
- Frontend: Blade, Tailwind CSS, Alpine.js, Vite.
- Datos: PostgreSQL 16.
- Pruebas: Pest + PHPUnit.

---

## Inicio rápido

### Requisitos

- PHP 8.3+
- Composer
- Node.js 18+
- npm
- PostgreSQL 16 (recomendado)

### Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Opcional:

```bash
php artisan db:seed
php artisan storage:link
```

### Levantar entorno local

```bash
composer run dev
```

---

## Documentación del producto

### Funcional

- [Visión](docs/00-overview/01_vision.md)
- [Requisitos del sistema (SRS)](docs/00-overview/02_requisitos.md)
- [Casos de uso](docs/00-overview/03_casos_uso.md)
- [Modelo de dominio](docs/00-overview/04_model.md)

### Técnica

- [Guía general de documentación](docs/README.md)
- [Arquitectura de datos SQL](docs/01-architecture/database.sql)
- [Roadmap del proyecto](ROADMAP.md)

---

## Módulos de alto nivel

- Security & Administration
- Organization & Corporate
- Employee & Welfare
- Schedule Engine / Planning
- Attendance & Incidents
- Workflow (Permisos, Excepciones, Cambios de turno)
- Analytics & Monitoring

---

## Comandos útiles

```bash
# pruebas
php artisan test

# frontend
npm run dev
npm run build

# cachés
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Estado actual

- Fase RUP: Elaboración.
- Cobertura de casos de uso trazada en roadmap funcional.
- Implementación incremental por sprints y módulos.

---

## Licencia

Distribuido bajo [MIT](LICENSE).
