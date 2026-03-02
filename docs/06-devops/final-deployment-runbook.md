# Runbook de despliegue final

Fecha: 2026-03-02
Ámbito: cierre Sprint 5 (Analytics & Monitoring + Calidad y Entrega)

## 1. Prerrequisitos

- PHP >= 8.2
- Composer instalado
- Node.js >= 18
- Base de datos PostgreSQL/MySQL operativa
- Variables de entorno productivas definidas

## 2. Checklist pre-despliegue

- [ ] `php artisan test` en verde.
- [ ] Permisos y roles sincronizados.
- [ ] `APP_ENV=production` y `APP_DEBUG=false`.
- [ ] Backups de base de datos verificados.

## 3. Comandos de despliegue

### Dependencias y build

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### Migraciones

```bash
php artisan migrate --force
```

### Cache de aplicación

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Enlace de storage

```bash
php artisan storage:link
```

## 4. Verificaciones post-despliegue

- [ ] Login y navegación general.
- [ ] Acceso a `Analytics & Monitoring` por rol.
- [ ] Exportación CSV/Excel desde analytics.
- [ ] Flujo de aprobación forzada institucional.
- [ ] Revisión de logs y cola de trabajos.

## 5. Rollback

Si falla una migración o verificación crítica:

1. Hacer rollback de release en infraestructura.
2. Restaurar backup de BD previo.
3. Limpiar cachés (`php artisan optimize:clear`).
4. Reabrir incidente y ejecutar postmortem.

## 6. Operación continua

- Monitorear errores 24h después del release.
- Registrar hallazgos en changelog del sprint.
- Programar ventana de hardening para UI y performance.
