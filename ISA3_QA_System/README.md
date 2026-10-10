# Sistema de Formulación de Casos de Prueba - (ISA3 QA System)

**Grupo:** 1GS232  
**Profesor:** Arturo Murillo  

**Integrantes:**
- Miguel Caballero — 8-957-2172
- Jose Rodriguez — 4-823-1007
   

## Descripción

Aplicación web desarrollada en PHP puro para registrar y gestionar los
formularios de casos de prueba definidos en la Unidad 3 de la asignatura
Ingeniería de Software Aplicada III.

En este primer avance solo el Formulario 1 (Registro de Caso de Prueba) está
funcional; los formularios 2 al 10 aparecen como "Pendiente" en el dashboard.

## Stack

- PHP 8.x puro (sin frameworks)
- MySQL / MariaDB (XAMPP)
- PDO con prepared statements
- HTML5 + CSS3 + JavaScript vanilla

## Requisitos previos

- XAMPP instalado, con Apache y MySQL.
- Navegador web moderno.

## Instalación paso a paso

1. Abre XAMPP e inicia los servicios Apache y MySQL.

2. Copia la carpeta `ISA3_QA_System` dentro de `htdocs`. En este repositorio,
   la ruta final es:

   ```text
   C:\xampp\htdocs\ISA_III\ISA3_QA_System\
   ```

   Si cambias la ruta pública, ajusta la constante `ISA3_BASE_URL` en
   `bootstrap.php`.

3. Abre phpMyAdmin:

   ```text
   http://localhost/phpmyadmin
   ```

4. Importa la base de datos:
   - Selecciona la pestaña "Importar".
   - Selecciona `ISA3_QA_System/database/isa3_testing.sql`.
   - Presiona "Continuar".
   - Debe aparecer la base de datos `isa3_testing` con las tablas `usuarios`
     y `casos_prueba` vacías.

5. Ejecuta el script de cuentas de demostración una sola vez:

   ```text
   http://localhost/ISA_III/ISA3_QA_System/seed.php
   ```

   Se crearán estas cuentas:
   - `admin@isa3.local` / `Admin123!` (rol admin)
   - `tester@isa3.local` / `Tester123!` (rol tester)

   **Importante:** elimina `seed.php` después de usarlo.

6. Abre la aplicación:

   ```text
   http://localhost/ISA_III/ISA3_QA_System/public/login.php
   ```

7. Inicia sesión con una de las cuentas de demostración.

## Cuentas de demostración

| Rol | Correo | Contraseña |
|---|---|---|
| Admin | admin@isa3.local | Admin123! |
| Tester | tester@isa3.local | Tester123! |

## Funcionalidades implementadas

- Registro e inicio de sesión con contraseñas hasheadas con bcrypt.
- Roles: administrador y tester.
- Dashboard con métricas de total de casos, casos exitosos, casos fallidos y
  distribución por técnica.
- Resumen de los 10 formularios de la Unidad 3.
- Formulario 1 (Registro de Caso de Prueba) completamente funcional:
  - Generación automática de códigos `CP-001`, `CP-002`, etc.
  - Cambio dinámico de subtécnicas según la técnica seleccionada.
  - Ayuda contextual por subtécnica.
  - Listado, consulta, edición y eliminación de casos.
  - Permisos por rol: el tester solo ve y edita sus propios casos; el admin
    ve, edita y elimina todos.
- Gestión de usuarios en modo solo lectura para administradores.

## Formularios pendientes

Los siguientes formularios se implementarán en avances posteriores:

2. Matriz de Clases de Equivalencia
3. Análisis de Valor Límite
4. Tabla de Decisión
5. Cobertura de Caja Blanca
6. Plan de Pruebas del Proyecto
7. Rúbrica de Evaluación
8. Autoevaluación y Coevaluación
9. Portafolio de Evidencias
10. Registro de Incidentes

## Estructura del proyecto

```text
ISA3_QA_System/
├── public/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── casos/
│   │   ├── listar.php
│   │   ├── crear.php
│   │   ├── guardar.php
│   │   ├── ver.php
│   │   ├── editar.php
│   │   ├── actualizar.php
│   │   └── eliminar.php
│   ├── admin/
│   │   └── usuarios.php
│   └── assets/
│       ├── css/estilos.css
│       └── js/
│           ├── app.js
│           └── subtecnicas.js
├── src/
│   ├── Controller/
│   │   ├── AuthController.php
│   │   └── CasoController.php
│   ├── Repository/
│   │   ├── UsuarioRepository.php
│   │   └── CasoRepository.php
│   ├── Service/
│   │   └── CasoService.php
│   ├── Support/
│   │   ├── Auth.php
│   │   └── Csrf.php
│   └── View/
│       └── layout/
│           ├── header.php
│           ├── sidebar.php
│           └── footer.php
├── config/
│   └── database.php
├── database/
│   └── isa3_testing.sql
├── bootstrap.php
├── seed.php
├── .env.example
└── README.md
```

## Contexto

Este sistema fue creado para documentar los casos de prueba aplicados al MVP
`Marketplace_Search_Bus`, desarrollado previamente en la misma asignatura.

El MVP es el Sistema Bajo Prueba (SUT) y no se modifica desde este proyecto.

## Seguridad

- Contraseñas hasheadas con `password_hash()` usando bcrypt.
- Sesiones con cookies `HttpOnly` y `SameSite=Strict`.
- PDO prepared statements en todas las consultas.
- Protección CSRF en formularios.
- Escape de salida HTML con `htmlspecialchars()`.
