# 🎮 BacklogQuest

**BacklogQuest** es una aplicación web diseñada para gestionar tu colección personal de videojuegos, registrar tus horas de juego (*playtime*), guardar notas de progreso (*checkpoints*) y visualizar estadísticas detalladas de tu biblioteca.

---

## ✨ Características Principales

* **🔐 Sistema de Autenticación Completo y Seguro:**
  * Registro de usuarios con encriptación de contraseñas (`BCRYPT`).
  * Verificación obligatoria de cuentas mediante envío de tokens únicos por correo electrónico (**PHPMailer** + SMTP).
  * Recuperación y restablecimiento autónomo de contraseñas con tokens temporales de expiración.
  * Opción de sesión persistente ("Recordar mi sesión").
* **📚 Gestión Avanzada de Biblioteca:**
  * Búsqueda e integración con APIs externas de videojuegos (IGDB) para importar títulos, carátulas verticales y plataformas automáticamente.
  * Organización alfabética automática (`A-Z`).
  * **Filtrado dinámico en tiempo real:** Búsqueda instantánea por nombre y botones de filtro por familias de consolas (*PlayStation, Xbox, Nintendo, PC*), ocultando automáticamente las categorías sin juegos.
  * **Control de estado y progreso:** Estados personalizables (*Pendiente, Jugando, Pausado, Completado, Abandonado*), registro de horas jugadas, cambio rápido de estado desde la tarjeta y sistema de notas rápidas.
* **📊 Panel de Estadísticas (`estadisticas.php`):**
  * Resumen visual del tiempo total invertido, distribución por plataformas y porcentaje de juegos completados.
* **🎨 Interfaz Moderna y Responsiva:**
  * Construida con **Bootstrap 5** con soporte nativo para **Modo Oscuro / Claro** persistente y carga temprana sin parpadeos.
  * Diseño adaptativo optimizado para escritorio y dispositivos móviles.

---

## 🛠️ Tecnologías Utilizadas

* **Frontend:** HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6+ / Fetch API), SweetAlert2.
* **Backend:** PHP 8+ (PDO para consultas preparadas y seguras).
* **Base de Datos:** MySQL / MariaDB.
* **Librerías Externas:** PHPMailer (envío de correos transaccionales vía SMTP).

---

## 📂 Estructura del Proyecto

```text
BacklogQuest/
├── assets/
│   ├── favicon/                   # Iconos de aplicación y webmanifest
│   ├── layout.css                 # Estilos personalizados y ajustes de tema
│   ├── logo.png                   # Logotipo principal
│   ├── main.js                    # Lógica frontend, Fetch API, filtros y modo oscuro
│   └── portada-placeholder.svg    # Imagen por defecto para juegos sin carátula
├── includes/
│   ├── PHPMailer/                 # Librería PHPMailer (SMTP, Exception, PHPMailer)
│   ├── actualizar-juego.php       # Edición de horas, estado y notas
│   ├── anadir-juego.php           # Inserción de nuevos títulos en la biblioteca
│   ├── buscar-igdb.php            # Conexión con API externa de videojuegos (IGDB)
│   ├── cambiar-estado-rapido.php  # Cambio rápido de estado desde las tarjetas
│   ├── conexion.php               # Configuración de conexión PDO a MySQL
│   ├── eliminar-juego.php         # Eliminación de títulos de la colección
│   ├── enviar-correo.php          # Plantillas y configuración SMTP de PHPMailer
│   ├── footer.php                 # Pie de página reutilizable
│   ├── header.php                 # Barra de navegación y controles de usuario
│   ├── login.php                  # Validación de credenciales y estado de verificación
│   ├── logout.php                 # Cierre de sesión seguro
│   ├── modal-actualizar-juego.php # Modal para editar progreso de un juego
│   ├── modal-anadir-juego.php     # Modal de búsqueda e inserción de juegos
│   ├── modal-eliminar-juego.php   # Modal de confirmación de borrado
│   ├── procesar-recuperacion.php  # Generación de token y envío de correo de recuperación
│   └── registro.php               # Alta de usuarios y generación de token de verificación
├── biblioteca.php                 # Panel principal de la colección del usuario
├── estadisticas.php               # Vista de métricas y gráficos de la biblioteca
├── index.php                      # Página de bienvenida, Login y Registro
├── README.md                      # Documentación del proyecto
├── restablecer.php                # Formulario de cambio de contraseña por token
└── verificar.php                  # Validación de cuenta mediante enlace de correo