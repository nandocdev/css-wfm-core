## Instalación de Laravel[]

TailAdmin Laravel está creado con **Laravel** y **Viento de cola CSS**. Sirve como un kit de inicio completo para crear paneles de administración sólidos.

### Requisitos[]

Antes de comenzar, asegúrese de que su entorno cumpla con los siguientes requisitos:

- **PHP:** >= 8.2
- **Compositor**
- **Node.js:** >= 18.x
- **Base de datos:** SQLite, MySQL o PostgreSQL

### Clonación del repositorio []

La forma más sencilla de comenzar es clonando el repositorio directamente desde GitHub.

1.  **Clonar el repositorio:** git clone https://github.com/TailAdmin/tailadmin-laravel.git

2.  **Navegue hasta la carpeta del proyecto:** cd tailadmin-laravel

### Alternativa: Descargar[]

Si prefieres no usar Git, puedes hacerlo [descargar la plantilla del panel] como un archivo ZIP. Después de descargar, extraiga el contenido y navegue hasta la carpeta del proyecto en su terminal.

---

### Pasos de instalación[]

Una vez que esté dentro de la carpeta del proyecto, siga estos pasos para instalar dependencias y configurar el entorno.

#### 1\. Instalar dependencias PHP[]

Ejecute el siguiente comando para instalar los paquetes PHP necesarios: composer install

#### 2\. Instalar dependencias de nodos[]

Elija su administrador de paquetes preferido para instalar las dependencias del frontend:

npmhilopnpmbollo npm install yarn install pnpm install bun install

#### 3\. Configurar entorno[]

Copie el archivo de entorno de ejemplo: cp .env.example .env

Usuarios de Windows copy .env.example .env

Generar la clave de la aplicación: php artisan key:generate

#### 4\. Configurar base de datos[]

Actualiza tu `.env` archivo con las credenciales de su base de datos. Por ejemplo, para MySQL: DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=tailadmin_db DB_USERNAME=your_username DB_PASSWORD=your_password

Crea la base de datos si no existe: # MySQL mysql -u root -p -e "CREATE DATABASE tailadmin_db;" # PostgreSQL createdb tailadmin_db

#### 5\. Ejecute migraciones y sembradoras[]

Configure las tablas de su base de datos e inserte datos de muestra: php artisan migrate:fresh --seed

#### 6\. Enlace de almacenamiento[]

Crea un enlace simbólico para que tu almacenamiento sea accesible al público: php artisan storage:link

---

### Ejecutando la aplicación[]

#### Modo de desarrollo []

La forma más sencilla de iniciar el desarrollo es utilizando el script incorporado: composer run dev

Este único comando comienza:

- ✅ Servidor de desarrollo Laravel
- ✅ Servidor de desarrollo Vite para recarga de módulos en caliente
- ✅ Trabajador de cola para trabajos en segundo plano
- ✅ Monitoreo de registros

#### Configuración de desarrollo manual[]

Alternativamente, puedes ejecutarlos por separado en diferentes ventanas de terminal:

npmhilopnpmbollo # Terminal 1 php artisan serve # Terminal 2 npm run dev # Terminal 1 php artisan serve # Terminal 2 yarn dev # Terminal 1 php artisan serve # Terminal 2 pnpm dev # Terminal 1 php artisan serve # Terminal 2 bun dev

---

### Edificio para producción[]

Cuando esté listo para implementar, siga estos pasos para optimizar su aplicación.

#### 1\. Construir y optimizar[]

npmhilopnpmbollo npm run build yarn build pnpm build bun run build

Ejecute estos comandos para almacenar en caché la configuración y las rutas para obtener un mejor rendimiento: php artisan config:cache php artisan route:cache php artisan view:cache composer install --optimize-autoloader --no-dev

#### 2\. Actualizar entorno[]

Asegure su `.env` El archivo está configurado para producción: APP_ENV=production APP_DEBUG=false APP_URL=https://yourdomain.com

# Estructura de archivos de Laravel

El **Plantilla de TailAdmin Laravel** aprovecha la estructura robusta de Laravel 12 combinada con las modernas herramientas frontend de Vite y Tailwind CSS v4. Se adhiere a las convenciones estándar de Laravel y al mismo tiempo organiza recursos específicos del panel de manera eficiente.

## Directorio raíz[]

La raíz sigue la estructura estándar de la aplicación Laravel.

Archivo / carpeta

Descripción

`app/`

**Lógica central:** Controladores, modelos y middleware viven aquí.

`resources/`

**Activos frontend:** Vistas , CSS sin formato y JavaScript.

`routes/`

**Enrutamiento:** Contiene `web.php` para definir las rutas de su aplicación.

`public/`

**Raíz web:** El punto de entrada al servidor web; contiene activos compilados.

`vite.config.js`

**Agrupador de activos:** Configuración para Vite, manejo de compilación CSS/JS y reemplazo de módulos activos.

`composer.json`

**Dependencias de PHP:** Gestiona paquetes backend y carga automática.

`package.json`

**Dependencias de nodos:** Administra herramientas frontend como Tailwind CSS y Vite.

## Directorio de recursos []

Aquí es donde vive la integración del frontend de TailAdmin.

recursos/

css/

aplicación.css\# Punto de entrada Tailwind CSS v4

js/

app.js\# Archivo JavaScript principal; inicializa Alpine.js

vistas/\# Plantillas de cuchillas

componentes/\# Componentes de cuchilla reutilizables

panel/

forma/

diseños/\# Diseños maestros

aplicación.blade.php\# Envoltorio de la aplicación principal

guest.blade.php\# Diseño de autenticación

barra lateral.blade.php

páginas/\# Visitas a páginas individuales

comercio electrónico.blade.php

analítica.blade.php

…

### Diferencias y características clave[]

#### `resources/css/app.css`[]

En esta configuración moderna, es posible que notes la ausencia de un gran `tailwind.config.js`. Eso es porque **Tailwind CSS v4** le permite configurar sus variables temáticas y configuraciones directamente dentro de CSS usando variables CSS.

#### `resources/views/components/`[]

TailAdmin hace un uso intensivo de **Componentes de la hoja**. En lugar de repetir código para botones o listas de definiciones, puedes tener una sintaxis más clara como: <x-card title="Revenue"> <x-chart.bar :data="$data" /> </x-card>

#### `resources/views/layouts/`[]

El `app.blade.php` el diseño actúa como shell para su panel. Incluye automáticamente la barra lateral, el encabezado y los scripts estándar, por lo que solo debes concentrarte en ellos `@yield` o `$slot` área para la lógica de tu página.

# Diseño de la aplicación

TailAdmin utiliza un sistema de diseño robusto y responsivo creado con **Caja flexible** y **Viento de cola CSS**. Está diseñado para manejar interfaces de panel complejas y al mismo tiempo adaptarse sin problemas desde grandes monitores de escritorio a pantallas móviles.

## El envoltorio de diseño[]

La aplicación está envuelta en un contenedor principal que gestiona la relación entre los **Barra lateral** y el **Área de contenido principal**. <div className="flex h-screen overflow-hidden"> {/_ Sidebar Component _/} <Sidebar sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} /> {/_ Content Area Wrapper _/} <div className="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden"> {/_ Header Component _/} <Header sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} /> {/_ Main Page Content _/} <main> <div className="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10"> {children} </div> </main> </div> </div>

### Componentes estructurales clave[]

Componente

Rol

Comportamiento CSS

**Envoltorio**

Contenedor flexible que contiene barra lateral y contenido.

`flex h-screen overflow-hidden`\- Garantiza que la aplicación alcance la altura completa de la ventana gráfica y evita el desplazamiento del cuerpo.

**Barra lateral**

Menú de navegación.

`fixed` en el móvil / `static` en el escritorio. Usos `translate-x` para alternar animaciones sin problemas.

**Encabezado**

Navegación y acciones principales.

`sticky top-0 z-999`\- Permanece visible mientras se desplaza por el contenido.

**Principal**

Contenido dinámico de la página.

`flex-1` - Se expande para llenar el ancho restante. `overflow-y-auto` maneja el desplazamiento interno.

## Comportamiento receptivo[]

TailAdmin utiliza un **mobile-first** enfoque para su lógica de diseño.

### Escritorio []

En pantallas más grandes que `1024px`:

- El **Barra lateral** es estático y siempre visible.
- El **Contenido principal** se sienta al lado de la barra lateral, ocupando el espacio restante.

### Móvil y tableta[]

En pantallas más pequeñas:

- El **Barra lateral** becomes `absolute` o `fixed` y está oculto por defecto .
- A **Menú de hamburguesas** en el **Encabezado** alterna el `sidebarOpen` estado.
- Cuando está abierta, la barra lateral se desliza hacia adentro sobre el contenido.
- A menudo se utiliza una funcionalidad de superposición para cerrar la barra lateral al hacer clic afuera.

## Personalización del diseño[]

### Cambiar el ancho de la barra lateral[]

Navegue hasta su archivo de componente específico de la barra lateral y ajustar las clases de ancho .

### Ajuste del relleno de contenido[]

El contenedor de contenido principal utiliza `max-w-screen-2xl` para limitar el contenido ampliamente extendido en monitores ultra anchos. Puedes eliminar esta clase si prefieres un ancho 100% fluido. <!-- Fluid Width Example --> <div className="mx-auto w-full p-4 ...">

# Componentes de Laravel

TailAdmin Laravel proporciona un conjunto de componentes Blade totalmente responsivos para agilizar su proceso de desarrollo. Estos componentes están construidos teniendo en cuenta la flexibilidad, lo que le permite personalizarlos para que se ajusten a las necesidades de su proyecto manteniendo el rendimiento y la usabilidad.

Puede utilizar estos componentes utilizando la sintaxis de componentes estándar de Blade: <x-ui.component-name />

**A continuación se muestran algunos ejemplos de los componentes:**

### Alerta[]

Las alertas se utilizan para proporcionar mensajes de retroalimentación a los usuarios.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Descripción

Predeterminado

variante

“éxito” | “error” | “advertencia” | “información”

Especifica el tipo de alerta. Determina estilos e iconos.

”info”

título

cadena

El título principal de la alerta.

""

mensaje

cadena

El mensaje detallado de la alerta.

""

mostrar enlace

booleano

Si desea mostrar el enlace “Más información”.

falso

enlaceHref

cadena

La URL a la que apunta el enlace “Más información”.

”#“

linkText

cadena

El texto que se muestra para el enlace “Más información”.

”Más información” <x-ui.alert variant="success" title="Success Message" message="Be cautious when performing this action." showLink="true" linkHref="/" linkText="Learn more" /> <x-ui.alert variant="success" title="Success Message" message="Be cautious when performing this action." />

### Insignia[]

Las insignias se utilizan para mostrar pequeños indicadores de estado, recuentos o etiquetas.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

variante

”ligero” | “sólido"

"luz”

Estilo visual de la insignia

tamaño

”sm” | “md"

"md”

Tamaño de la insignia

color

“primario” | “éxito” | “error” | “advertencia” | “información” | “luz” | “oscuro"

"primario”

Color de la insignia

inicioIcono

cadena html

\-

Icono al inicio de la insignia

endIcon

cadena html

\-

Icono al final de la insignia <x-ui.badge variant="light" color="primary"> Primary </x-ui.badge> <x-ui.badge variant="light" color="success"> Success </x-ui.badge> <x-ui.badge variant="light" color="error"> Error </x-ui.badge>

### Botón[]

Los botones se utilizan para activar acciones, enviar formularios o navegar dentro de la aplicación.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

tamaño

”sm” | “md"

"md”

Define el tamaño del botón. Opțiuni: `"sm"` o `"md"`.

variante

“primario” | “esquema"

"primario”

Variante de estilo de botón. Opciones: `"primary"` o `"outline"`.

inicioIcono

cadena html

—

Icono opcional que se muestra antes del texto del botón.

endIcon

cadena html

—

Icono opcional que se muestra después del texto del botón.

discapacitado

booleano

falso

Desactiva el botón y aplica un estilo “deshabilitado”.

nombre de clase

cadena

""

Clases personalizadas adicionales para estilismo. <x-ui.button size="sm" variant="primary"> Button Text </x-ui.button> <x-ui.button size="md" variant="primary"> Button Text </x-ui.button>

### Tarjeta[]

Las tarjetas se utilizan para mostrar contenido y acciones relacionadas con un solo tema.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

imagen

cadena

nulo

URL de la imagen que se mostrará en la parte superior de la tarjeta.

título

cadena

nulo

El título de la tarjeta.

descripción

cadena

nulo

El texto de descripción de la tarjeta. <x-ui.card image="/images/cards/card-01.png" title="Card Title" description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi architecto aspernatur cum et ipsum." > <a       href="#"       class="inline-flex items-center gap-2 px-4 py-3 mt-4 text-sm font-medium text-white rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600"     > Read more </a> </x-ui.card>

### Entrada[]

Los campos de entrada permiten a los usuarios ingresar texto, contraseñas y otros datos.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

etiqueta

cadena

nulo

Etiqueta para el campo de entrada

nombre

cadena

Requerido

Atributo de nombre para la entrada

tipo

cadena

”texto”

Tipo de entrada

marcador de posición

cadena

""

Texto del marcador de posición

requerido

booleano

falso

Si el campo es obligatorio

discapacitado

booleano

falso

Si el campo está deshabilitado <x-form.input label="Password" type="password" name="password" placeholder="Enter your new password" required autofocus />

### Modal[]

Los modales se utilizan para mostrar contenido en una capa encima de la aplicación.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

está abierto

booleano

falso

Si el modal está abierto de forma predeterminada

mostrarBotónCerrar

booleano

verdadero

Si se debe mostrar el botón de cierre <x-ui.button size="sm" variant="primary" @click="$dispatch">Open Modal</x-ui.button> <x-ui.modal x-data="{ open: false }" @open-regular-modal.window="open = true" :isOpen="false" class="max-w-[600px]"> <div class="relative w-full rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10"> <h4 class="font-semibold text-gray-800 mb-7 text-title-sm dark:text-white/90"> Modal Heading </h4> <button @click="open = false" class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 sm:h-11 sm:w-11" > <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /> </svg> </button> <p class="text-sm leading-6 text-gray-500 dark:text-gray-400"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod est quis mauris lacinia pharetra. Sed a ligula ac odio condimentum aliquet a nec nulla. Aliquam bibendum ex sit amet ipsum rutrum feugiat ultrices enim quam. </p> </div> </x-ui.modal>

### Tabla[]

Las tablas se utilizan para mostrar datos en un formato estructurado.

Vista previaCódigo

#### Vista previa

![] <table class="w-full min-w-[1102px]"> <thead> <tr class="border-b border-gray-100 dark:border-gray-800"> <th class="px-5 py-3 text-left sm:px-6"> <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"> User </p> </th> <th class="px-5 py-3 text-left sm:px-6"> <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"> Project Name </p> </th> <th class="px-5 py-3 text-left sm:px-6"> <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"> Team </p> </th> <th class="px-5 py-3 text-left sm:px-6"> <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"> Status </p> </th> <th class="px-5 py-3 text-left sm:px-6"> <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"> Budget </p> </th> </tr> </thead> <tbody> <template x-for="order in orders" :key="order.id"> <tr class="border-b border-gray-100 dark:border-gray-800"> <td class="px-5 py-4 sm:px-6" colspan="1"> <div class="flex items-center gap-3"> <div class="w-10 h-10 overflow-hidden rounded-full"> <img :src="order.user.image" :alt="order.user.name"> </div> <div> <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90" x-text="order.user.name"></span> <span class="block text-gray-500 text-theme-xs dark:text-gray-400" x-text="order.user.role"></span> </div> </div> </td> <td class="px-5 py-4 sm:px-6"> <p class="text-gray-500 text-theme-sm dark:text-gray-400" x-text="order.projectName"></p> </td> <td class="px-5 py-4 sm:px-6"> <div class="flex -space-x-2"> <template x-for=" in order.team.images" :key="index"> <div class="w-6 h-6 overflow-hidden border-2 border-white rounded-full dark:border-gray-900"> <img :src="teamImage" alt="team member"> </div> </template> </div> </td> <td class="px-5 py-4 sm:px-6"> <p class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium" :class="getStatusClass" x-text="order.status"></p> </td> <td class="px-5 py-4 sm:px-6"> <p class="text-gray-500 text-theme-sm dark:text-gray-400" x-text="order.budget"></p> </td> </tr> </template> </tbody> </table>

# Elementos del formulario

TailAdmin proporciona un conjunto de elementos de formulario prediseñados para ayudarle a crear formularios funcionales y fáciles de usar rápidamente. Estos elementos se pueden personalizar con varios accesorios para adaptarse a sus necesidades específicas.

### Entrada[]

Un campo de entrada personalizable para texto, correo electrónico, contraseña y otros tipos de entrada de datos.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

tipo

“texto” | “número” | “correo electrónico” | “contraseña” | “fecha” | “hora” | cadena

”texto”

Tipo de campo de entrada

id

cadena

\-

ID para el elemento de entrada

nombre

cadena

\-

Atributo de nombre para la entrada

marcador de posición

cadena

\-

Texto de marcador de posición para la entrada

valor

cadena | número

\-

Valor actual de la entrada

enCambio
=> void

\-

Función para manejar cambios de entrada

nombre de clase

cadena

""

Clases CSS adicionales para la entrada

min

cadena

\-

Valor mínimo para entradas numéricas

max

cadena

\-

Valor máximo para entradas numéricas

paso

número

\-

Valor de paso para entradas numéricas

discapacitado

booleano

falso

Si la entrada está deshabilitada

éxito

booleano

falso

Si se debe mostrar el estado de éxito

error

booleano

falso

Si se debe mostrar el estado del error

hint

cadena

\-

Texto de sugerencia opcional que se muestra debajo de la entrada <div> <div> <Label>Email</Label> <Input placeholder="info@gmail.com" type="text" /> </div> <div> <Label>Email</Label> <Input placeholder="info@gmail.com" type="text" /> </div> </div>

### Seleccionar[]

Un menú desplegable selecciona la entrada para elegir de una lista de opciones.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

opciones

Opción\[\]

Requerido

Matriz de objetos de opción con propiedades de valor y etiqueta

marcador de posición

cadena

”Seleccione una opción”

Texto de marcador de posición para la entrada seleccionada

enCambio
=> void

Requerido

Función para manejar cambios de valor

nombre de clase

cadena

""

Clases CSS adicionales para la entrada seleccionada

valor predeterminado

cadena

""

Valor seleccionado predeterminado <div> <Label>Select Input</Label> <Select       options={options}       placeholder="Select Option"       onChange={handleSelectChange}       className="dark:bg-dark-900"     /> </div>

### Checkbox[]

Una entrada de casilla de verificación alternable para representar un valor verdadero/falso.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

etiqueta

cadena

\-

Etiqueta opcional para la casilla de verificación

comprobado

booleano

Requerido

Estado marcado de la casilla de verificación

nombre de clase

cadena

""

Clases CSS adicionales para la casilla de verificación

id

cadena

\-

Identificación única para la casilla de verificación

enCambio
=> void

Requerido

Cambiar el controlador de la casilla de verificación

discapacitado

booleano

falso

Estado deshabilitado de la casilla de verificación <div> <Checkbox checked={isChecked} onChange={setIsChecked} /> <span className="block text-sm font-medium text-gray-700 dark:text-gray-400"> Default </span> </div> <Checkbox     checked={isCheckedTwo}     onChange={setIsCheckedTwo}     label="Checked"   /> <Checkbox     checked={isCheckedDisabled}     onChange={setIsCheckedDisabled}     disabled     label="Disabled"   />

### Radio[]

Un conjunto de botones de opción para seleccionar una opción de un grupo.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

id

cadena

Requerido

Identificación única para el botón de opción

nombre

cadena

Requerido

Nombre del grupo de radio

valor

cadena

Requerido

Valor del botón de opción

comprobado

booleano

Requerido

Si el botón de opción está marcado

etiqueta

cadena

Requerido

Etiqueta para el botón de opción

enCambio
=> void

Requerido

Manejador de cambios de valor

nombre de clase

cadena

""

Clases adicionales opcionales

discapacitado

booleano

falso

Estado deshabilitado opcional para el botón de opción <Radio id="radio1" name="group1" value="option1" checked={selectedValue === "option1"} onChange={handleRadioChange} label="Default" /> <Radio id="radio2" name="group1" value="option2" checked={selectedValue === "option2"} onChange={handleRadioChange} label="Selected" /> <Radio id="radio3" name="group1" value="option3" checked={selectedValue === "option3"} onChange={handleRadioChange} label="Disabled" disabled={true} />

### Subir archivo[]

Una entrada de archivo para permitir a los usuarios cargar archivos a su aplicación.

Vista previaCódigo

#### Vista previa

![]

#### Accesorios

Prop

Tipo

Predeterminado

Descripción

nombre de clase

cadena

indefinido

Clases CSS adicionales para la entrada de archivos

enCambio
=> void

indefinido

Función para manejar cambios en la selección de archivos <div> <Label>Upload file</Label> <FileInput onChange={handleFileChange} className="custom-class" /> </div>

# Pantalla

En Tailwind CSS v4, la personalización del punto de interrupción se ha trasladado de los archivos de configuración de JavaScript a un enfoque “CSS-first”. Puede ver los puntos de interrupción predeterminados y extenderlos o anularlos directamente en su CSS.

## Puntos de interrupción predeterminados[]

Tailwind incluye cinco puntos de interrupción predeterminados inspirados en resoluciones de dispositivos comunes:

Punto de interrupción

Ancho mínimo

Ayudante CSS

`sm`

640px

`@media `

`md`

768px

`@media `

`lg`

1024px

`@media `

`xl`

1280px

`@media `

`2xl`

1536px

`@media `

## Personalización de puntos de interrupción[]

Para agregar un nuevo punto de interrupción en v4, agregue una variable CSS que comience con `--breakpoint-` a tu `@theme` bloquear. @theme { --breakpoint-3xl: 1920px; }

Esto creará automáticamente un `3xl:` variante que puedes utilizar en tu HTML: <div class="3xl:grid-cols-4 grid grid-cols-1"> <!-- Content --> </div>

## Configuración CSS-First[]

A diferencia de versiones anteriores donde podrías editar `tailwind.config.js`, Tailwind v4 le permite administrar todos sus tokens de diseño directamente en sus archivos CSS. Esto mantiene su configuración más cercana a sus estilos y simplifica la configuración.

# Modo oscuro

Tailwind CSS v4 incluye una versión incorporada `dark` variante que hace que el estilo para el modo oscuro sea sencillo. De forma predeterminada, detecta la preferencia del sistema del usuario, pero también puedes alternarla manualmente usando una clase.

## Uso básico[]

Para diseñar un elemento para el modo oscuro, agregue el `dark:` prefijo a cualquier clase de utilidad: <div class="bg-white text-black dark:bg-gray-900 dark:text-white"> <!-- Content --> </div>

## Alternar manualmente el modo oscuro[]

Si desea admitir la alternancia manual , Tailwind v4 lo simplifica con la configuración “CSS-first”. Puedes definir una variante personalizada que busque una clase específica en un elemento principal.

Añade esto a tu CSS: @custom-variant dark );

Ahora, siempre que el `.dark` La clase está presente en el `<html>` o `<body>` etiqueta, la `dark:` Los modificadores se activarán.

## Usando `next-themes`[]

Para proyectos Next.js como TailAdmin, recomendamos usar `next-themes` para administrar el estado de alternancia de clases. Maneja automáticamente las preferencias del sistema, la persistencia y la evitación de desajustes de hidratación.

El `ThemeToggler` componente en este proyecto utiliza `next-themes` para alternar el `dark` clasa: import { useTheme } from "next-themes"; const ThemeToggler = () => { const { theme, setTheme } = useTheme(); return ( <button onClick={() => setTheme}> Toggle Theme </button> ); };

# Color Swatch

You can easily customize the color palette of your TailAdmin project by modifying the `css` file `@theme`. TailAdmin comes with a variety of pre-defined color swatches that you can directly use or customize according to your needs.

**Here are some default color categories:**

### Brand

25#f2f7ff

50#ecf3ff

100#dde9ff

200#c2d6ff

300#9cb9ff

400#7592ff

500#465fff

600#3641f5

700#2a31d8

800#252dae

900#262e89

950#161950

### Blue Light

25#f5fbff

50#f0f9ff

100#e0f2fe

200#b9e6fe

300#7cd4fd

400#36bffa

500#0ba5ec

600#0086c9

700#026aa2

800#065986

900#0b4a6f

950#062c41

### Gray

25#fcfcfd

50#f9fafb

100#f2f4f7

200#e4e7ec

300#d0d5dd

400#98a2b3

500#667085

600#475467

700#344054

800#1d2939

900#101828

950#0c111d

### Orange

25#fffaf5

50#fff6ed

100#ffead5

200#fddcab

300#feb273

400#fd853a

500#fb6514

600#ec4a0a

700#c4320a

800#9c2a10

900#7e2410

950#511c10

### Success

25#f6fef9

50#ecfdf3

100#d1fadf

200#a6f4c5

300#6ce9a6

400#32d583

500#12b76a

600#039855

700#027a48

800#05603a

900#054f31

950#053321

### Error

25#fffbfa

50#fef3f2

100#fee4e2

200#fecdca

300#fda29b

400#f97066

500#f04438

600#d92d20

700#b42318

800#912018

900#7a271a

950#55160c

### Warning

25#fffcf5

50#fffaeb

100#fef0c7

200#fedf89

300#fec84b

400#fdb022

500#f79009

600#dc6803

700#b54708

800#93370d

900#7a2e0e

950#4e1d09

## Customizing Colors[]

You can easily customize your TailAdmin color palette by editing the `css` file. Here’s how to do it:

1.  **Add Custom Colors:** Inside the theme object, use the colors key to define custom colors.
2.  **Example Configuration:** @theme { --color-current: currentColor; --color-transparent: transparent; --color-white: #ffffff; --color-black: #101828; --color-brand-25: #f2f7ff; --color-brand-50: #ecf3ff; --color-brand-100: #dde9ff; --color-brand-200: #c2d6ff; --color-brand-300: #9cb9ff; --color-brand-400: #7592ff; --color-brand-500: #465fff; --color-brand-600: #3641f5; --color-brand-700: #2a31d8; --color-brand-800: #252dae; --color-brand-900: #262e89; --color-brand-950: #161950; --color-blue-light-25: #f5fbff; --color-blue-light-50: #f0f9ff; --color-blue-light-100: #e0f2fe; --color-blue-light-200: #b9e6fe; --color-blue-light-300: #7cd4fd; --color-blue-light-400: #36bffa; --color-blue-light-500: #0ba5ec; --color-blue-light-600: #0086c9; --color-blue-light-700: #026aa2; --color-blue-light-800: #065986; --color-blue-light-900: #0b4a6f; --color-blue-light-950: #062c41; --color-gray-25: #fcfcfd; --color-gray-50: #f9fafb; --color-gray-100: #f2f4f7; --color-gray-200: #e4e7ec; --color-gray-300: #d0d5dd; --color-gray-400: #98a2b3; --color-gray-500: #667085; --color-gray-600: #475467; --color-gray-700: #344054; --color-gray-800: #1d2939; --color-gray-900: #101828; --color-gray-950: #0c111d; --color-gray-dark: #1a2231; --color-orange-25: #fffaf5; --color-orange-50: #fff6ed; --color-orange-100: #ffead5; --color-orange-200: #fddcab; --color-orange-300: #feb273; --color-orange-400: #fd853a; --color-orange-500: #fb6514; --color-orange-600: #ec4a0a; --color-orange-700: #c4320a; --color-orange-800: #9c2a10; --color-orange-900: #7e2410; --color-orange-950: #511c10; --color-success-25: #f6fef9; --color-success-50: #ecfdf3; --color-success-100: #d1fadf; --color-success-200: #a6f4c5; --color-success-300: #6ce9a6; --color-success-400: #32d583; --color-success-500: #12b76a; --color-success-600: #039855; --color-success-700: #027a48; --color-success-800: #05603a; --color-success-900: #054f31; --color-success-950: #053321; --color-error-25: #fffbfa; --color-error-50: #fef3f2; --color-error-100: #fee4e2; --color-error-200: #fecdca; --color-error-300: #fda29b; --color-error-400: #f97066; --color-error-500: #f04438; --color-error-600: #d92d20; --color-error-700: #b42318; --color-error-800: #912018; --color-error-900: #7a271a; --color-error-950: #55160c; --color-warning-25: #fffcf5; --color-warning-50: #fffaeb; --color-warning-100: #fef0c7; --color-warning-200: #fedf89; --color-warning-300: #fec84b; --color-warning-400: #fdb022; --color-warning-500: #f79009; --color-warning-600: #dc6803; --color-warning-700: #b54708; --color-warning-800: #93370d; --color-warning-900: #7a2e0e; --color-warning-950: #4e1d09; --color-theme-pink-500: #ee46bc; --color-theme-purple-500: #7a5af8; /_ --- other theme configs -- _/ }

This structure allows you to define your project’s colors and makes it easy to use them throughout your components. **Simply use the color values in your classes like so:** <div class="bg-brand-500 text-white">Custom color background with text</div>

Feel free to adjust the color palette to suit your brand or design preferences.

# Personalización del espaciado

Tailwind CSS v4 viene con una escala de espaciado predeterminada completa que es perfecta para la mayoría de los proyectos. Sin embargo, puede ampliar o anular fácilmente esta escala directamente en su CSS utilizando la configuración “CSS-first”.

## Ampliación de la escala de espaciado[]

Para agregar un valor de espaciado personalizado, utilice el `--spacing-*` variables dentro de tu `@theme` bloquear. Esto le permite definir nuevos valores manteniendo intacta la escala predeterminada. @theme { --spacing-4_5: 1.125rem; --spacing-10_5: 2.625rem; }

Ahora puedes usar estos nuevos valores en tus utilidades tal como están predeterminados: <div class="p-4_5 m-10_5"> <!-- Custom padding and margin --> </div>

## Anulación de la escala predeterminada[]

Si desea reemplazar toda la escala de espaciado por la suya propia, puede configurar la `--spacing` variable. Esto establece un valor base que las utilidades utilizarán como multiplicador. @theme { --spacing: 0.25rem; /_ The default is 0.25rem _/ }

## Valores arbitrarios[]

Para ajustes puntuales en los que agregar una nueva variable temática no tiene sentido, la sintaxis de valores arbitrarios de Tailwind es perfecta. Funciona específicamente bien para diseños con píxeles perfectos. <div class="top-[117px] p-[10px]"> <!-- Custom one-off spacing --> </div>
