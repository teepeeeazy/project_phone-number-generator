# Phone Number Generator and Validator

This repository contains a PHP-based application that generates random phone numbers, validates them using a microservice, and stores valid numbers in a MongoDB database.

## Prerequisites

Before running the application, ensure you have the following installed:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## Getting Started

Follow these steps to set up and run the application:

### 1. Clone the Repository

```bash
git clone <repository-url>
cd project_phone-number-generator
```

### 2. Build and Start the Docker Containers

Run the following command to build and start the application:

```bash
docker-compose up --build
```

This will:
- Start a MongoDB container.
- Start the `service` container (Lumen microservice).
- Start the `app` container (PHP web application).

### 3. Access the Application

Once the containers are running, you can access the application in your browser:

- **Frontend Application**: [http://localhost:8080](http://localhost:8080)
- **Microservice**: [http://localhost:9000](http://localhost:9000)

### 4. Generate Phone Numbers

1. Open the application in your browser at [http://localhost:8080](http://localhost:8080).
2. Enter the desired quantity of phone numbers and the country code.
3. Click the "Generate" button to generate and validate phone numbers.

### 5. View Validation Results

After submitting the form, the application will display:
- The number of valid phone numbers.
- The percentage of valid phone numbers.
- Detailed results for each phone number.

## Project Structure

- **`app/`**: Contains the frontend application.
- **`service/`**: Contains the Lumen microservice for phone number validation.
- **`Dockerfile`**: Docker configuration for the frontend application.
- **`Dockerfile.service`**: Docker configuration for the microservice.
- **`docker-compose.yml`**: Docker Compose configuration to orchestrate the services.
- **`access.conf`**: Custom Apache configuration for the frontend.

## Environment Variables

The application uses the following environment variables:

- **Frontend (`app/.env`)**:
  - `MICROSERVICE_URL`: URL of the microservice (default: `http://localhost:9000/validate`).

- **Microservice (`service/.env`)**:
  - `APP_ENV`: Application environment (e.g., `local`, `production`).
  - `DB_CONNECTION`: Database connection type (e.g., `mongodb`).
  - `DB_HOST`: MongoDB host.
  - `DB_PORT`: MongoDB port.
  - `DB_DATABASE`: MongoDB database name.

## Running Tests

To run tests for the microservice, execute the following commands:

1. Enter the `service` container:
   ```bash
   docker exec -it <service-container-name> bash
   ```
2. Run PHPUnit tests:
   ```bash
   ./vendor/bin/phpunit
   ```

## Troubleshooting

- **Port Conflicts**: Ensure ports `8080`, `9000`, and `27017` are not in use by other applications.
- **Container Name Conflicts**: If you encounter a container name conflict, remove the existing container:
  ```bash
  docker rm -f <container-name>
  ```
