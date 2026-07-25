# Zon Bumijaya - B2B E-commerce & Landing Page

This repository contains the complete codebase for the **Zon Bumijaya** project, consisting of a high-performance Next.js landing page integrated with a robust headless Laravel/Aimeos e-commerce backend.

## 🏗️ Project Architecture

The project is split into two main components:
1. **Frontend (`/`)**: A Next.js 15 application utilizing Tailwind CSS v4 and GSAP animations for a premium, dynamic landing page experience.
2. **Backend (`/zon-bumijaya-backend`)**: A headless Laravel 11/13 + Aimeos (2024.10) e-commerce engine running via Docker (Laravel Sail).

---

## 🚀 Getting Started for Teammates

### Prerequisites
- **Node.js** (v18 or higher)
- **Docker Desktop** (Required for the backend)
- **WSL2** (If running on Windows)

### 1. Frontend Setup (Next.js)
The frontend serves as the main landing page and communicates with the backend via JSON:API.

```bash
# Install dependencies
npm install

# Start the development server
npm run dev
```
The frontend will be available at `http://localhost:3000`.

### 2. Backend Setup (Laravel/Aimeos via Docker)
The backend requires Docker to run. We use Laravel Sail for container management.

```bash
cd zon-bumijaya-backend

# Install PHP dependencies using a temporary docker container
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs

# Start the Docker containers in the background
docker compose up -d

# Run database migrations and seed the Aimeos Demo data
docker compose exec -T laravel.test php artisan migrate
docker compose exec -T laravel.test php artisan aimeos:setup --option=setup/default/demo:1

# Compile and publish frontend assets for the Aimeos admin/storefront
docker compose exec -T laravel.test npm install
docker compose exec -T laravel.test npm run build
docker compose exec -T laravel.test php artisan vendor:publish --tag=public --force
```
The backend API and storefront will be available at `http://localhost:8000`.

### ⚠️ Important Notes & Troubleshooting
- **Database Port Conflicts (XAMPP):** If you have XAMPP or another local MySQL instance running, it will conflict with Docker on port `3306`. We have configured the `.env` file to use `FORWARD_DB_PORT=3307` to avoid this. Connect your database GUI to `localhost:3307` instead.
- **Docker WSL Issues:** If Docker fails with a `mkfs` or virtual disk error, ensure your Docker Desktop WSL integration is healthy and you have enough disk space.
- **Aimeos Assets:** If the `localhost:8000` page looks unstyled, ensure you ran the `npm run build` and `vendor:publish` commands listed above inside the container.
