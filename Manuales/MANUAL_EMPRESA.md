# Manual de Operación para la Empresa Cliente (Portal Tenant) — SIGEN v4.0

**Sistema Integral de Gestión de Empleados y Nómina (SIGEN v4.0)**  
Universidad Bicentenaria de Aragua — Programación IV  
Autores: Fredderick Moreno (C.I. V-21.202.137) & Iván Arturo Zambrano Acosta (C.I. V-29.503.564)

---

## 1. Acceso Inicial al Portal de la Empresa

El Administrador de la Empresa ingresa en `/login` con las credenciales suministradas por el Operador Macro de SIGEN.

El dashboard de la empresa despliega:
* Porcentaje de ocupación del cupo de trabajadores contratados.
* Cantidad de sedes autorizadas para marcaje satelital.
* Conteo de asistencias y retardos del día en tiempo real.
* Préstamos y deducciones pendientes por cobrar en nómina.

---

## 2. Matriz de Permisos RBAC para el Equipo de Trabajo

En el módulo **🔐 Equipo & Permisos** (`/equipo`), el Administrador de la Empresa puede crear cuentas de usuario secundarias (supervisores, jefes de almacén, analistas contables) y asignarles funciones de forma granular:

| Permiso Módulo | Función Autorizada |
| :--- | :--- |
| **💳 Préstamos y Deducciones** | Otorgar préstamos, anticipos y definir si se reflejan en el recibo de pago. |
| **⚠️ Permisos y Retiros** | Aprobar salidas imprevistas antes del horario o ausencias justificadas. |
| **📁 Ver Expedientes** | Consultar y descargar cédulas, títulos y constancias médicas del trabajador. |
| **👥 Modificar Personal** | Dar de alta personal, editar salarios, cambiar sedes autorizadas y actualizar PIN. |
| **📑 Procesar Nómina y Recibos** | Calcular nóminas periódicas y generar recibos de pago oficiales LOTTT. |
| **⏱️ Auditar Asistencias GPS** | Consultar los registros satelitales y las alertas por fuera de geocerca. |

---

## 3. Ficha de Trabajadores: Multi-Sede y Horario con Almuerzo

Al registrar o editar a un trabajador en `/trabajadores`:
1. **Teléfono Móvil Obligatorio:** Se registra el número celular para el despacho de alertas de incidencias.
2. **PIN de Seguridad Anti-Fraude:** Clave personal de 4 a 10 dígitos requerida al marcar para evitar fraudes entre compañeros.
3. **Asignación Multi-Sede:** El trabajador puede asignarse a **una o varias sedes simultáneamente** (ej: Sede Principal y Almacén Secundario). El sistema validará su presencia satelital si se encuentra dentro del radio permitido de cualquiera de ellas.
4. **Horario Individual y Descanso Legal LOTTT:**
   * Hora de entrada, hora de salida y minutos de tolerancia para retardos.
   * Switch de **Aplica Hora de Almuerzo / Descanso Legal**: define inicio y fin del descanso (mínimo 1 hora continua según el Art. 168 de la LOTTT).

---

## 4. Marcaje Inteligente Anti-Fraude y Kiosco de Recepción

El sistema de marcaje de SIGEN funciona sin selectores desplegables confusos:
* **Detección Automática de Jornada:** El sistema analiza la hora actual del servidor frente al horario asignado al trabajador y detecta automáticamente si se trata de:
  1. *Entrada Matutina*
  2. *Salida a Almuerzo*
  3. *Regreso de Almuerzo*
  4. *Salida Fin de Jornada*
* **Geocerca Satelital GPS (Haversine):** Al marcar desde el móvil o navegador, el GPS calcula la distancia a las sedes asignadas. Si el empleado se encuentra fuera del radio permitido, el marcaje es rechazado y se envía una notificación inmediata al teléfono de la empresa.
* **Botón de Retiro por Imprevisto:** Permite al trabajador registrar una salida antes de tiempo por causa de fuerza mayor, registrando el motivo y notificando a la gerencia.
* **Terminal Kiosco de Recepción (`/kiosco`):** Alternativa física presencial en recepción o caseta de vigilancia para cuando al trabajador se le dañe el teléfono o se quede sin batería.

---

## 5. Préstamos, Descuentos Extras y Nómina LOTTT

* **Módulo de Préstamos / Ajustes (`/ajustes`):**
  * Permite registrar deducciones o asignaciones extraordinarias estableciendo el número de cuotas (ej: préstamo a 6 cuotas quincenales).
  * **Switch de Privacidad:** Permite decidir si el descuento se reflejará visiblemente en el recibo de pago o si se maneja como acuerdo interno.
* **Liquidación de Nómina (`/nominas`):**
  * Procesa nóminas semanales o quincenales calculando automáticamente el Cestaticket devengado, deducciones de ley LOTTT (IVSS 4%, FAOV 1%, RPE 0.5%) y cobrando automáticamente la cuota activa de préstamos.
  * Genera el **Recibo de Pago Oficial** con membrete de la empresa, desglose de asignaciones, deducciones legales y balance neto a pagar.
