#!/bin/sh
set -eu

# Render supplies PORT. Use the same default for local Docker runs.
: "${PORT:=10000}"
case "$PORT" in
    ''|*[!0-9]*) printf '%s\n' 'PORT must be an integer from 1 to 65535.' >&2; exit 1 ;;
esac
if [ "${#PORT}" -gt 5 ] || [ "$PORT" -lt 1 ] || [ "$PORT" -gt 65535 ]; then
    printf '%s\n' 'PORT must be an integer from 1 to 65535.' >&2
    exit 1
fi
export PORT

# FormSubmit needs the public Referer. No localhost URL is baked into the image.
# An explicit value supports a custom domain; otherwise Render provides its URL.
if [ -z "${PORTFOLIO_SITE_URL:-}" ] && [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    PORTFOLIO_SITE_URL="${RENDER_EXTERNAL_URL%/}/"
    export PORTFOLIO_SITE_URL
fi

printf 'Listen 0.0.0.0:%s\n' "$PORT" > /etc/apache2/ports.conf
apache2ctl -t
exec apache2-foreground
