## Setup

Follow these steps to get the application running locally.

### 1. Clone the repository

```bash
git clone git@github.com:glennbaquero/latus-exam.git
cd latus-exam
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure environment

Copy the example `.env` file and update it with your environment-specific settings:

```bash
cp .env.example .env
```

Update your database credentials and other required settings in `.env`.

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Run database migrations

```bash
php artisan migrate
```

### 6. Seed the database

```bash
php artisan db:seed
```

### 7. Link Storage

```bash
php artisan storage:link
```

### 8. Install Node.js dependencies

```bash
npm install --legacy-peer-deps
```

### 9. Compile front-end assets (Don't stop/close the process)

```bash
npm run dev
```

### 10. Start the development server in new terminal

```bash
php artisan serve
```

You can now access the app at [http://localhost:8000/login](http://localhost:8000/login)

**Credentials:**

* Email: [test@example.com](mailto:test@example.com)
* Password: password

### 11. Run Tests

For running the tests, use the following commands:

```bash
php artisan test --filter=JokeApiServiceTest
php artisan test --filter=JokeControllerTest
```
