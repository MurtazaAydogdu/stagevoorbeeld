# Microservice-Transactions
Microservices for registering the number of products that a user has available per time period.

## Usage
```bash
# Sets up VM with docker environment
$ source setenv
# Setup environment variables
$ cp .env.example .env
# Start microstore
$ dobi dev
```

If you wan't to make local changes to your `docker-compose.yml` file you should create a `docker-compose.override.yml`. Docker compose applies the changes in this file automatically.

## Database
Microservice-transactions uses a Mysql instance (`mysqldb` in `docker-compose.yml`).

## Testing
[PHPUNIT](https://phpunit.de/) is used to run tests. 

The tests are defined with the `*Test.php` suffix

```bash
# Run test with dobi commands
$ dobi e2e
```

## Deployment

This application is deployed on the zerok.nl Docker Swarm and available at: [microservicetransactions.zerok.nl](https://microservicetransactions.zerok.nl/api/version).

To deploy to the swarm at zerok.nl you can use this command:

```bash
# Make sure you have a ssh config for master.swarm.zerok.nl
$ DB_PASSWORD=password DB_USERNAME=username ./operations/deployment/dev.microservicetransactions.zerok.nl.sh
```

## Docs
The current API documentation is available at the folllowing endpoint: `/api/swagger`. You can use this URL:
`http://microservicetransactions.zerok.nl/api/swagger` with the [swagger-ui](http://petstore.swagger.io).