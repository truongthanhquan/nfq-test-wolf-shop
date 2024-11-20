# Wolf Shop

## Prerequisites

Ensure you have the following installed on your machine before proceeding:

- [Docker](https://www.docker.com/products/docker-desktop/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

## Setup Instructions

Follow these steps to set up and run the project:

1. **Clone the repository**
   ```bash
   git clone git@github.com:truongthanhquan/nfq-test-wolf-shop.git
   cd nfq-test-wolf-shop
   ```
2. Build the Docker containers
   ```bash
   docker compose build
   ```
3. Start the Docker containers
   ```bash
   docker compose up -d
   ```
4. Install dependencies inside the PHP container
   To install the necessary PHP dependencies, run composer install inside the PHP container
   ```bash
   docker-compose exec php bash -c "composer install"
   ```
5. Run database migrations
   To apply the database migrations, run the following command inside the PHP container
   ```bash
   docker-compose exec php bash -c "php bin/console doctrine:migrations:migrate"
   ```
6. Load fixtures
   Load the fixtures into the database by running
   ```bash
   docker-compose exec php bash -c "php bin/console doctrine:fixtures:load"
   ```
7. Running PHPUnit tests
   To run the PHPUnit tests, execute the following command inside the PHP container
   ```bash
   docker-compose exec php bash -c "php bin/phpunit"
   ```
8. Import data from an API
   To import data from an external API, you can run the following Symfony command
   ```bash
   docker-compose exec php bash -c "php bin/console import:item:api"
   ```
   This command will trigger the import of data from the API and load it into your Symfony application.

## Postman Collections
The project includes a Postman collection for testing the API endpoints, located in the file `NFQ-test-wolf-shop.postman_collection.json`

## Cloudinary Configuration
To use Cloudinary with the Symfony project, you need to set the following environment variables:

* `CLOUDINARY_CLOUD_NAME`
* `CLOUDINARY_API_KEY`
* `CLOUDINARY_API_SECRET`
These environment variables can be set in several places:
1. In the .env file (for default environment):
   ```dotenv
   # .env
   APP_ENV=dev
   APP_SECRET=your_secret_key
   DATABASE_URL=mysql://db_user:db_password@mysql:3306/db_name
   
   # Cloudinary Environment Variables
   CLOUDINARY_CLOUD_NAME=your_cloudinary_cloud_name
   CLOUDINARY_API_KEY=your_cloudinary_api_key
   CLOUDINARY_API_SECRET=your_cloudinary_api_secret
   ```
2. In the .env.local file (for local overrides)
   ```dotenv
   # .env.local
   DATABASE_URL=mysql://root:rootpassword@mysql:3306/symfony_local
   
   # Cloudinary Environment Variables for Local Development
   CLOUDINARY_CLOUD_NAME=your_local_cloudinary_cloud_name
   CLOUDINARY_API_KEY=your_local_cloudinary_api_key
   CLOUDINARY_API_SECRET=your_local_cloudinary_api_secret
   ```
   Make sure to replace `your_cloudinary_*` placeholders with your actual Cloudinary credentials.

## Stopping the containers
When you're done, you can stop the Docker containers
   ```bash
   docker-compose down
   ```

## Notes
* The PHP container is the primary container for interacting with the Symfony project, running migrations, loading fixtures, and running tests.
* Make sure your `.env` or `.env.local` file is properly configured for the local development environment (e.g., database connection settings).
* Cloudinary credentials are needed for media handling and image uploads in your Symfony project. These credentials should be configured as shown above.

