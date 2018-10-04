# Docker Image for Friendica
[![Build Status Travis](https://travis-ci.org/friendica/docker.svg?branch=master)](https://travis-ci.org/friendica/docker)

This repository holds the official Docker Image for [Friendica](https://friendi.ca)

# What is Friendica?

Friendica is a decentralised communications platform that integrates social communication.
Our platform links to independent social projects and corporate services.

![logo](https://cdn.rawgit.com/friendica/docker/9c954f4d/friendica.svg)

# How to use this image

The images are designed to be used in a micro-service environment.		
There are two types of the image you can choose from.

The `apache` tag contains a full Friendica installation including an apache web server.
It is designed to be easy to use and gets you running pretty fast.
This is also the default for the `latest` tag and version tags that are not further specified.

The second option is a `fpm` container.
It is based on the [php-fpm](https://hub.docker.com/_/php/) image and runs a fastCGI-Process that serves your Friendica server.
To use this image it must be combined with any Webserver that can proxy the http requests to the FastCGI-port of the container.

[![Try in PWD](https://github.com/play-with-docker/stacks/raw/cff22438cb4195ace27f9b15784bbb497047afa7/assets/images/button.png)](http://play-with-docker.com?stack=https://raw.githubusercontent.com/nupplaphil/friendica-docker/fec33c98be957436279b7074ca08068b18622627/stack.yml)
(Admin-E-Mail: `root@friendica.local`)

## Using the apache image

You need at least one other mariadb/mysql-container to link it to Friendica.

The apache image contains a webserver and exposes port 80.
To start the container type:

```console
$ docker run -d -p 8080:80 --link some-mysql:mysql friendica/server
```

Now you can access the Friendica installation wizard at http://localhost:8080/ from your host system.

## Using the fpm image

To use the fpm image you need an additional web server that can proxy http-request to the fpm-port of the container.
For fpm connection this container exposes port 9000.
In most cases you might want use another container or your host as proxy.
If you use your host you can address your Friendica container directly on port 9000.
If you use another container, make sure that you add them to the same docker network (via `docker run --network <NAME> ...` or a `docker-compose` file).
In both cases you don't want to map the fpm port to you host.

```console
$ docker run -d friendica/server:fpm
```

As the fastCGI-Process is not capable of serving static files (style sheets, images, ...) the webserver needs access to these files.
This can be achieved with the `volumes-from` option.
You can find more information in the docker-compose section.

## Using the cron job

There are three options to enable the cron-job for Friendica:

-	Using the default Image and activate the cron-job (see [Installation](https://friendi.ca/resources/installation/), sector `Activating scheduled tasks`)
-	Using the default image (apache, fpm, fpm-alpine) and creating **two** container (one for cron and one for the main app)
-	Using one of the additional, prepared [`cron dockerfiles`](https://github.com/friendica/docker/tree/master/.examples/dockerfiles/cron)

## Possible Environment Variables

**Auto Install Settings**
-	`FRIENDICA_ADMIN_MAIL` E-Mail address of the administrator.
-	`FRIENDICA_TZ` The default localization of the Friendica server.
-	`FRIENDICA_LANG` The default language of the Friendica server.
-	`FRIENDICA_PHP_PATH` The path of the PHP binary.

**Database** (**required at installation**)
-	`MYSQL_USERNAME` Username for the database user using mysql.
-	`MYSQL_USER` Username for the database user using mariadb.
-	`MYSQL_PASSWORD` Password for the database user using mysql / mariadb.
-	`MYSQL_DATABASE` Name of the database using mysql / mariadb.
-	`MYSQL_HOST` Hostname of the database server using mysql / mariadb.
-	`MYSQL_PORT` Port of the database server using mysql / mariadb (Default: `3306`)

**Develop/Release Candidat Settings**
-	`FRIENDICA_UPGRADE` If set to `true`, a develop or release candidat node will get updated at startup.

## Administrator account

Because Friendica links the administrator account to a specific mail address, you **have** to set a valid address for `MAILNAME`.

## Mail settings

see the [example](https://github.com/friendica/docker/tree/master/.examples/dockerfiles/README.md#smtpsetting)

## Database settings

You have to link a running database container, e. g. `--link my-mysql:mysql`, and then use `mysql` as the database host on setup.

## Persistent data

The Friendica installation and all data beyond what lives in the database (file uploads, etc) is stored in the [unnamed docker volume](https://docs.docker.com/engine/tutorials/dockervolumes/#adding-a-data-volume) volume `/var/www/html`.
The docker daemon will store that data within the docker directory `/var/lib/docker/volumes/...`.
That means your data is saved even if the container crashes, is stopped or deleted.

To make your data persistent to upgrading and get access for backups is using named docker volume or mount a host folder.
To achieve this you need one volume for your database container and Friendica.

Friendica:

-	`/var/www/html/` folder where all Friendica data lives

```console
$ docker run -d \
  -v friendica-vol-1:/var/www/html \
  friendica/server
```

Database:

-	`/var/lib/mysql` MySQL / MariaDB Data

```console
$ docker run -d \
  -v mysql-vol-1:/var/lib/mysql \
  mariadb
```

## Automatic installation

The Friendica image supports auto configuration via environment variables.
You can preconfigure everything that is asked on the install page on first run.
To enable the automatic installation, there are two possibilities:

### Environment Variables

You have to set at least the following environment variables (others are optional).

-	`FRIENDICA_ADMIN_MAIL` E-Mail address of the administrator.
-	`MYSQL_USERNAME` or `MYSQL_USER` Username for the database user using mysql/mariadb.
-	`MYSQL_PASSWORD` Password for the database user using mysql / mariadb.
-	`MYSQL_DATABASE` Name of the database using mysql / mariadb.
-	`MYSQL_HOST` Hostname of the database server using mysql / mariadb.

### Using a predefined config file

You can create a `local.ini.php` and `COPY` it to `/usr/src/config`.
If no other environment variable is set, this `local.ini.php` will get copied to the config path.

# Maintenance of the image

## Updating to a newer version

There are differences between the deveop (everything which ends with `-rc` or `-dev`) and the stable (the rest) branches. 

### Updating stable

You have to pull the latest image from the hub (`docker pull friendica`).
The stable branch gets checked at every startup and will get updated if no installation was found or a new image is used.

### Updating develop

You don't need to pull the image for each commit in [friendica](https://github.com/friendica/friendica/).
Instead, the develop branch will get updated if no installation was found or the environment variable `FRIENDICA_UPGRADE` is set to `true`.

It will clone the latest Friendica version and copy it to your working directory.

# Running this image with docker-compose

The easiest way to get a fully featured and functional setup is using a `docker-compose` file.
There are too many different possibilities to setup your system, so here are only some examples what you have to look for.

At first make sure you have chosen the right base image (fpm or apache) and added the features you wanted (see below).
In every case you want to add a database container and docker volumes to get easy access to your persistent data.
When you want your server reachable from the internet adding HTTPS-encryption is mandatory!
See below for more information.

## Base version - apache

This version will use the apache image and add a mariaDB container.
The volumes are set to keep your data persistent.
This setup provides **no ssl encryption** and is intended to run behind a proxy.

Make sure to set the variable `MYSQL_PASSWORD` before run this setup.

```yaml
version: '2'

services:
  db:
    image: mariadb
    restart: always
    volumes:
      - db:/var/lib/mysql
    environment:
      - MYSQL_USER=friendica
      - MYSQL_PASSWORD=
      - MYSQL_DATABASE=friendica
      - MYSQL_RANDOM_ROOT_PASSWORD=yes

  app:
    image: friendica/server
    restart: always
    volumes:
      - friendica:/var/www/html
    ports:
      - "8080:80"
    environment:
      - MYSQL_HOST=db
      - MYSQL_USER=friendica
      - MYSQL_PASSWORD=
      - MYSQL_DATABASE=friendica
      - FRIENDICA_ADMIN_MAIL=root@friendica.local      
    hostname: friendica.local
    depends_on:
      - db

volumes:
  db:
  friendica:
```

Then run `docker-compose up -d`, now you can access Friendica at http://localhost:8080/ from your system.

## Base version - FPM

When using the FPM image you need another container that acts as web server on port 80 and proxies requests to the Friendica container.
In this example a simple nginx container is combined with the Friendica-fpm image and a MariaDB database container.
The data is stored in docker volumes.
The nginx container also need access to static files from your Friendica installation.
It gets access to all the volumes mounted to Friendica via the `volumes_from` option.
The configuration for nginx is stored in the configuration file `nginx.conf` that is mounted into the container.

An example can be found in the [examples section](https://github.com/friendica/docker/tree/master/.examples).

As this setup does **not include encryption** it should to be run behind a proxy.

Prerequisites for this example:
- Make sure to set the variable `MYSQL_PASSWORD` before you run the setup.
- Create a `nginx.conf` in the same directory as the docker-compose.yml file (take it from [example](https://github.com/friendica/docker/tree/master/.examples/docker-compose/with-traefik-proxy/mariadb-cron-smtp/fpm/web/nginx.conf))

```yaml
version: '2'

services:
  db:
    image: mariadb
    restart: always
    volumes:
      - db:/var/lib/mysql
    environment:
      - MYSQL_USER=friendica
      - MYSQL_PASSWORD=
      - MYSQL_DATABASE=friendica
      - MYSQL_RANDOM_ROOT_PASSWORD=yes

  app:
    image: friendica/server:fpm
    restart: always
    volumes:
      - friendica:/var/www/html    
    environment:
      - MYSQL_HOST=db
      - MYSQL_USER=friendica
      - MYSQL_PASSWORD=
      - MYSQL_DATABASE=friendica
      - FRIENDICA_ADMIN_MAIL=root@friendica.local
    hostname: friendica.local
    networks:
      - proxy-tier
      - default 

  web:
    image: nginx
    ports:
      - 8080:80
    links:
      - app
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf:ro    
    restart: always
    networks:
      - proxy-tier  

volumes:
  db:
  friendica:

networks:
  proxy-tier:
```

Then run `docker-compose up -d`, now you can access Friendica at http://localhost:8080/ from your system.

# Questions / Issues

If you got any questions or problems using the image, please visit our [Github Repository](https://github.com/friendica/docker) and write an issue.
