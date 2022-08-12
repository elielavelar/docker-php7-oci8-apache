# Docker php7-oci8-apache

This image is built on the [official PHP docker image](https://hub.docker.com/_/php/)

It contains PHP 7, Apache 2
PHP Extensions:
- Oracle's OCI8 + PDO OCI
- SQL Server + SQL Server PDO
- MySQL + MySQL PDO
- XDebug (Development environment)

You can use it to connect your docker instance with Oracle DB.

To use this image, docker-compose is prefered:

```
docker compose up
```
To use development environment, run the follow command
```
docker compose --f docker-compose-dev.yml up
```

Then go to 

http://localhost:8089/

and you should see the PHP Info page, with OCI8 enabled
