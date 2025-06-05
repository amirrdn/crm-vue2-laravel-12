# Mini CRM with Mock VoIP

A simple CRM application with VoIP simulation using Vue 2 and Laravel 12.

## Preview

[Watch Demo Video](https://go.screenpal.com/watch/cT1hizn6SmC)

## Main Features

- Contact management with filtering by company and role
- VoIP call simulation
- Call history logging
- SPA interface with Vue 2
- Laravel 12 API
- Dockerized setup

## System Requirements

- Docker
- Docker Compose
- Git

## Installation

1. Clone the repository:
```bash
git clone git@github.com:amirrdn/crm-vue2-laravel-12.git
cd crmvue
```

2. Run Docker Compose:
```bash
docker-compose up -d
```

3. Install Laravel dependencies:
```bash
docker-compose exec backend composer install
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate
```

4. Install Vue dependencies:
```bash
docker-compose exec frontend npm install
```

5. Build frontend:
```bash
docker-compose exec frontend npm run build
```

## Access the Application

- Frontend: http://localhost:8081
- Backend API: http://localhost:8000

## Project Structure

- `/frontend` - Vue 2 application
- `/backend` - Laravel 12 API
- `docker-compose.yml` - Docker configuration

## Assumptions

- Using Vue 2 for frontend
- Using Laravel 12 for backend
- MySQL database
- Authentication
- VoIP simulation without external API integration 