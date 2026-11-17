# SIGEN v4.0 — Sistema Integral de Gestión de Empleados y Nómina

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)

**Universidad Bicentenaria de Aragua — Decanato de Ingeniería de Sistemas**  
**Cátedra:** Programación IV (FEE18P - Sección B)  
**Facilitador:** Ing. Ricardo Montero, MSc.  
**Evaluación:** Actividad Sumativa 4 — Fase III Final (Proyecto Integral de Aplicación)  
**Autores:**
* **Fredderick Moreno** — C.I. V-21.202.137
* **Iván Arturo Zambrano Acosta** — C.I. V-29.503.564

---

## 📌 Organización de Ramas en GitHub

Para preservar la integridad de cada fase evaluativa sin sobreescribir el trabajo anterior:

| Rama | Descripción | Estado |
| :--- | :--- | :--- |
| **`main`** | Versión de la Fase II (Sumativa 2) preservada intacta con su estructura original. | Versión Base |
| **`sumativa-4`** | **Versión Final (Fase IV)** con arquitectura Multi-Tenant B2B, desacoplamiento de portales, RBAC y geocerca anti-fraude. | **Versión de Entrega Final** |

Para cambiar a la versión final en consola:
```bash
git checkout sumativa-4
```

---

## 🚀 Novedades y Módulos de la Fase 4 (Final)

### 1. Dos Portales Estrictamente Desacoplados
* **Panel Macro (SuperAdmin SaaS):** El menú principal del Operador Macro solo muestra opciones de negocio B2B: *Dashboard Macro*, *Empresas Clientes*, *Registrar Empresa* y *Leyes Laborales LOTTT*. No contiene módulos de trabajadores, asistencias ni préstamos.
* **Portal de Empresa (Tenant):** Portal operativo para cada empresa cliente con acceso a *Personal*, *Asistencias GPS*, *Préstamos / Ajustes*, *Nóminas LOTTT* y *Equipo & Permisos*.

### 2. Generación Automática de Credenciales
* Al dar de alta una empresa, el sistema genera de forma automática el usuario administrador del cliente (`admin_empresa`) con contraseña segura aleatoria.
* Se despliega una tarjeta interactiva con botón **📋 Copiar Credenciales** para facilitar la entrega al cliente por WhatsApp o correo.

### 3. Control de Cuotas y Suspensión por Mora
* Se asignan cuotas por contrato: `limite_trabajadores` y `limite_sedes`. El sistema bloquea nuevos registros si se alcanza el cupo contratado.
* Botón **⛔ Suspender por Mora**: bloquea mediante middleware (`CheckEmpresaActiva`) el acceso a todos los usuarios de la empresa morosa mediante una pantalla informativa 403, permitiendo el cierre de sesión y la reactivación inmediata al regularizar el pago.

### 4. Modo Auditor (Impersonación de Clientes)
* El SuperAdmin puede ingresar al panel de cualquier empresa cliente haciendo clic en **👁️ Entrar al Panel de la Empresa**.
* Una barra ámbar superior identifica el modo auditor activo y permite regresar al Panel Macro con un solo clic.

### 5. Matriz de Permisos RBAC Granular para la Empresa
* El Administrador de la Empresa puede crear usuarios secundarios (supervisores, analistas, contadores) y delegar permisos independientes:
  * Otorgar préstamos y deducciones
  * Aprobar permisos y retiros por imprevisto
  * Ver expedientes digitales de personal
  * Modificar, cargar y editar trabajadores
  * Procesar nóminas periódicas y generar recibos LOTTT
  * Auditar asistencias satelitales GPS

### 6. Personal Multi-Sede y Horario con Almuerzo
* Cada trabajador incluye número de **teléfono móvil obligatorio** y **PIN personal anti-fraude**.
* Posibilidad de asociar al empleado a **una o varias sedes simultáneamente**.
* Horario individual con tolerancia en minutos y switch para descanso legal de almuerzo (Art. 168 LOTTT).

### 7. Marcaje Satelital Anti-Fraude, Kiosco y Retiro por Imprevisto
* **Marcaje Inteligente sin Desplegable:** Detección automática de Entrada, Salida a Almuerzo, Retorno y Fin de Jornada según el horario.
* **Geocerca Satelital GPS (Haversine):** Rechaza marcajes fuera del radio de las sedes autorizadas y notifica al teléfono de la empresa.
* **Botón de Retiro por Imprevisto:** Permite salidas justificadas antes de hora.
* **Terminal Kiosco (`/kiosco`):** Alternativa presencial para casos de daño o descarga del teléfono del trabajador.

### 8. Préstamos, Deducciones Extras y Nómina LOTTT
* Registro de préstamos y anticipos en cuotas, con switch de visibilidad para decidir si se refleja en el recibo de pago.
* Liquidación quincenal/semanal con retenciones de ley (IVSS 4%, FAOV 1%, RPE 0.5%), Cestaticket y emisión de Recibos de Pago oficiales.

---

## 📚 Manuales y Documentación Incluida

En la carpeta [`Manuales/`](Manuales/) se encuentran disponibles los manuales oficiales en formatos **PDF, Word (.docx) y Markdown**:

1. [Manual de SuperAdmin (Gestión SaaS y Empresas)](Manuales/MANUAL_SUPERADMIN.md) — ([PDF](Manuales/Manual%20de%20SuperAdmin%20-%20Gestion%20SaaS%20y%20Empresas.pdf) / [DOCX](Manuales/Manual%20de%20SuperAdmin%20-%20Gestion%20SaaS%20y%20Empresas.docx))
2. [Manual de Empresa (Operación del Sistema SIGEN)](Manuales/MANUAL_EMPRESA.md) — ([PDF](Manuales/Manual%20de%20Empresa%20-%20Operacion%20del%20Sistema%20SIGEN.pdf) / [DOCX](Manuales/Manual%20de%20Empresa%20-%20Operacion%20del%20Sistema%20SIGEN.docx))
3. [Manual de Desarrollador](Manuales/MANUAL_DESARROLLADOR.md) — ([PDF](Manuales/Manual%20de%20Desarrollador%20-%20SIGEN.pdf) / [DOCX](Manuales/Manual%20de%20Desarrollador%20-%20SIGEN.docx))
4. [Manual de Usuario General](Manuales/MANUAL_EMPRESA.md) — ([PDF](Manuales/Manual%20de%20Usuario%20-%20SIGEN.pdf) / [DOCX](Manuales/Manual%20de%20Usuario%20-%20SIGEN.docx))

---

## 🛠️ Instrucciones de Instalación y Ejecución

```bash
# 1. Clonar el repositorio y cambiarse a la rama sumativa-4
git clone https://github.com/fredd-code/Proyecto-Prog.-IV.git
cd Proyecto-Prog.-IV
git checkout sumativa-4

# 2. Acceder al proyecto
cd "Control de Acceso-Sumativa 4"

# 3. Instalar dependencias
composer install
npm install && npm run build

# 4. Configurar el archivo de entorno
cp .env.example .env
php artisan key:generate

# 5. Migrar la base de datos y generar enlace de almacenamiento
php artisan migrate
php artisan storage:link

# 6. Iniciar el servidor
php artisan serve
```

### Acceso Inicial de Prueba:
* **URL:** `http://127.0.0.1:8000/login`
* **SuperAdmin:** `fredderickjms@gmail.com`
* **Terminal Kiosco:** `http://127.0.0.1:8000/kiosco`
