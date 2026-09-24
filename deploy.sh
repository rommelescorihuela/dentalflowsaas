#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Despliegue automático de DentalFlow (hosting compartido)
# -----------------------------------------------------------------------------
# Ejecuta: git pull + composer install + migrate + assets + cachés, y deja un
# registro detallado (incluye errores) en storage/logs/deploy.log
#
# Uso manual:
#   ./deploy.sh
#
# Uso por cron (cada 5 minutos, sin salida por correo):
#   */5 * * * * /home/USUARIO/dentalflowsaas/deploy.sh >/dev/null 2>&1
#
# Ver el resultado / errores:
#   tail -n 100 storage/logs/deploy.log
#   tail -f storage/logs/deploy.log
#
# Variables opcionales (export antes de ejecutar o edítalas aquí):
#   DEPLOY_BRANCH   rama a desplegar            (por defecto: main)
#   DEPLOY_REMOTE   remoto git                  (por defecto: origin)
#   PHP_BIN         binario de PHP              (por defecto: php)
#   COMPOSER_BIN    binario de composer         (por defecto: composer)
#   DEPLOY_LOG      ruta del log                (por defecto: storage/logs/deploy.log)
#   DEPLOY_RUN_NPM  1 = forzar build de assets, 0 = nunca (por defecto: auto)
# =============================================================================

set -uo pipefail

# PATH ampliado para que funcione bajo cron (que no hereda el entorno del shell)
export PATH="/usr/local/bin:/usr/bin:/bin:/usr/local/sbin:/usr/sbin:/sbin:${PATH:-}"

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR" || { echo "No se pudo entrar a $APP_DIR"; exit 1; }

BRANCH="${DEPLOY_BRANCH:-main}"
REMOTE="${DEPLOY_REMOTE:-origin}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
LOG_FILE="${DEPLOY_LOG:-$APP_DIR/storage/logs/deploy.log}"
LOCK_FILE="$APP_DIR/storage/framework/deploy.lock"
RUN_NPM="${DEPLOY_RUN_NPM:-auto}"

mkdir -p "$(dirname "$LOG_FILE")" "$(dirname "$LOCK_FILE")" 2>/dev/null || true

ts()  { date '+%Y-%m-%d %H:%M:%S'; }
log() { printf '[%s] %s\n' "$(ts)" "$*" | tee -a "$LOG_FILE"; }

# ---------------------------------------------------------------------------
# Lock: evita que dos ejecuciones (cron) corran a la vez
# ---------------------------------------------------------------------------
exec 9>"$LOCK_FILE" 2>/dev/null || true
if command -v flock >/dev/null 2>&1; then
    if ! flock -n 9; then
        log "WARN  Ya hay un despliegue en curso; se omite esta ejecución."
        exit 0
    fi
fi

fail() { log "ERROR $*"; log "==== Deploy FALLÓ · $(ts) ===="; exit 1; }

run() {  # paso crítico: si falla, aborta
    local name="$1"; shift
    log ">> $name"
    if "$@" >>"$LOG_FILE" 2>&1; then
        log "OK   $name"
    else
        local code=$?
        fail "$name (exit $code). Revisa $LOG_FILE"
    fi
}

run_soft() {  # paso opcional: si falla, solo advierte
    local name="$1"; shift
    log ">> $name"
    if "$@" >>"$LOG_FILE" 2>&1; then
        log "OK   $name"
    else
        log "WARN $name falló (se continúa)"
    fi
}

# Modo simulación: DEPLOY_DRY_RUN=1 → registra los pasos sin ejecutarlos
if [ "${DEPLOY_DRY_RUN:-0}" = "1" ]; then
    run()      { local name="$1"; shift; log "DRY  $name :: $*"; }
    run_soft() { local name="$1"; shift; log "DRY  $name :: $*"; }
fi

# ---------------------------------------------------------------------------
START=$(date +%s)
log "=============================================================================="
log "==== Deploy iniciado · rama=$BRANCH · remoto=$REMOTE · $(ts) ===="

command -v git >/dev/null 2>&1       || fail "git no está disponible en PATH"
command -v "$PHP_BIN" >/dev/null 2>&1 || fail "PHP ('$PHP_BIN') no está disponible"

# 1) Traer el código
run "git fetch"  git fetch --prune "$REMOTE" "$BRANCH"
run "git reset"  git reset --hard "$REMOTE/$BRANCH"
log "OK   commit actual: $(git log --oneline -1 2>/dev/null)"

# 2) Dependencias PHP
if command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
    export COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_NO_INTERACTION=1
    run "composer install" "$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction
else
    fail "composer no está disponible. Instálalo o define COMPOSER_BIN"
fi

# 3) Base de datos
run "migrate" "$PHP_BIN" artisan migrate --force

# 4) Assets de frontend (public/build no viene por git)
if [ ! -f public/build/manifest.json ]; then
    if [ "$RUN_NPM" != "0" ] && command -v npm >/dev/null 2>&1; then
        run "npm ci"    npm ci --no-audit --no-fund
        run "npm build" npm run build
    else
        log "WARN Falta public/build y no hay npm disponible."
        log "WARN Sube los assets compilados (public/build) o habilita Node.js en el hosting."
    fi
else
    log "OK   public/build presente"
fi

# 5) Enlaces y assets de Filament
run_soft "storage:link"    "$PHP_BIN" artisan storage:link
run_soft "filament:assets" "$PHP_BIN" artisan filament:assets

# 6) Cachés de Laravel
run "optimize:clear" "$PHP_BIN" artisan optimize:clear
if [ "${DEPLOY_DRY_RUN:-0}" = "1" ]; then
    log "DRY  optimize :: $PHP_BIN artisan optimize"
elif ! "$PHP_BIN" artisan optimize >>"$LOG_FILE" 2>&1; then
    log "WARN 'optimize' falló → se dejan las cachés limpias"
    "$PHP_BIN" artisan optimize:clear >>"$LOG_FILE" 2>&1 || true
else
    log "OK   optimize"
fi

END=$(date +%s)
log "==== Deploy OK · $((END-START))s · $(ts) ===="
exit 0
