# Guía de Deployment - Render.com

Esta guía explica cómo desplegar la aplicación Task Manager en Render.com usando su plan gratuito.

## 📋 Prerequisitos

- Cuenta en [Render.com](https://render.com) (gratuita)
- Cuenta en [GitHub](https://github.com)
- Repositorio de GitHub con el código de la aplicación

## 🚀 Deployment Automático con Blueprint

### Opción 1: Deploy con render.yaml (Recomendado)

1. **Conecta tu repositorio a Render**
   - Ve a [Render Dashboard](https://dashboard.render.com)
   - Click en "New +" → "Blueprint"
   - Conecta tu repositorio de GitHub
   - Selecciona el repositorio `task-manager`

2. **Render detectará automáticamente el archivo `render.yaml`**
   - Creará un Web Service (Laravel app)
   - Creará una base de datos PostgreSQL (1GB gratis)
   - Configurará todas las variables de entorno automáticamente

3. **Espera a que el deploy termine** (5-10 minutos primera vez)

4. **¡Listo!** Tu app estará disponible en `https://task-manager-xxxx.onrender.com`

### Opción 2: Deploy Manual

Si prefieres configurar manualmente:

#### Paso 1: Crear Base de Datos

1. En Render Dashboard, click "New +" → "PostgreSQL"
2. Configura:
   - **Name:** `task-manager-db`
   - **Database:** `task_manager`
   - **Region:** Oregon (más cercano)
   - **Plan:** Free
3. Click "Create Database"
4. Guarda las credenciales (las necesitarás después)

#### Paso 2: Crear Web Service

1. Click "New +" → "Web Service"
2. Conecta tu repositorio de GitHub
3. Configura:
   - **Name:** `task-manager`
   - **Region:** Oregon
   - **Branch:** `main`
   - **Runtime:** Docker
   - **Build Command:** `./render-build.sh`
   - **Start Command:** `php artisan serve --host=0.0.0.0 --port=$PORT`
   - **Plan:** Free

#### Paso 3: Configurar Variables de Entorno

En la sección "Environment" del Web Service, agrega:

```env
APP_NAME=TaskManager
APP_ENV=production
APP_DEBUG=false
APP_KEY=                          # Se genera automáticamente
APP_URL=https://tu-app.onrender.com

DB_CONNECTION=pgsql
DB_HOST=                          # Del PostgreSQL que creaste
DB_PORT=5432
DB_DATABASE=task_manager
DB_USERNAME=                      # Del PostgreSQL que creaste
DB_PASSWORD=                      # Del PostgreSQL que creaste

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
SCOUT_DRIVER=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@taskmanager.com
MAIL_FROM_NAME=TaskManager

# Flux UI Credentials (necesario para instalar dependencias)
FLUX_USERNAME=tu_email_flux
FLUX_LICENSE_KEY=tu_license_key_flux
```

4. Click "Create Web Service"

### 📝 Nota Importante sobre Flux UI

Este proyecto usa Flux UI. Para que el build funcione, necesitas:

1. Agregar tus credenciales de Flux en las variables de entorno de Render:
   - `FLUX_USERNAME`: Tu email de cuenta Flux
   - `FLUX_LICENSE_KEY`: Tu license key de Flux

2. También agregar estos secretos en GitHub para CI/CD:
   - Ve a tu repositorio → Settings → Secrets and variables → Actions
   - Agrega: `FLUX_USERNAME` y `FLUX_LICENSE_KEY`

## 🔄 CI/CD con GitHub Actions

El proyecto incluye un workflow de CI/CD que:

- ✅ Ejecuta todos los tests
- ✅ Verifica el código con Pint
- ✅ Compila assets de frontend
- ✅ Notifica cuando está listo para deploy

### Workflow Automático

1. **En Pull Requests:** Ejecuta tests y validaciones
2. **En Push a `main`:** Ejecuta tests + activa deploy automático en Render

Para ver el estado del workflow:
- Ve a tu repositorio en GitHub
- Click en la pestaña "Actions"
- Verás todos los workflows ejecutándose

## 📊 Monitoreo y Logs

### Ver Logs en Tiempo Real

1. Ve a tu Web Service en Render Dashboard
2. Click en la pestaña "Logs"
3. Los logs se actualizan automáticamente

### Métricas y Performance

1. Pestaña "Metrics" muestra:
   - CPU usage
   - Memory usage
   - Request count
   - Response times

## 🔧 Comandos Útiles

### Ejecutar Migraciones Manualmente

Si necesitas ejecutar migraciones después del deploy:

1. Ve a tu Web Service → Shell
2. Ejecuta:
   ```bash
   php artisan migrate --force
   ```

### Seed Database (Primera vez)

Para poblar la base de datos con datos de ejemplo:

1. Agrega variable de entorno: `SEED_DATABASE=true`
2. Re-deploya la aplicación
3. Después del deploy, remueve o cambia a `SEED_DATABASE=false`

### Limpiar Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Re-indexar Search

```bash
php artisan scout:import "App\Models\Discussion"
```

## ⚠️ Limitaciones del Plan Gratuito

- **Web Service:**
  - Se duerme después de 15 minutos de inactividad
  - Primera petición después de dormir toma ~30 segundos
  - 750 horas de compute gratis/mes

- **PostgreSQL:**
  - 1GB de almacenamiento
  - Expira después de 90 días (puedes crear nueva base de datos)
  - Backups no incluidos

- **Ancho de Banda:**
  - 100GB/mes gratis

## 🚨 Troubleshooting

### Error: "Application key not set"

```bash
# En Render Shell
php artisan key:generate --show
# Copia el key y agrégalo a las variables de entorno como APP_KEY
```

### Error: "No such file or directory (storage)"

```bash
# En Render Shell
php artisan storage:link
```

### Error de Migraciones

1. Verifica que la base de datos esté conectada
2. Checa las credenciales en las variables de entorno
3. Ejecuta migraciones manualmente desde Shell

### Build Falla

1. Revisa los logs del build en Render
2. Asegúrate de que `render-build.sh` tenga permisos de ejecución:
   ```bash
   chmod +x render-build.sh
   git add render-build.sh
   git commit -m "Make build script executable"
   git push
   ```

## 📧 Configurar Email (Opcional)

Para enviar emails reales en producción, puedes usar:

### Opción 1: Resend (Recomendado - Gratis)

1. Crea cuenta en [Resend.com](https://resend.com)
2. Genera API Key
3. Actualiza variables de entorno:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.resend.com
   MAIL_PORT=587
   MAIL_USERNAME=resend
   MAIL_PASSWORD=tu_api_key
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@tudominio.com
   ```

### Opción 2: Mailtrap (Para Testing)

1. Crea cuenta en [Mailtrap.io](https://mailtrap.io)
2. Usa las credenciales SMTP que te dan

## 🔐 Seguridad en Producción

- ✅ `APP_DEBUG=false` en producción
- ✅ `APP_ENV=production`
- ✅ Usa HTTPS (Render lo proporciona automáticamente)
- ✅ Variables sensibles en Environment Variables (nunca en código)
- ✅ Rate limiting configurado en rutas de autenticación

## 📱 Dominio Personalizado (Opcional)

1. Ve a tu Web Service → Settings → Custom Domains
2. Click "Add Custom Domain"
3. Ingresa tu dominio (ej: `forum.tudominio.com`)
4. Sigue las instrucciones para configurar DNS
5. Render proporciona SSL/HTTPS gratis con Let's Encrypt

## 🔄 Actualizar la Aplicación

Simplemente haz push a la rama `main`:

```bash
git add .
git commit -m "Update feature"
git push origin main
```

Render detectará el cambio y hará deploy automáticamente.

## 📞 Soporte

- [Documentación Render](https://render.com/docs)
- [Render Community](https://community.render.com)
- [Laravel Deployment Docs](https://laravel.com/docs/deployment)

