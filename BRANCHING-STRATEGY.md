# Estrategia de Branching

Este proyecto usa una estrategia de branching basada en GitFlow simplificado.

## 📋 Ramas Principales

### `main` (Producción)
- **Propósito:** Código en producción
- **Protegida:** Sí
- **Deploy automático:** Sí (Render.com)
- **CI/CD:** Pipeline completo (tests, code style, build)
- **Merge desde:** `development` via Pull Request

### `development` (Desarrollo)
- **Propósito:** Rama de integración para desarrollo
- **Protegida:** No
- **Deploy automático:** No
- **CI/CD:** Quick checks (tests básicos, Pint)
- **Trabajo diario:** Aquí haces commits directos

## 🔄 Workflow de Desarrollo

### 1️⃣ Desarrollo Diario (en `development`)

```bash
# Asegúrate de estar en development
git checkout development

# Trabaja normalmente
git add .
git commit -m "Add new feature"
git push origin development

# ✅ Solo ejecuta quick checks, NO el pipeline completo
```

### 2️⃣ Preparar Release (development → main)

Cuando estés listo para deploy a producción:

```bash
# 1. Asegúrate de que development esté actualizado
git checkout development
git pull origin development

# 2. Crea un Pull Request en GitHub:
# development → main

# 3. El PR ejecutará el pipeline completo automáticamente
# 4. Si pasa, haz merge del PR
# 5. Render.com desplegará automáticamente
```

### 3️⃣ Features Grandes (opcional)

Para features que toman varios días:

```bash
# Crea una rama de feature
git checkout development
git checkout -b feature/nueva-funcionalidad

# Trabaja en la feature
git add .
git commit -m "Work on feature"
git push origin feature/nueva-funcionalidad

# Cuando termines, merge a development
git checkout development
git merge feature/nueva-funcionalidad
git push origin development

# Luego sigue el paso 2 (PR a main)
```

## ⚙️ Configuración de CI/CD

### Pipeline en `main` (Completo - 3-5 min)

```yaml
✓ Tests en PHP 8.2 y 8.3
✓ Code style (Pint)
✓ Build frontend assets
✓ Deploy notification
```

**Se ejecuta:**
- ✅ Push a `main`
- ✅ Pull Request hacia `main`

### Quick Checks en `development` (Rápido - 1-2 min)

```yaml
✓ Tests (PHP 8.3 only)
✓ Code style check (Pint)
✓ Migrations check
```

**Se ejecuta:**
- ✅ Push a `development`
- ✅ Push a `feature/**`
- ✅ Push a `bugfix/**`

## 🚀 Proceso de Deploy

```
development (push) → Quick checks (1-2 min)
       ↓
   (crear PR)
       ↓
main (PR) → Pipeline completo (3-5 min)
       ↓
   (merge PR)
       ↓
    Render.com → Deploy automático (5-10 min)
```

## 📝 Convenciones de Commits

### En `development`:

```bash
# Features
git commit -m "Add user profile editing"
git commit -m "Implement search autocomplete"

# Fixes
git commit -m "Fix reply button not working"
git commit -m "Resolve dark mode toggle issue"

# Refactor
git commit -m "Refactor authentication logic"
git commit -m "Update API response format"

# Chores
git commit -m "Update dependencies"
git commit -m "Add missing tests"
```

### En PRs a `main`:

```bash
# Título del PR:
"Release v1.2.0 - User profiles and search"

# Descripción:
## Changes
- Added user profile editing
- Implemented search autocomplete
- Fixed 3 bugs

## Testing
- ✅ All tests passing
- ✅ Manually tested on staging

## Deploy notes
- No database migrations required
- No environment variables changed
```

## 🔒 Protección de Ramas

### Configurar en GitHub (Recomendado)

1. Ve a: Repositorio → Settings → Branches
2. Add rule para `main`:
   - ✅ Require pull request before merging
   - ✅ Require status checks to pass
   - ✅ Require branches to be up to date
   - ✅ Include administrators

## 🆘 Troubleshooting

### "Quiero revertir algo en main"

```bash
# Opción 1: Revert commit
git checkout main
git revert <commit-hash>
git push origin main

# Opción 2: Fix forward
git checkout development
# Fix the issue
git commit -m "Fix issue in production"
# Create PR to main
```

### "Olvidé trabajar en development"

```bash
# Si NO has hecho push a main:
git checkout main
git checkout -b development
git push origin development

# Si YA hiciste push a main:
# No hay problema, simplemente:
git checkout -b development
git push origin development
# Y trabaja en development de ahora en adelante
```

### "Pipeline falla en PR"

```bash
# 1. Revisa los logs del pipeline en GitHub
# 2. Fix en development:
git checkout development
# Fix issues
git commit -m "Fix CI issues"
git push origin development

# 3. El PR se actualizará automáticamente
```

## 📊 Comparación

| Acción | Rama | Pipeline | Tiempo | Deploy |
|--------|------|----------|--------|--------|
| Push código | `development` | Quick checks | 1-2 min | ❌ No |
| Push código | `feature/*` | Quick checks | 1-2 min | ❌ No |
| PR a main | `development` | Completo | 3-5 min | ❌ No |
| Merge a main | `main` | Completo | 3-5 min | ✅ Sí |

## 💡 Best Practices

1. **Trabaja en `development`** - Siempre
2. **Commits frecuentes** - Pequeños y descriptivos
3. **PRs cuando sea necesario** - No hay prisa
4. **Tests antes de PR** - Asegúrate de que pasen localmente
5. **Deploy controlado** - Solo via PR a main

## 🎯 Resumen Rápido

```bash
# Día a día:
git checkout development
git add .
git commit -m "Work in progress"
git push origin development
# ✅ Quick checks solo

# Cuando estés listo para producción:
# 1. Crear PR: development → main en GitHub
# 2. Esperar pipeline completo
# 3. Merge PR
# 4. Deploy automático
```

---

**¿Preguntas?** Revisa este documento o pregunta en el equipo.
