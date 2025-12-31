# 🚀 Guía Rápida de Deployment

## ⚠️ Estrategia de Branching

Este proyecto usa dos ramas:
- **`development`** - Trabajo diario (commits directos, quick checks)
- **`main`** - Producción (solo via PR, pipeline completo, deploy automático)

**Ver [BRANCHING-STRATEGY.md](BRANCHING-STRATEGY.md) para detalles completos.**

## Pasos para Deploy en Render.com (5 minutos)

### 1️⃣ Preparar Repositorio GitHub

```bash
# Trabaja en development normalmente
git checkout development
git add .
git commit -m "Add deployment configuration"
git push origin development

# Cuando estés listo para producción, crea un PR:
# development → main en GitHub
# Merge el PR después de que pasen los tests
```

### 2️⃣ Deploy en Render.com

**Opción A: Con Blueprint (Automático - Recomendado)**
1. Ve a [Render Dashboard](https://dashboard.render.com)
2. Click "New +" → "Blueprint"
3. Conecta tu repositorio de GitHub
4. Selecciona el repositorio
5. Render detectará `render.yaml` y creará todo automáticamente
6. Agrega la variable de entorno:
   - `APP_URL` (después de que se cree el servicio)
7. ✅ Listo!

**Opción B: Manual**
Ver [DEPLOYMENT.md](DEPLOYMENT.md) para instrucciones detalladas.

### 4️⃣ Verificar Deployment

1. Espera 5-10 minutos (primera vez)
2. Visita la URL proporcionada por Render: `https://task-manager-xxxx.onrender.com`
3. Verifica que la aplicación carga correctamente

### 5️⃣ Seed Database (Opcional - Primera Vez)

Si quieres datos de ejemplo:

1. En Render Dashboard, ve a tu Web Service → Environment
2. Agrega: `SEED_DATABASE=true`
3. Re-deploya (Manual Deploy → Deploy latest commit)
4. Después del deploy exitoso, cambia `SEED_DATABASE=false`

---

## 📊 Monitoreo Post-Deploy

- **Logs:** Render Dashboard → Tu servicio → Logs
- **Metrics:** Render Dashboard → Tu servicio → Metrics
- **Shell:** Render Dashboard → Tu servicio → Shell (para ejecutar comandos)

## ⚡ Actualizaciones Futuras

Simplemente haz push a `main`:

```bash
git add .
git commit -m "Nueva feature"
git push origin main
```

GitHub Actions ejecutará tests automáticamente, y si pasan, Render desplegará automáticamente.

---

## 🆘 Solución Rápida de Problemas

**Error: "Application key not set"**
```bash
# En Render Shell
php artisan key:generate --show
# Agrega el key a variables de entorno como APP_KEY
```

**Build falla**
- Revisa logs en Render
- Verifica que todas las variables de entorno estén configuradas
- Asegúrate de que GitHub Actions pase (verde ✓)

---

## 📚 Documentación Completa

Ver [DEPLOYMENT.md](DEPLOYMENT.md) para:
- Instrucciones detalladas paso a paso
- Configuración de email
- Dominio personalizado
- Limitaciones del plan gratuito
- Troubleshooting avanzado

---

**⏱️ Tiempo estimado total: 5-10 minutos**

**💰 Costo: $0 (Plan gratuito de Render)**
