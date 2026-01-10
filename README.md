# Local-Tabacchi

Gestione locale dei tabacchi - Laravel 12

## Requisiti

- PHP 8.3+
- Laravel 12.46+
- MySQL
- Composer
- Node.js + npm

## Installazione

```bash
git clone https://github.com/<username>/local-tabacchi.git
cd local-tabacchi
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
