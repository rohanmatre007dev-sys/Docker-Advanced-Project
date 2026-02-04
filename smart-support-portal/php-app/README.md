# PHP App - smart-support-portal/php-app

Small PHP-FPM application (no composer required).

Env vars:

- DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD
- REDIS_HOST (optional), REDIS_PORT (default 6379)
- ANALYZER_HOST, ANALYZER_PORT (for calling analyzer service)

Public dir: `public/index.php`
DB schema: `db/schema.sql`
