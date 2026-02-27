
---

# 📌 Descripción por Sección

## 00-overview
Documentación ejecutiva y de contexto general:
- Visión del sistema
- Alcance
- Roadmap
- Glosario

👉 Ideal para entender rápidamente el propósito del proyecto.

---

## 01-architecture
Documentación técnica estructural:
- Arquitectura general del sistema
- Decisiones técnicas (ADR)
- Diagramas
- Modelo de datos
- Estrategias de indexación

👉 Aquí se documenta *cómo está construido* el sistema.

---

## 02-backend
Detalles técnicos del backend:
- Módulos del sistema
- Convenciones internas
- Uso de Actions / DTOs / Policies
- Contratos de API

👉 Sirve como referencia para desarrolladores.

---

## 03-frontend
Documentación de interfaz y diseño:
- Sistema de diseño
- Personalización del template
- Componentes reutilizables
- Estructura de layouts

👉 Garantiza coherencia visual y mantenimiento sostenible.

---

## 04-ai
Material generado o asistido por IA:
- Resúmenes técnicos
- Prompts utilizados
- Decisiones apoyadas por IA

👉 Permite trazabilidad y reproducibilidad.

---

## 05-testing
Estrategia y cobertura de pruebas:
- Enfoque de testing
- Casos de prueba clave
- Convenciones

👉 Define qué se prueba y bajo qué criterios.

---

## 06-devops
Infraestructura y despliegue:
- Configuración de entornos
- Estrategia de versionado
- Pipeline CI/CD
- Proceso de deployment

👉 Referencia operativa para producción.

---

## 07-changelog
Historial de cambios del proyecto:
- CHANGELOG principal
- Notas por versión

👉 Debe actualizarse en cada release.

---

## 08-user-guide
Documentación final orientada al usuario:
- Guías de uso
- Flujos funcionales
- Preguntas frecuentes

👉 No debe contener detalles técnicos internos.

---

# 🧭 Reglas de Documentación

1. No mezclar documentación técnica con guía de usuario.
2. Las decisiones arquitectónicas deben registrarse como ADR.
3. Cada carpeta debe mantener coherencia temática.
4. Evitar duplicación de información entre secciones.
5. Mantener los documentos versionados cuando aplique.

---

# 🏷️ Convenciones

- Usar Markdown.
- Nombrar archivos en `kebab-case`.
- Incluir fecha cuando el documento sea contextual.
- Registrar cambios relevantes en el changelog.

---

# 🎯 Principio Rector

La documentación debe ser:

- Clara
- Práctica
- Útil
- Actualizable

Si un documento deja de aportar valor o está desactualizado, debe corregirse o eliminarse.

---

📌 Este README actúa como punto de entrada oficial a la documentación del proyecto.