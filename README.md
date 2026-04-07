# FUNDACITE Carabobo — Sistema de Gestión de Actividades
## Guía de Instalación

---

## ✅ REQUISITOS
- XAMPP (PHP 8.1+ / MySQL 5.7+)
- Apache con mod_rewrite habilitado

---

## 🚀 INSTALACIÓN PASO A PASO

### 1. Copiar el proyecto
Coloca la carpeta `fundacite` dentro de:
```
C:\xampp\htdocs\fundacite
```

### 2. Crear la base de datos
1. Abre **phpMyAdmin** → http://localhost/phpmyadmin
2. Crea una base de datos llamada: `fundacite_db`
3. Importa el archivo: `database/fundacite.sql`

### 3. Configurar la conexión
Edita el archivo `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'fundacite_db');
define('DB_USER', 'root');   // tu usuario MySQL
define('DB_PASS', '');       // tu contraseña MySQL
```

### 4. Habilitar mod_rewrite en XAMPP
Edita `C:\xampp\apache\conf\httpd.conf`:
- Busca `#LoadModule rewrite_module` y quita el `#`
- Busca el bloque `<Directory "C:/xampp/htdocs">` y cambia:
  `AllowOverride None` → `AllowOverride All`
- Reinicia Apache

### 5. Acceder al sistema
```
http://localhost/fundacite
```

### 6. Primer acceso (Admin)
- **Email:** `admin@fundacite.gob.ve`
- **Contraseña:** *(dejar vacía — primer login)*
- El sistema te pedirá crear una contraseña nueva

---

## 📁 ESTRUCTURA DE ARCHIVOS
```
fundacite/
├── .htaccess               ← Redirige todo a /public
├── app/
│   ├── controllers/        ← Lógica de negocio
│   ├── models/             ← Acceso a datos
│   ├── views/              ← Interfaces de usuario
│   └── core/               ← Router, App, helpers
├── config/
│   ├── app.php             ← Configuración principal
│   ├── database.php        ← Credenciales MySQL
│   └── mail.php            ← Servicio de correo
├── database/
│   └── fundacite.sql       ← Script de base de datos
└── public/
    ├── .htaccess           ← Front controller
    ├── index.php           ← Punto de entrada
    ├── css/style.css
    ├── js/main.js
    └── uploads/            ← Logos e imágenes (auto-creado)
```

---

## 👥 ROLES DEL SISTEMA
| Rol | Permisos |
|-----|----------|
| **Admin** | Control total: crear, editar, eliminar, aprobar, gestionar usuarios, configurar |
| **Operativo** | Crear y editar sus actividades, cambiar contraseña propia |
| **Visualización** | Solo lectura: ver actividades, calendario, dashboard |

---

## 📧 CONFIGURACIÓN DE CORREO
En el panel de **Configuración** (admin):
- Ingresa los datos SMTP de Gmail u otro proveedor
- Para Gmail: usa una **contraseña de aplicación**
  (Google → Cuenta → Seguridad → Verificación en 2 pasos → Contraseñas de app)

---

## 🛡️ NOTAS DE SEGURIDAD
- Cambia las credenciales de la base de datos en producción
- En producción descomenta la regla HTTPS en el `.htaccess` raíz
- La carpeta `public/uploads/` se crea automáticamente con permisos 755

---

## 🐛 SOLUCIÓN DE ERRORES COMUNES

**Error 404 en todas las páginas:**
→ Verifica que `AllowOverride All` esté activo en httpd.conf y reinicia Apache

**Error de conexión a base de datos:**
→ Verifica credenciales en `config/database.php`

**Imágenes no suben:**
→ Verifica permisos de escritura en `public/uploads/`

**Página en blanco:**
→ Activa `display_errors = On` en `php.ini` para ver el error
