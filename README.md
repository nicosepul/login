🚀 Login App - Laravel + Vue

📌 Descripción

Este proyecto es un sistema de autenticación completo utilizando Laravel y Vue.js.

Incluye:

Registro de usuario con información adicional:
Dirección
Género
Fecha de nacimiento (calcula automáticamente la edad)
Nacionalidad (obtenida de API Countries
)
Login seguro con hash bcrypt
Validaciones en frontend y backend
Redirección a dashboard tras login
🎨 Capturas
Login

Registro

⚙️ Tecnologías
Backend: Laravel 10
Frontend: Vue.js 3
Base de datos: MySQL / MariaDB
Autenticación: Bcrypt
Gestión de dependencias: Composer / NPM
API externa: API Countries
🛠️ Instalación
# Clonar el repositorio
git clone https://github.com/nicosepul/login.git
cd login

# Instalar dependencias PHP
composer install

# Instalar dependencias Node
npm install

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Compilar assets
npm run dev
🚀 Uso
Accede a /login para iniciar sesión
Haz clic en Registrar para ir al formulario de registro
Después de registrarte, serás redirigido al dashboard principal (/)
🗂️ Estructura del proyecto
app/Http/Controllers       # Controladores Laravel
resources/js/components    # Componentes Vue
resources/views             # Vistas Blade
routes/web.php             # Rutas web
database/migrations        # Migraciones de base de datos
🔐 Buenas prácticas
Nunca subir node_modules ni .env a GitHub
Guardar contraseñas en hash con bcrypt
Validaciones en frontend y backend
Guardar solo el numericCode de la nacionalidad de la API
🤝 Contribución
Haz un fork del repo
Crea tu branch: git checkout -b mi-feature
Haz commit de tus cambios: git commit -am 'Agrega nueva feature'
Push a tu branch: git push origin mi-feature
Abre un Pull Request
📜 Licencia

MIT License