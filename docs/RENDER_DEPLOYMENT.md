# Guía de Deployment en Render

Esta guía te ayudará a desplegar tu aplicación Laravel Forum en Render.

## Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Paso 1: Preparar el Repositorio](#paso-1-preparar-el-repositorio)
- [Paso 2: Crear Servicios en Render](#paso-2-crear-servicios-en-render)
- [Paso 3: Configurar Variables de Entorno](#paso-3-configurar-variables-de-entorno)
- [Paso 4: Configurar Storage](#paso-4-configurar-storage)
- [Paso 5: Deploy](#paso-5-deploy)
- [Troubleshooting](#troubleshooting)

## Requisitos Previos

- Cuenta en [Render](https://render.com)
- Repositorio Git (GitHub, GitLab, o Bitbucket)
- Cuenta en Cloudinary o AWS S3 (para almacenamiento de imágenes)
- Cuenta en Mailtrap o servicio SMTP (para emails)

## Paso 1: Preparar el Repositorio

### 1.1 Asegurar que estos archivos estén en el repo:

```bash
# Verificar archivos necesarios
ls -la render.yaml
ls -la build.sh
ls -la Dockerfile
```

### 1.2 Hacer commit y push de todos los cambios:

```bash
git add .
git commit -m "Prepare for Render deployment"
git push origin main
```

## Paso 2: Crear Servicios en Render

### 2.1 Crear PostgreSQL Database

1. Ve a [Render Dashboard](https://dashboard.render.com/)
2. Click en **"New +"** → **"PostgreSQL"**
3. Configuración:
   - **Name:** `laravel-forum-db`
   - **Database:** `laravel_forum`
   - **User:** `laravel_forum_user`
   - **Region:** Selecciona la más cercana a tus usuarios
   - **Plan:** Free (para pruebas) o Starter ($7/mes)
4. Click **"Create Database"**
5. **GUARDA** las credenciales (Internal Database URL)

### 2.2 Crear Web Service

#### Opción A: Usando Blueprint (render.yaml)

1. En Render Dashboard, click **"New +"** → **"Blueprint"**
2. Conecta tu repositorio Git
3. Selecciona el repositorio `task-manager`
4. Render detectará automáticamente el archivo `render.yaml`
5. Click **"Apply"**

#### Opción B: Manual

1. Click **"New +"** → **"Web Service"**
2. Conecta tu repositorio Git
3. Configuración:
   - **Name:** `laravel-forum`
   - **Region:** Misma que la base de datos
   - **Branch:** `main`
   - **Runtime:** `Docker`
   - **Plan:** Free (para pruebas) o Starter ($7/mes)
4. Click **"Create Web Service"**

## Paso 3: Configurar Variables de Entorno

En tu Web Service, ve a **Environment** y agrega:

### Variables Obligatorias

```bash
# Application
APP_NAME="Laravel Forum"
APP_ENV=production
APP_KEY=                          # Se generará automáticamente
APP_DEBUG=false
APP_URL=https://tu-app.onrender.com

# Database (obtenido del servicio PostgreSQL de Render)
DB_CONNECTION=pgsql
DB_HOST=                          # Desde Internal Database URL
DB_PORT=5432
DB_DATABASE=laravel_forum
DB_USERNAME=                      # Desde credenciales de DB
DB_PASSWORD=                      # Desde credenciales de DB

# Queue
QUEUE_CONNECTION=database

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database

# Log
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Variables de Correo (Mailtrap para desarrollo)

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=                    # Tu username de Mailtrap
MAIL_PASSWORD=                    # Tu password de Mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@laravel-forum.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Variables de Correo (Producción con Gmail/SendGrid/etc)

```bash
# Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=                    # App Password de Gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# SendGrid
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=                    # Tu API Key de SendGrid
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Variables de Storage de Imágenes

#### Opción A: Cloudinary (Recomendado para Render)

```bash
IMAGES_DISK=cloudinary
CLOUDINARY_CLOUD_NAME=            # Tu cloud name
CLOUDINARY_API_KEY=               # Tu API key
CLOUDINARY_API_SECRET=            # Tu API secret
```

**Obtener credenciales de Cloudinary:**
1. Regístrate en [Cloudinary](https://cloudinary.com)
2. Ve a Dashboard → Account Details
3. Copia Cloud Name, API Key, API Secret

#### Opción B: AWS S3

```bash
IMAGES_DISK=s3
AWS_ACCESS_KEY_ID=                # Tu access key
AWS_SECRET_ACCESS_KEY=            # Tu secret key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=laravel-forum-images
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Variables de Scout (Búsqueda)

```bash
SCOUT_DRIVER=database
# O si usas Algolia/Meilisearch:
# SCOUT_DRIVER=algolia
# ALGOLIA_APP_ID=
# ALGOLIA_SECRET=
```

## Paso 4: Configurar el Build

### 4.1 Verificar Dockerfile

El archivo `Dockerfile` ya está configurado. Asegúrate de que existe en la raíz del proyecto.

### 4.2 Verificar Build Script

El archivo `build.sh` contiene los comandos de build. Verifica que existe y tiene permisos de ejecución:

```bash
chmod +x build.sh
git add build.sh
git commit -m "Make build.sh executable"
git push
```

## Paso 5: Deploy

### 5.1 Primer Deploy

1. En el Web Service de Render, verás que el deploy inicia automáticamente
2. Monitorea los logs en la pestaña **"Logs"**
3. El proceso tomará 5-10 minutos la primera vez

### 5.2 Verificar el Deploy

Una vez completado:

1. Ve a la URL de tu app: `https://tu-app.onrender.com`
2. Deberías ver la página de inicio del foro
3. Prueba registrar un usuario
4. Prueba crear una discusión
5. Prueba subir imágenes

### 5.3 Ejecutar Comandos (si es necesario)

Si necesitas ejecutar comandos Artisan:

```bash
# Desde Render Shell
php artisan migrate --force
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize
```

Para acceder al Shell:
1. Ve a tu Web Service en Render
2. Click en **"Shell"** (icono de terminal)
3. Ejecuta los comandos necesarios

## Paso 6: Configurar Worker para Queues (Opcional)

Si quieres procesar jobs en cola:

### 6.1 Crear Background Worker

1. En Render Dashboard, click **"New +"** → **"Background Worker"**
2. Configuración:
   - **Name:** `laravel-forum-worker`
   - **Runtime:** `Docker`
   - **Start Command:** `php artisan queue:work --verbose --tries=3 --timeout=90`
3. Usa las **mismas variables de entorno** que el Web Service
4. Click **"Create Background Worker"**

## Troubleshooting

### Error: "Application key not set"

```bash
# En Render Shell
php artisan key:generate --show
```

Copia la key generada y agrégala en Environment Variables:
```
APP_KEY=base64:...
```

### Error: "Database connection failed"

1. Verifica que las credenciales de DB sean correctas
2. Asegúrate de usar el **Internal Database URL** (no el External)
3. El formato debe ser:
   ```
   DB_HOST=dpg-xxxxx-a
   DB_PORT=5432
   DB_DATABASE=laravel_forum
   DB_USERNAME=laravel_forum_user
   DB_PASSWORD=xxxxx
   ```

### Error: "Storage disk not configured"

1. Verifica que `IMAGES_DISK` esté configurado
2. Si usas Cloudinary, verifica las credenciales
3. Si usas S3, verifica las credenciales y permisos del bucket

### Error: "CSRF token mismatch"

1. Asegúrate de que `APP_URL` coincida con tu dominio de Render
2. Verifica que `SESSION_DRIVER=database`
3. Limpia caché:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### Logs no aparecen o app muy lenta

1. Verifica que `LOG_CHANNEL=stack`
2. Cambia `LOG_LEVEL=debug` temporalmente
3. Revisa los logs en Render Dashboard → Logs
4. Para mejor rendimiento, considera el plan Starter ($7/mes)

### Imágenes no se suben

1. Verifica configuración de Cloudinary/S3
2. Verifica que el paquete esté instalado:
   ```bash
   # Para Cloudinary
   composer require cloudinary-labs/cloudinary-laravel

   # Para S3
   composer require league/flysystem-aws-s3-v3
   ```
3. Verifica permisos en S3 bucket (si usas S3)

### Assets CSS/JS no cargan

1. Verifica que `npm run build` se ejecutó en el build
2. Revisa los logs de build
3. Asegúrate de que Vite está configurado correctamente en `vite.config.js`

### Free Tier se queda dormido

El plan Free de Render duerme después de 15 minutos de inactividad y tarda ~30 segundos en despertar. Opciones:

1. **Upgrade a Starter** ($7/mes) - siempre activo
2. **Usar servicio de ping** gratuito:
   - [UptimeRobot](https://uptimerobot.com) - ping cada 5 minutos
   - [Cron-Job.org](https://cron-job.org) - ping programado

## Post-Deployment

### Configurar Dominio Personalizado (Opcional)

1. En Render Web Service, ve a **"Settings"** → **"Custom Domain"**
2. Agrega tu dominio: `www.tudominio.com`
3. Configura los DNS records en tu proveedor de dominio
4. Render provee SSL automáticamente con Let's Encrypt

### Monitoring

Render incluye:
- **Logs** en tiempo real
- **Metrics** (CPU, memoria, requests)
- **Alerts** por email

### Backups de Base de Datos

1. Ve a tu PostgreSQL service en Render
2. Activa **"Point in Time Recovery"** (planes pagos)
3. O configura backups manuales:
   ```bash
   # Desde Shell
   pg_dump $DATABASE_URL > backup.sql
   ```

### Actualizar la Aplicación

Simplemente haz push a tu repositorio:

```bash
git add .
git commit -m "Update feature"
git push origin main
```

Render detectará el cambio y desplegará automáticamente.

## Costos Estimados

### Plan Free
- **Web Service:** Free (con limitaciones)
- **PostgreSQL:** Free (hasta 256MB)
- **Total:** $0/mes
- **Limitaciones:**
  - Se duerme después de 15 min sin uso
  - 750 horas/mes
  - CPU/RAM limitado

### Plan Básico (Recomendado)
- **Web Service Starter:** $7/mes
- **PostgreSQL Starter:** $7/mes
- **Cloudinary Free:** $0/mes (hasta 25GB)
- **Total:** ~$14/mes
- **Beneficios:**
  - Siempre activo
  - Mejor rendimiento
  - 7-day Point in Time Recovery

## Recursos Adicionales

- [Render Docs](https://render.com/docs)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Cloudinary Laravel](https://cloudinary.com/documentation/laravel_integration)
- [Render Community](https://community.render.com/)

## Soporte

Si tienes problemas:
1. Revisa los logs en Render Dashboard
2. Revisa esta guía de troubleshooting
3. Consulta [Render Community](https://community.render.com/)
4. Contacta soporte de Render (planes pagos)

---

**¡Felicidades! Tu aplicación Laravel Forum está en producción!** 🎉
