# Manual de Operador Macro / SuperAdmin (SaaS B2B) — SIGEN v4.0

**Sistema Integral de Gestión de Empleados y Nómina (SIGEN v4.0)**  
Universidad Bicentenaria de Aragua — Programación IV  
Autores: Fredderick Moreno (C.I. V-21.202.137) & Iván Arturo Zambrano Acosta (C.I. V-29.503.564)

---

## 1. Arquitectura de Dos Portales Desacoplados

En la Fase 4 de SIGEN, el menú del **Operador Macro (SuperAdmin)** está 100% desacoplado de las tareas operativas de las empresas clientes.

### Opciones Visibles en el Menú Superior:
* 📊 **Dashboard Macro:** Indicadores consolidados del negocio (empresas activas vs suspendidas, total de trabajadores bajo plataforma, total de sedes y normativas legales vigentes).
* 🏢 **Empresas Clientes:** Catálogo B2B para auditar el uso de cupos, límites de sedes y estado de servicio.
* ➕ **Registrar Empresa:** Formulario de alta para nuevos clientes corporativos.
* ⚖️ **Leyes & LOTTT:** Gestor centralizado de Cestaticket, retenciones legales y normativas laborales.

> **Importante:** El menú principal del SuperAdmin **no muestra** módulos de trabajadores, asistencias, préstamos ni nóminas, garantizando la separación entre la gestión de la plataforma SaaS y la administración interna de cada cliente.

---

## 2. Registro de Empresas Clientes y Generación de Credenciales

Al afiliar a una nueva empresa en `/empresas/create`:
1. **Datos Corporativos:** RIF, Nombre Comercial, Razón Social, Teléfono de Notificaciones WhatsApp y Correo de Contacto.
2. **Cuotas del Plan SaaS Contratado:**
   * `Límite de Trabajadores`: Cupo máximo de empleados que el cliente puede dar de alta (ej: 15, 50, 200).
   * `Límite de Sedes`: Cantidad máxima de sucursales o centros de trabajo geocercados.
3. **Sedes Iniciales:** Se pueden añadir múltiples sedes con el botón **+ Añadir Sede**, configurando sus coordenadas GPS satelitales y radio de geocerca en metros.
4. **Generación Automática de Acceso para el Cliente:**
   * Nombre y correo institucional del Administrador de la Empresa.
   * Contraseña segura: si no se escribe manualmente, el sistema genera una clave aleatoria de alta seguridad.
5. **Tarjeta de Entrega de Credenciales:** Al guardar, se despliega una ficha con botón **📋 Copiar Credenciales** para enviárselas al representante de la empresa por WhatsApp o correo.

---

## 3. Control de Suspensión por Mora y Bloqueo de Acceso

Si un cliente se atrasa en el pago mensual de la suscripción SaaS:
1. En el catálogo de empresas o en su ficha, presione **⛔ Suspender por Mora**.
2. Indique el motivo administrativo en el modal y confirme la suspensión.
3. El middleware `CheckEmpresaActiva` intercepta en tiempo real cualquier petición de los usuarios de esa empresa.
4. Los usuarios reciben una pantalla oficial de corte de servicio (código 403) con el RIF, razón social y datos de contacto para regularizar su pago (manteniendo activo el botón de cerrar sesión).
5. Cuando el cliente pague, el SuperAdmin presiona **✓ Reactivar Acceso** y el servicio se reanuda de inmediato.

---

## 4. Modo Auditor (Impersonación de Empresa)

Para dar soporte técnico o auditar irregularidades sin pedirle la contraseña al cliente:
1. En la ficha de la empresa, presione **👁️ Entrar al Panel de la Empresa**.
2. Se activa una sesión de auditoría (`empresa_activa_id`) y se despliega una barra superior ámbar:
   > `👁️ MODO AUDITOR ACTIVO: Inspeccionando empresa [Nombre] (RIF: ...) [⬅️ Volver al Panel Macro]`
3. El menú superior se transforma en el menú operativo de la empresa (Personal, Asistencias GPS, Préstamos, Nóminas) permitiendo al auditor revisar cada registro.
4. Al finalizar la revisión, presione **⬅️ Volver al Panel Macro** para regresar al portal de SuperAdmin.

---

## 5. Actualización de Leyes y Parámetros Laborales (LOTTT)

En `/leyes`, el SuperAdmin puede incorporar o actualizar decretos de la LOTTT:
* Monto del **Cestaticket Socialista** (Gaceta Oficial).
* Porcentajes de retención obligatoria: **IVSS (4%)**, **FAOV (1%)**, **RPE (0.5%)**.
* Normativas de descanso legal: mínimo 1 hora continua de almuerzo (Art. 168 LOTTT), días de vacaciones y utilidades.
* Todas las liquidaciones de nómina de todas las empresas clientes adoptan estos valores automáticamente.
