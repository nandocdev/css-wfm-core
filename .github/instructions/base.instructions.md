---
description: Este archivo describe las pautas de codificación, arquitectura y mejores prácticas para el proyecto Contact Center. Estas instrucciones deben seguirse rigurosamente para mantener la calidad, seguridad y mantenibilidad del código.
applyTo: **/*.php, **/*.blade.php
---

# Coding Guidelines

## 1. Arquitectura de Monolito Modular

El sistema se divide en dominios autónomos. No se permite el acoplamiento directo entre módulos.

### 1.1 Estructura de Módulos

- **Ubicación:** Todo el código de negocio debe vivir en `app/Modules/{NombreModulo}`.
- **Estructura Interna:** Cada módulo debe seguir este esquema:
    - `Actions/`: Lógica de negocio (Clases de una sola responsabilidad).
    - `Models/`: Modelos Eloquent con tipado estricto.
    - `DTOs/`: Objetos de transferencia de datos inmutables.
    - `Events/`: Eventos del dominio del módulo.
    - `Listeners/`: Manejadores de eventos del módulo.
    - `Policies/`: Políticas de autorización del módulo.
    - `Observers/`: Observadores de modelos para auditoría automática.
    - `Http/Controllers/`: Controladores delgados (delegar a Actions).
    - `Http/Requests/`: Validación y sanitización de entrada.
    - `Providers/ModuleServiceProvider.php`: Registro de rutas, vistas, traducciones, eventos y observadores.
    - `Routes/web.php`: Rutas del módulo.
    - `Resources/views/`: Vistas Blade del módulo.

### 1.2 Contratos entre Módulos

- Cada módulo DEBE exponer sus capacidades mediante interfaces en `app/Contracts/`.
- Los módulos consumidores SOLO pueden depender de contratos, nunca de implementaciones concretas.
- Las implementaciones de contratos se registran en el `ModuleServiceProvider` mediante `$this->app->bind()`.
- Prohibido importar clases de otros módulos que no sean contratos o DTOs compartidos.

### 1.3 Comunicación entre Módulos

- **Síncrona:** A través de contratos (interfaces) inyectados por DI.
- **Asíncrona:** A través de eventos del sistema.
- **Datos Compartidos:** Solo mediante DTOs inmutables declarados en `app/DTOs/Shared/`.
- Nunca acceder directamente a modelos o actions de otros módulos.

### 1.4 Eventos y Listeners

- Eventos DEBEN ser clases inmutables (`final readonly class`).
- Eventos DEBEN ubicarse en `app/Modules/{NombreModulo}/Events/`.
- Listeners DEBEN registrarse en el `EventServiceProvider` del módulo.
- Usa `ShouldQueue` para operaciones no críticas (notificaciones, emails, logs externos).
- Eventos que modifican datos externos deben implementar el trait `ShouldDispatchAfterCommit`.

## 2. Estándares de PHP 8.4+ y Laravel 12

### 2.1 Tipado y Declaraciones

- **Tipado Estricto:** Siempre incluir `declare(strict_types=1);` al inicio de cada archivo PHP.
- **Clases Inmutables:** Usa `final readonly class` para Actions, DTOs y Events.
- **Sintaxis Moderna:** Usa Property Hooks, Constructor Promotion y Enums para estados/tipos.
- **Null Safety:** Evita valores null; usa tipos Union (`string|int`) o valores por defecto cuando sea apropiado.

### 2.2 Inyección de Dependencias

- Todas las dependencias DEBEN inyectarse en constructores o métodos.
- Prohibido usar `new` para instanciar Actions, Services o cualquier clase con lógica de negocio.
- Prohibido usar fachadas estáticas excepto para helpers globales de Laravel (`Route`, `Cache`, `Log`).
- Usa `app()` o `resolve()` solo en Service Providers, nunca en lógica de negocio.

## 3. Patrones de Código

### 3.1 Thin Controllers

- El controlador SOLO captura la entrada, delega al Action y devuelve la respuesta.
- Máximo 10 líneas por método de controlador (excluyendo llaves).
- Prohibido lógica de negocio, consultas Eloquent o llamadas a APIs externas en controladores.
- Toda autorización debe hacerse mediante el método `authorize()` o middleware.

### 3.2 Fat Actions

- La lógica de negocio, validaciones de negocio, auditoría y persistencia ocurren en Actions.
- Cada Action debe tener un único método público `execute()` o `handle()`.
- Los Actions son `final readonly class` con dependencias inyectadas en el constructor.
- Toda operación de escritura DEBE estar envuelta en una transacción de base de datos.

### 3.3 Data Transfer Objects (DTOs)

- Los DTOs son `final readonly class` con propiedades públicas tipadas.
- Deben incluir un método estático `fromRequest()` para construcción desde Form Requests.
- Deben incluir un método `toArray()` para conversión a arrays asociativos.
- Los DTOs compartidos entre módulos se ubican en `app/DTOs/Shared/`.
- Los DTOs específicos de módulo se ubican en `app/Modules/{NombreModulo}/DTOs/`.

### 3.4 Form Requests

- Toda entrada HTTP DEBE validarse mediante Form Request classes.
- Los Form Requests deben incluir sanitización de datos en el método `prepareForValidation()`.
- Para inputs de texto libre, aplicar `strip_tags()` o `htmlspecialchars()` según contexto.
- Los mensajes de error deben estar en español y ser claros para usuarios finales.

### 3.5 Modelos Eloquent

- Todos los modelos DEBEN tener `protected $fillable` o `protected $guarded` explícitamente.
- Usa `protected $casts` para tipar atributos (especialmente fechas, booleans, arrays, enums).
- Implementa `SoftDeletes` en modelos críticos donde se requiera auditoría histórica.
- Prohibido lógica de negocio compleja en modelos; usa Actions o Services.
- Los scopes deben ser simples consultas reutilizables, no lógica de negocio.

## 4. Manejo de Transacciones y Persistencia

### 4.1 Transacciones de Base de Datos

- Toda operación que modifique múltiples modelos DEBE envolverse en `DB::transaction()`.
- La auditoría y logs internos deben estar dentro de la misma transacción.
- Eventos que disparan operaciones externas (emails, APIs) deben usar `ShouldDispatchAfterCommit`.
- Nunca capturar excepciones de transacciones sin re-lanzarlas.
- Si una transacción falla, el sistema debe revertir automáticamente todos los cambios.

### 4.2 Auditoría Automática

- Todos los modelos críticos DEBEN tener un Observer registrado para auditoría.
- Los Observers deben capturar: `created`, `updated`, `deleted`, `restored`.
- Cada registro de auditoría debe incluir: tipo de modelo, ID, acción, usuario, IP, cambios realizados.
- Los Observers se registran en el `ModuleServiceProvider` mediante `Model::observe()`.
- La tabla de auditoría debe implementar particionamiento por fecha para escalabilidad.

## 5. Seguridad

### 5.1 Protección CSRF

- NUNCA deshabilitar el middleware `VerifyCsrfToken`.
- Todos los formularios Blade deben incluir la directiva `@csrf`.
- En peticiones AJAX, incluir el token CSRF desde el meta tag en headers.
- Las rutas API bajo `api.php` están exentas de CSRF pero requieren autenticación por token.

### 5.2 Autorización

- Usa Laravel Policies ubicadas en `app/Modules/{NombreModulo}/Policies/`.
- Toda acción sensible (crear, editar, eliminar) debe verificar autorización con `$this->authorize()`.
- Registra las Policies en el `AuthServiceProvider` o en el `ModuleServiceProvider`.
- Los Gates son solo para lógica de autorización que no depende de un modelo específico.

### 5.3 Sanitización de Datos

- Los Form Requests deben validar Y limpiar datos antes de pasarlos a Actions.
- Para inputs que permiten HTML, usa listas blancas explícitas de tags permitidos.
- Los componentes AdminLTE escapan automáticamente salida con sintaxis Blade `{{ }}`.
- Para salida sin escapar, usa `{!! !!}` solo cuando sea absolutamente necesario y el contenido esté sanitizado.

### 5.4 Gestión de Sesiones

- Tras un login exitoso, SIEMPRE ejecutar `$request->session()->regenerate()`.
- Tras un logout, ejecutar `$request->session()->invalidate()` y `$request->session()->regenerateToken()`.
- Las sesiones deben tener tiempo de expiración configurado en `session.lifetime`.
- Para acciones críticas (cambio de contraseña, eliminaciones), re-validar credenciales.

### 5.5 Roles y Permisos

- El sistema DEBE tener al menos dos roles: Administrador y Usuario.
- Los roles y permisos deben almacenarse en base de datos, no hardcodeados.
- Usa un paquete de roles/permisos (Spatie Permission) o implementación propia en `app/Modules/Security`.
- Los permisos deben verificarse tanto en backend (Policies) como en frontend (directivas Blade).

## 6. Desarrollo de UI con TailAdmin Laravel

### 6.1 Layout y Estructura

- Todas las vistas de aplicación DEBEN extender `@extends('layouts.app')`.
- Usa `@section('content')` para el contenido principal; el shell (sidebar/header) vive en `resources/views/layouts/app.blade.php`.
- No duplicar estructura global (sidebar, header, backdrop) dentro de vistas de página.
- Los ajustes de tema deben respetar el sistema existente (Tailwind + clases `dark` + stores de Alpine), sin hardcodear estilos fuera del diseño base.

### 6.2 Componentes Blade

- Prohibido escribir markup repetitivo si existe un componente Blade del proyecto equivalente.
- **UI base:** Usa componentes en `resources/views/components/ui/` (`x-ui.alert`, `x-ui.badge`, `x-ui.button`, `x-ui.modal`, etc.).
- **Formularios:** Prioriza componentes en `resources/views/components/form/` para inputs, selects, radios y estados de formulario.
- **Layouts parciales:** Reutiliza includes existentes de `resources/views/layouts/` para header/sidebar/backdrop.
- Mantén consistencia con variantes y props de cada componente; evita crear APIs de props ad hoc para casos similares.

### 6.3 Manejo de Errores de Validación

- Toda validación debe venir de Form Requests y mostrarse de forma consistente en componentes de formulario.
- Cuando el componente no renderice errores automáticamente, usa bloques `@error('campo')` de manera explícita y uniforme.
- Para mensajes flash, usa componentes UI del proyecto (por ejemplo `x-ui.alert`) con variantes semánticas (`success`, `error`, `warning`, `info`).
- Mantén textos de validación en español, claros y orientados a usuario final.

### 6.4 Rutas y Middleware

- Todas las rutas web DEBEN estar agrupadas bajo el middleware `web`.
- Las rutas protegidas DEBEN incluir el middleware `auth`.
- Agrupa rutas por prefijo y namespace en `Routes/web.php` de cada módulo.
- Los nombres de rutas deben seguir el patrón: `{modulo}.{recurso}.{accion}`.

### 6.5 Estilos y Frontend

- Usa únicamente utilidades Tailwind y tokens del template; evita CSS inline y estilos hardcodeados por vista.
- Los assets frontend deben cargarse vía Vite (`@vite(['resources/css/app.css', 'resources/js/app.js'])`).
- Para interactividad ligera en vistas, usa Alpine.js siguiendo el patrón de stores existente (`theme`, `sidebar`).
- No introducir librerías frontend adicionales para resolver casos que ya cubren Tailwind + Alpine + componentes Blade.

### 6.6 Ejemplos disponibles

- Para formularios: `resources/views/modules/organization/directorates/create.blade.php`.
- Para tablas: `resources/views/modules/organization/directorates/index.blade.php`.
- Para modales: `resources/views/components/ui/modal.blade.php`.
- Para alertas: `resources/views/components/ui/alert.blade.php`.
- Para layout general: `resources/views/layouts/app.blade.php`.

## 7. Testing

### 7.1 Estructura de Tests

- **Tests Unitarios:** `tests/Unit/Modules/{NombreModulo}/Actions/`.
- **Tests de Integración:** `tests/Feature/Modules/{NombreModulo}/`.
- **Tests de API:** `tests/Feature/Api/{NombreModulo}/`.

### 7.2 Cobertura Mínima Requerida

- Actions de escritura (crear, actualizar, eliminar): 100%.
- Controllers (tests de integración): 80%.
- Policies y validaciones: 90%.
- DTOs, Enums y Value Objects: 60%.

### 7.3 Principios de Testing

- Cada test debe ser independiente y no depender del orden de ejecución.
- Usa base de datos de testing (SQLite in-memory o RefreshDatabase trait).
- Los tests deben seguir el patrón AAA: Arrange, Act, Assert.
- Usa factories para generación de datos de prueba, no datos hardcodeados.
- Los tests de integración deben verificar autenticación, autorización y validación.

### 7.4 Nomenclatura de Tests

- Tests unitarios: `test_{accion}_{escenario_esperado}`.
- Tests de feature: `test_{usuario}_{puede_realizar_accion}`.
- Los nombres deben ser descriptivos en español o inglés, consistentemente.

## 8. Gestión de Git (Conventional Commits)

### 8.1 Formato de Commits

- **Estructura:** `tipo(módulo): descripción corta en infinitivo`.
- **Tipos permitidos:**
    - `feat`: Nueva funcionalidad.
    - `fix`: Corrección de error.
    - `refactor`: Mejora de código sin cambiar funcionalidad.
    - `docs`: Cambios en documentación.
    - `test`: Añadir o modificar tests.
    - `chore`: Tareas de mantenimiento (dependencias, configuración).
    - `perf`: Mejoras de rendimiento.
    - `style`: Cambios de formato (no afectan lógica).

### 8.2 Mensajes de Commit

- El mensaje debe estar en español y usar infinitivo.
- Máximo 72 caracteres en la línea de descripción.
- Para cambios complejos, agregar cuerpo del mensaje con detalles adicionales.
- Referencias a issues: `refs #123` o `closes #123` al final del mensaje.

### 8.3 Ramas

- `main`: Código en producción, protegida.
- `develop`: Integración de features, protegida.
- `feature/{modulo}-{descripcion}`: Desarrollo de nuevas funcionalidades.
- `fix/{modulo}-{descripcion}`: Corrección de bugs.
- `hotfix/{descripcion}`: Correcciones urgentes en producción.

### 8.4 Pull Requests

- Todo código debe pasar por Pull Request antes de merge a `develop` o `main`.
- Requiere al menos una aprobación de otro desarrollador.
- Debe pasar todos los tests automatizados (CI/CD).
- Debe cumplir con los estándares de código (PHPStan, Laravel Pint).

## 9. Calidad de Código

### 9.1 Análisis Estático

- Ejecutar PHPStan/Larastan en nivel 8 antes de cada commit.
- Prohibido ignorar errores de PHPStan con anotaciones sin justificación documentada.
- Configurar pre-commit hooks para validación automática.

### 9.2 Estilo de Código

- Usar Laravel Pint para formateo automático según PSR-12.
- Ejecutar `./vendor/bin/pint` antes de cada commit.
- Configuración personalizada en `pint.json` si es necesario.

### 9.3 Documentación de Código

- Todos los métodos públicos de Actions y Services deben tener DocBlocks.
- Los DocBlocks deben incluir: descripción, `@param`, `@return`, `@throws`.
- Evita comentarios obvios; el código debe ser auto-explicativo.
- Documenta decisiones arquitectónicas complejas con comentarios extensos.
- Los archivos Markdowns generados deben ser almacenados en `docs/changelogs` con prefijo de fecha y módulo.

## 10. Deployment y Configuración

### 10.1 Variables de Entorno

- Nunca hardcodear credenciales, URLs o configuraciones sensibles.
- Usar `config()` para acceder a configuraciones, nunca `env()` fuera de archivos de configuración.
- Validar que todas las variables requeridas estén en `.env.example`.

### 10.2 Optimización Pre-Despliegue

- Ejecutar `php artisan config:cache`.
- Ejecutar `php artisan route:cache`.
- Ejecutar `php artisan view:cache`.
- Ejecutar `php artisan event:cache`.
- Verificar permisos de directorios `storage/` y `bootstrap/cache/`.

### 10.3 Migraciones y Seeders

- Las migraciones NUNCA deben eliminarse una vez aplicadas en producción.
- Crear nuevas migraciones para modificar estructuras existentes.
- Los seeders solo deben ejecutarse en ambientes de desarrollo y testing.
- Datos iniciales críticos (roles, permisos) deben tener seeders dedicados y versionados.

## 11. Monitoreo y Logs

### 11.1 Logging

- Usar los niveles de log apropiados: `debug`, `info`, `notice`, `warning`, `error`, `critical`.
- Logs de auditoría deben ir a un canal dedicado configurado en `config/logging.php`.
- Nunca loggear información sensible (contraseñas, tokens, datos personales completos).
- Incluir contexto relevante en logs (user_id, IP, acción realizada).

### 11.2 Manejo de Excepciones

- Todas las excepciones deben capturarse y loggearse adecuadamente.
- Excepciones de negocio deben extender clases personalizadas en Provide project context and coding guidelines that AI should follow when generating code, answering questions, or reviewing changes.
  `app/Exceptions/`.
- Nunca mostrar stack traces completos a usuarios finales.
- Usar `report()` para loggear sin interrumpir ejecución cuando sea apropiado.

## 12. Instrucciones para Generación de Código

### 12.1 Al generar un Módulo Completo

- Incluir `ModuleServiceProvider` con registro de rutas, vistas, traducciones, eventos y observers.
- Crear estructura completa de carpetas según sección 1.1.
- Registrar el ServiceProvider en `config/app.php` o usar auto-discovery.
- Crear al menos un contrato base en `app/Contracts/` si el módulo expone servicios.

### 12.2 Al generar Vistas

- Usar exclusivamente componentes AdminLTE según sección 6.2.
- Guardar en `resources/views/modules/{modulo}/` con snake_case.
- Incluir CSRF token en formularios.
- Verificar que las rutas referenciadas existan y estén nombradas correctamente.

### 12.3 Al generar Rutas

- Agrupar bajo middleware `web` y `auth` según corresponda.
- Usar nombres de ruta consistentes: `{modulo}.{recurso}.{accion}`.
- Aplicar resource controllers donde sea apropiado.
- Documentar rutas API con prefijos y versionado (`/api/v1/`).

### 12.4 Al generar Actions

- Crear clase `final readonly class` con dependencias inyectadas.
- Implementar único método público `execute()` o `handle()`.
- Envolver operaciones de escritura en transacciones.
- Retornar DTOs o modelos, nunca arrays asociativos.

### 12.5 Al generar Tests

- Crear test unitario y de integración para cada Action.
- Usar factories para datos de prueba.
- Verificar casos exitosos y casos de error.
- Incluir tests de autorización cuando aplique.

## 13. Flujo de Trabajo Recomendado

1. Crear una rama feature/fix desde develop.
2. Implementar cambios siguiendo estas instrucciones.
3. Ejecutar tests basicos.
4. Hacer commits atómicos con mensajes claros.
5. Abrir Pull Request hacia develop.
