# Sistema WFM Call Center CSS

Repositorio del **Sistema de Gestión de Horarios (WFM)** para Call Center, construido con **Laravel 12** y orientado a control operativo de horarios, asistencia, permisos, excepciones y jerarquía organizacional.

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791)
![Estado](https://img.shields.io/badge/Estado-En%20elaboraci%C3%B3n-yellow)

## Tabla de contenido

- [Visión general](#visión-general)
- [Stack tecnológico](#stack-tecnológico)
- [Módulos funcionales](#módulos-funcionales)
- [Documentación](#documentación)
- [Puesta en marcha local](#puesta-en-marcha-local)
- [Comandos útiles](#comandos-útiles)
- [Estructura del repositorio](#estructura-del-repositorio)
- [Convenciones del proyecto](#convenciones-del-proyecto)
- [Licencia](#licencia)

## Visión general

Este proyecto centraliza:

- Gestión de empleados, roles y permisos
- Jerarquía organizacional y alcance por visibilidad
- Asignación de horarios (individual y por equipo)
- Registro de asistencia e incidencias
- Solicitudes de permisos y cambios de turno
- Auditoría de acciones críticas

El enfoque funcional y de diseño está documentado bajo el ciclo RUP (fase de elaboración) en la carpeta `docs/`.

## Stack tecnológico

- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Blade, Tailwind CSS v4, Alpine.js, Vite
- **Base de datos:** PostgreSQL 16 (compatible con MySQL/SQLite para desarrollo)
- **Testing:** Pest + PHPUnit

## Módulos funcionales

- Seguridad y acceso (usuarios, roles, permisos)
- Organización (empleados, equipos, cargos, estados laborales)
- Horarios (plantillas, asignaciones, resolución de horario efectivo)
- Excepciones y permisos (flujos de aprobación)
- Asistencia (registro y trazabilidad)
- Auditoría (eventos críticos del sistema)

## Documentación

Punto de entrada de documentación:

- [docs/README.md](docs/README.md)

Documentos clave del dominio:

- [Visión y casos de uso](docs/00-overview/01_vision.md)
- [Requisitos del sistema (SRS)](docs/00-overview/02_requisitos.md)
- [Casos de uso](docs/00-overview/03_casos_uso.md)
- [Modelo de datos](docs/00-overview/04_model.md)
- [Esquema SQL de arquitectura](docs/01-architecture/database.sql)

## Puesta en marcha local

### 1) Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- npm
- PostgreSQL (recomendado) o SQLite/MySQL

### 2) Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 3) Configurar base de datos

Edita `.env` y luego ejecuta:

```bash
php artisan migrate
```

Opcional:

```bash
php artisan db:seed
php artisan storage:link
```

### 4) Levantar entorno de desarrollo

```bash
composer run dev
```

Esto levanta servidor Laravel, cola, logs y Vite en paralelo.

## Comandos útiles

```bash
# Desarrollo frontend
npm run dev

# Build de assets
npm run build

# Ejecutar pruebas
composer run test
# o
php artisan test

# Limpiar/optimizar (según entorno)
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Estructura del repositorio

```text
app/                # Código de aplicación (dominio, controladores, modelos, etc.)
config/             # Configuración de Laravel
database/           # Migraciones, seeders, factories
docs/               # Documentación funcional y técnica del proyecto
public/             # Punto de entrada web y assets compilados
resources/          # Vistas Blade, CSS y JS fuente
routes/             # Definición de rutas web/console
tests/              # Pruebas unitarias y feature
```

## Convenciones del proyecto

- Commits con convención tipo Conventional Commits en español (`feat`, `fix`, `docs`, `refactor`, etc.)
- Arquitectura orientada a módulos de dominio
- Policies para autorización + validación de jerarquía organizacional
- Cambios funcionales deben reflejarse en `docs/`

## Licencia

Este proyecto se distribuye bajo licencia [MIT](LICENSE).
