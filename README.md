# Water

Water is a Laravel-based project built using the official Laravel Livewire starter kit. It leverages modern PHP and frontend technologies to provide a robust foundation for building interactive web applications.

This project has been a work-in-progress for three years being built to support a small rural community water system. It goal is to provide customer, billing, and communication as well as facility monitoring from remote sensors.

## Functional requirements:
- **Facility monitoring** - using custom Arduino and Raspberry Pi sensors for water levels, pump current, and ambient temperature.
- **Customer management** - Maintain customer mailing address and preferred contact information as well as front ending for Stripe payement system.
- **Billing** - Integrate with Stripe for payment processing.
- **Communication** - Send and receive SMS and email notifications to customers and volunteers/staff using Twilio services.


## Stack

- **Framework:** [Laravel 13](https://laravel.com)
- **Frontend:** [Livewire 4](https://livewire.laravel.com), [Filament 5](https://filamentphp.com), [Tailwind CSS 4](https://tailwindcss.com)
- **Language:** PHP 8.3+
- **Build Tool:** [Vite](https://vitejs.dev)
- **Testing:** [Pest](https://pestphp.com)
- **Database:** SQLite (Default)

## Requirements

- PHP ^8.3
- Composer
- Node.js & npm
- SQLite (or another supported database)

## Setup

Follow these steps to get the project running locally:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd water
   ```

2. **Run the setup script:**
   The project includes a comprehensive setup script that installs dependencies, prepares the environment, and runs migrations.
   ```bash
   composer run setup
   ```
   *Note: This will copy `.env.example` to `.env` if it doesn't exist, generate an app key, and run migrations.*

## Running the Application

To start the development server and asset bundling:

```bash
npm run dev
```

Alternatively, you can run the Laravel development server separately:

```bash
php artisan serve
```

## Available Scripts

### Composer Scripts

- `composer run setup`: Full project initialization.
- `composer run dev`: Starts the development environment.
- `composer run test`: Runs the full test suite (linting, type checking, and tests).
- `composer run lint`: Runs Laravel Pint to fix styling issues.
- `composer run lint:check`: Checks for styling issues without fixing them.
- `composer run types:check`: Runs PHPStan for static analysis.
- `composer run ci:check`: Runs tests in a CI-like environment.

### NPM Scripts

- `npm run dev`: Starts the Vite development server.
- `npm run build`: Builds assets for production.

## Environment Variables

Key environment variables in `.env`:

- `APP_NAME`: The name of the application.
- `APP_ENV`: Application environment (local, production, etc.).
- `APP_KEY`: Application encryption key.
- `DB_CONNECTION`: Database driver (default: `sqlite`).
- `DB_DATABASE`: Path to the database file for SQLite.

Refer to `.env.example` for a full list of available variables.

## Testing

The project uses Pest for testing. You can run the tests using:

```bash
composer run test
```

Or run Pest directly:

```bash
php artisan test
```

## Project Structure

- `app/`: Core PHP logic (Models, Controllers, Providers).
- `config/`: Application configuration files.
- `database/`: Migrations, factories, and seeders.
- `public/`: Publicly accessible assets.
- `resources/`: Frontend assets (Views, CSS, JS).
- `routes/`: Application route definitions.
- `tests/`: Automated tests.
- `vite.config.js`: Vite configuration.

## TODO

- [ ] Define the primary purpose of the application in the Overview.
- [ ] Add specific deployment instructions.
- [ ] Document custom Filament resources and Livewire components once implemented.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
