<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<h1 align="center">Artickets - Event Ticketing System</h1>
</p>

## Installation & Setup

Follow these steps to set up the Artickets project locally:

### Prerequisites
- PHP 8.1 or higher
- Composer 2.0 or higher
- Node.js 16.x or higher
- MySQL 5.7+ or MariaDB 10.3+
- Git

### 1. Clone the repository
```bash
git clone https://github.com/yourusername/artickets.git
cd artickets
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install JavaScript dependencies
```bash
npm install
```

### 4. Configure environment
Copy the example environment file and generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file to set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=artickets
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Database setup
Run migrations and seeders:
```bash
php artisan migrate --seed
```

### 6. Build assets
```bash
npm run build
```

### 7. Start the development server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

