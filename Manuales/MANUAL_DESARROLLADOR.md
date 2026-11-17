# Manual de Desarrollador: SIGEN v4.0

**Sistema Integral de Gestión de Empleados y Nómina (SIGEN v4.0)**  
Universidad Bicentenaria de Aragua — Programación IV  
Autores: Fredderick Moreno (C.I. V-21.202.137) & Iván Arturo Zambrano Acosta (C.I. V-29.503.564)

---

## 1. Stack Tecnológico y Arquitectura
* **Framework Backend:** Laravel 12 / PHP 8.2+
* **Frontend:** Blade Templates + Tailwind CSS v4 + Vite
* **Base de Datos:** SQLite / MySQL con Eloquent ORM
* **Geolocalización:** Fórmula de Haversine para cálculo de distancias ortodrómicas
* **Patrón de Diseño:** MVC (Modelo-Vista-Controlador) con Middleware de Tenancy y RBAC

---

## 2. Estructura de Directorios Clave

```
Control de Acceso-Sumativa 4/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AjusteSalarialController.php   # Préstamos y deducciones extras
│   │   │   ├── AsistenciaController.php       # Marcaje GPS, kiosco y retiro
│   │   │   ├── DashboardController.php        # Router contextual Macro vs Tenant
│   │   │   ├── EmpresaController.php          # B2B SaaS, credenciales y suspensión
│   │   │   ├── EmpresaUsuarioController.php   # RBAC granular para equipo de empresa
│   │   │   ├── NominaController.php           # Liquidación LOTTT y recibos
│   │   │   ├── ParametroLegalController.php   # Mantenimiento central de leyes
│   │   │   ├── SedeController.php             # Gestión de sedes y geocercas
│   │   │   └── TrabajadorController.php       # Personal con multi-sede y horario
│   │   └── Middleware/
│   │       └── CheckEmpresaActiva.php         # Intercepta empresas suspendidas por mora
│   └── Models/
│       ├── AjusteSalarial.php
│       ├── Asistencia.php
│       ├── Empresa.php
│       ├── ParametroLegal.php
│       ├── Sede.php
│       ├── Trabajador.php
│       └── User.php                           # Métodos isSuperAdmin, isAdminEmpresa, tienePermiso
├── database/
│   ├── database.sqlite                        # Base de datos pre-configurada
│   └── migrations/                            # 13 migraciones del sistema
├── resources/
│   └── views/
│       ├── ajustes/                           # Vistas de préstamos
│       ├── asistencias/                       # Auditoría GPS y kiosco
│       ├── empresas/                          # Alta de empresas y sedes
│       ├── errors/
│       │   └── empresa_suspendida.blade.php   # Pantalla 403 de suspensión por mora
│       ├── layouts/
│       │   └── app.blade.php                  # Navbar condicional Macro vs Tenant vs Auditor
│       ├── leyes/                             # Gestor de parámetros legales LOTTT
│       ├── nominas/                           # Nóminas y recibos
│       ├── trabajadores/                      # Personal y expedientes
│       └── usuarios_empresa/                  # Matriz de permisos RBAC
└── routes/
    └── web.php                                # Registro completo de rutas
```

---

## 3. Despliegue Local Rápido

Para levantar el proyecto en un nuevo entorno:

```bash
# 1. Acceder al directorio
cd "Control de Acceso-Sumativa 4"

# 2. Instalar dependencias PHP y Node
composer install
npm install && npm run build

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Migrar base de datos y generar enlace simbólico de almacenamiento
php artisan migrate
php artisan storage:link

# 5. Iniciar servidor de desarrollo
php artisan serve
```

### Credenciales Predeterminadas:
* **SuperAdmin (Operador Macro):** `fredderickjms@gmail.com`
* **Empresa Cliente Demo:** Creada con RIF `J-12345678-0`.
