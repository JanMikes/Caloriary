# Caloriary

REST API for the input of calories

[API Documentation](https://documenter.getpostman.com/view/1523209/RzffKqKp)

## 2-step installation

1. Run `cp docker-compose.dist.yml docker-compose.yml`
2. Run `docker-compose up` and enjoy (by default app will run on localhost:8080), *(optionally edit file `docker-compose.yml` for your needs)*

## Configuration
There are several ways how to customize application configuration.

1. Environment variables
2. Config file `config/config.local.neon`

### Environment variables
It is possible to use `.env` file to set environment variables (look at `.env.dist` as example). You can as well use environment variables in `docker-compose.yml`.

These environment variables are supported:
- `DATABASE_USER` (required)
- `DATABASE_PASSWORD` (required)
- `DATABASE_HOST` (required)
- `DATABASE_NAME` (required)
- `JWT_SECRET` (required)
- `NUTRITIONIX_APP_ID` (required)
- `NUTRITIONIX_API_KEY` (required)

### Config file `config/config.local.neon`
You can create this file and change anything. This is extremely useful for local development, etc disable logging.

## Code testing
- PhpStan static analysis: `vendor/bin/phpstan analyse src packages --level=max`
- PhpUnit Tests: `vendor/bin/phpunit tests` 
- Coding Standard: `vendor/bin/ecs check src packages`
