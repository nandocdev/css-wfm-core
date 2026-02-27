---
name: roadmap-tarea
description: Este prompt guía la implementación de tareas específicas dentro del roadmap del proyecto, asegurando coherencia arquitectónica, trazabilidad y calidad en cada entrega.
---

Trabajemos en la tarea **{{TASK}}**.

### Instrucciones:

0. **Generar Rama**: Crear una rama específica para esta tarea siguiendo la convención `feature/{{TASK_NAME}}`.

1. **Analizar** los requerimientos funcionales y técnicos asociados a la tarea.
   Referencias clave: `docs/technical/{UsesCase,ModulesCU,Permisos,Role}.md` y `ROADMAP.md`.

2. **Planificar antes de escribir código.** Describir brevemente:
    - Qué se va a implementar.
    - Qué archivos se crearán o modificarán.
    - Qué dependencias existen con otros módulos.

3. **Definir una estrategia de implementación** simple y coherente con la arquitectura modular actual (`app/Modules`). Reutilizar componentes, contratos e interfaces existentes cuando sea posible.

4. **Implementar** las funcionalidades necesarias para cumplir completamente la tarea.

5. **Integrar** con los módulos afectados, respetando la comunicación por eventos y contratos definidos (sin acoplamiento directo entre módulos).

6. **Generar pruebas:**
    - Al menos un **Feature test** por caso de uso implementado.
    - **Unit tests** para lógica de negocio no trivial (validaciones, cálculos, reglas de dominio).

7. **Actualizar el menú** solo si la tarea implica nuevas rutas o vistas accesibles desde la navegación:
   Modificar `app/Helpers/MenuHelper.php` para reflejar los cambios.

8. **Actualizar el roadmap** (`ROADMAP.md`) marcando la tarea como completada y ajustando el progreso de la fase correspondiente.

9. **Documentar** cualquier decisión técnica relevante o cambio de contrato en los archivos de `docs/technical/` afectados.

10. **Crear changelog** claro y descriptivo en `docs/changelog/` que refleje qué se implementó y por qué.

11. **Commits atómicos** usando la convención estándar:
    - `feat:` nueva funcionalidad
    - `fix:` corrección de error
    - `refactor:` mejora sin cambio de comportamiento
    - `test:` pruebas
    - `docs:` documentación

12. **merge** a `develop` con descripción detallada de los cambios, referencias a tareas y cualquier consideración para el equipo de revisión.

---

> **Principio guía:** Evitar sobreingeniería. Priorizar soluciones simples, predecibles y alineadas con los patrones ya establecidos en el proyecto.
