<p align="right">🌎 <a href="https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/README.de.md">Deutsch</a></p>

# REDAXO with Docker :whale:

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_01.jpg)

* [Introduction](#introduction)
* [Package Content](#package-content)
* [Usage](#usage)
* [Customize for your project](#customize-for-your-project)
* [Configuration and Tips](#configuration-and-tips)
* [Beginner’s Guide](#beginners-guide-rocket)
* [Questions or comments?](#questions-or-comments)

---

## Introduction

:rocket: _No experience with Docker yet? No worries, find a [Beginner’s guide](#beginners-guide-rocket) below!_

__In short, what is the purpose of this Docker setup?__

1. Docker provides you and your team with a __server environment (Apache, PHP, MySQL)__ for each of your REDAXO projects. It works much like a virtual machine but requires significantly fewer resources. You can customize the server environment and discard it at any time without losing data.
2. If you like, Docker can __automatically install a fresh REDAXO__ within the server environment.
3. Much better: Docker can install the __Demo-Websites__, for example the [Basisdemo](https://github.com/FriendsOfREDAXO/demo_base) or the [Community-Demo](https://github.com/FriendsOfREDAXO/demo_community). You can try the REDAXO features anytime without special requirements.

__Is it usefull for you?__

* For all those who work intensively with REDAXO and supervise __several projects__. With Docker, you can give any project the right server environment, and auto-installation allows you to easily generate fresh REDAXOs and demo sites to test and develop features.
* For teams, because they get a __unified server environment__ and save the time to manually set up and maintain their systems.
* For all those who want to develop __complex applications__: If your REDAXO wants to use an Elasticsearch, needs an external mail server, outsourced data to S3 or uses other services, you can map the environment with different Docker containers.

__Okay cool, how to start?__

* If you have already experience with Docker: `docker-compose up -d`, look at [Usage](#usage).
* If Docker is still pretty new to you: No Problem!, here is a [Guide for beginners](#guide-for-beginners-rocket). :rocket: If you have questions or need help, feel free to contact us in Slack Chat! You will receive an invitation here: https://redaxo.org/slack/

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/demo_base/assets/demo_base_01.jpg)

---

## Package Content

* Apache 2.4
* PHP 7.2
* MySQL 5.7
* [Mailhog](https://github.com/mailhog/MailHog) (For Testing to send Emails)
* REDAXO 5.x
* [REDAXO-Demo](https://github.com/FriendsOfREDAXO/demo_base) (optional)

The volume for the Webroot is the folder `html/`. If this is empty during the build of the container, a current REDAXO will be downloaded and automatically installed (login into the backend via `admin`/`admin`).
The database is persisted to the `db/` folder.

---

## Usage

__Start a Docker-Container:__

    $ docker-compose up -d

__Stop and remove the Docker Container:__

    $ docker-compose down

__Rebuild docker images if changes were made to the setup:__

    $ docker-compose build

Or conveniently summarized (build all images and restart all containers, see [Docs](https://docs.docker.com/compose/reference/up/)):

    $ docker-compose up -d --build --force-recreate

__REDAXO in the Browser:__

     http://localhost:20080
     https://localhost:20443

:point_right:_We use Port `20080` for HTTP, `20443` for HTTPS and `23306` for the database, so as not to interfere with the standard `80`/`443`/`3306` if they are already in use. That makes our setup more robust.
If you use several Docker projects, you have to keep in mind that all these ports use and therefore only one can run at a time, not several at the same time._

:point_right: _To access via HTTPS, an SSL certificate will be generated that works only for testing purposes. Your browser will alert you that the connection is not secure. For local testing, however, that's enough, and you can skip the safety note._

---

## Customize for your project

Where do you have to adjust something if you want to use the package for your projects?

1. __The names of your containers__  
`docker-compose.yml`  
In this package the container names start with `redaxodocker`. For your projects you should adapt the name, preferably in each case so that you can recognize the project by the name. In the end you will have many containers on your system and you need a good overview!
2. __The Database-Configuration__  
`docker-compose.yml` and `docker/php-apache/default.config.yml`
For local development, `MYSQL_USER` and `MYSQL_PASSWORD` are not all that relevant because your database runs in a docker container. If you do not have any work experience, you are not in need of change at this point.
But of course you should adjust the credentials if you ever leave your development environment and end up on a productive server !.
3. __The login for your REDAXO-Admin__  
`docker-compose.yml`  
If Docker automatically sets up REDAXO for you, `REDAXO_USER` and `REDAXO_PASSWORD` are used to create an Adminsuser. If your project ever goes live like this, then you better use other information than `admin` :)
4. __REDAXO-Demo__  
`docker-compose.yml`  
If Docker is going to automatically set up a website demo for you, you can set it up under `REDAXO_DEMO`. Leave the value empty if you do not want to set up a demo.
The list of existing demos can be found in `docker/php-apache/demos.yml`.

:point_up: For Short: if you're using this setup for your REDAXO local development projects, you probably only need to consider point 1, which means customizing the container names for each project.

---

## Configuration and tips

:warning: Keep in mind: Whenever you make changes to the container, you have to rebuild it afterwards!

    $ docker-compose build

### Set REDAXO-Version

In `docker/php-apache/Dockerfile`, the version is stored as `ENV`, ie environment variable. It consists of two parts, the version and a hash, which is used to check for correctness after the download. How to find out the hash of a new version is in the [CONTRIBUTING.md](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/CONTRIBUTING.md).  

Incidentally, the REDAXO version is only relevant if Docker automatically installs the system for you. If you are installing manually or updating an existing REDAXO, you do not have to change anything here.

### Set and configure PHP version

Just customize `docker/php-apache/php.ini` and build again.
If you want to use a different version of PHP, such as 5.6 for older REDAXO versions, you just have to customize and rebuild the Dockerfile:

```dockerfile
FROM php:5.6-apache
```

### Install more PHP extensions

In addition to the extensions that the PHP Apache image already includes, we also install [GD](http://php.net/manual/de/book.image.php) and [PDO_MYSQL](http://php.net/manual/de/ref.pdo-mysql.php) ( please refer to [/docker/php-apache/Dockerfile#L23-L24](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/docker/php-apache/Dockerfile#L23-L24) ).  If you need more extensions, you can use the helper functions that the image offers: `docker-php-ext-configure` and `docker-php-ext-install`.

Some extensions need to be configured, like GD, but most of them you just have to install. You do that by just adding them right after `pdo_mysql`, like this:

```dockerfile
    && docker-php-ext-install -j$(nproc) gd pdo_mysql exif opcache
```

:point_right: _Tip:To find out which extensions the PHP Apache image already has, you can use `<?php phpinfo (); ?>`._

### Database configuration

Just customize `docker/mysql/my.cnf` and build again.
If you want to use a different version, all you have to do is adapt and rebuild the Dockerfile:

```dockerfile
FROM mysql:5.7
```

### Use Mailhog 

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_02.jpg)

We integrated [Mailhog](https://github.com/mailhog/MailHog) in order to be able to test the e-mail dispatch within REDAXO, without having to fill in an existing e-mail account. Instead Mailhog intercepts the mail and offers a web interface to display it. It is accessible via:

    http://localhost:28025

:point_right: _Tip: In the REDAXO backend, you do not have to configure anything in the AddOn PHPMailer. Use the standard shipping via `mail()` and send a testmail to you. This should appear directly in the mailhog._

### Integrate phpMyAdmin

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_03.jpg)

If you want to integrate phpMyAdmin you just have to add this code snippet in the `docker-compose.yml` at the end:

```yaml
phpmyadmin:
  container_name: redaxodocker_phpmyadmin
  hostname: redaxodocker_phpmyadmin
  image: phpmyadmin/phpmyadmin
  ports:
    - 28080:80
  depends_on:
    - db
  environment:
    PMA_HOST: db
    PMA_USER: redaxo
    PMA_PASSWORD: redaxo
```

:point_right: _Keep in mind: Here we use a finished image for the container, which we do not adapt later. Therefore, we can directly integrate it with `image: phpmyadmin/phpmyadmin`  and do not need a separate dockerfile in the `docker/` folder, just like with our other containers._

Restart the Docker-Container:

    $ docker-compose up -d

After that you can use phpMyAdmin in the Browser:

    http://localhost:28080

---

## Beginner’s Guide :rocket:

### What is it all about?

Virtualization! Docker runs various __applications in containers__, eg. For example, a database, a web server and, in our case, a REDAXO. These containers run on your system and use its resources, but still run completely isolated. Unlike virtual machines (VMs) that run entire operating systems - such as: For example, Windows on a Mac to test websites in Internet Explorer or make tax returns - Docker containers are very small and performant! You can easily start many containers on a system.

We use Docker in this project to assemble a development environment in which we can run REDAXO by using different containers: one container contains the database, another the Apache web server with PHP. Your local development environment, which you have previously set up on your system - perhaps with the help of tools such as XAMPP (Windows) or MAMP (Mac) - is thus unnecessary, because it is now provided by Docker container. And that brings many advantages with it, of which only these are relevant for us:

1. The containers are transportable. You can distribute them within a team, so that without any special effort all team members work in the same development environment.
2. You can customize your local environment to match the live environment.

:point_right: _If you continue docker, it leads to topics like [Microservices](https://de.wikipedia.org/wiki/Microservices), Scaling and automation. We do not care, because we want to keep our Docker setup simple and use it only for local REDAXO development._

### What is needed?

You only have to install [Docker (Community Edition) for your System](https://www.docker.com/community-edition#/download)  - nothing more is needed. In configuration, you have to set the folders where Docker projects are allowed to run in. For a start you put in here just the one folder, in which this Git repo lies. Then change into this folder inside your console and start the containers:

    $ docker-compose up -d

This will take a little while the first time, because all the Images must be downloaded, from which Docker will then build working containers. There will be a lot of text in your console.

:warning: When the console is ready again and the command line appears, you will have to wait __another 1-2 minutes__ until REDAXO is fully installed. You will not see the status of the REDAXO installation in your console, because the process is taking place inside of the container. You can look at the container logs using `docker-compose logs web` (The `web` at the end is our web server, db would be the database). Alternatively you can see the logs in the free Docker tool [Kitematic](https://kitematic.com), which is very useful when working with multiple Docker projects.

Then you have a fresh REDAXO together with the [Demo-Website](https://github.com/FriendsOfREDAXO/demo_base) available in the browser through:

    http://localhost:20080

You can log in to the REDAXO backend using `admin`/`admin`.

:tada:

### What are the next steps?

You should work a little with Docker and dig into the [official Documentation](https://docs.docker.com). Do not let yourself be scared off - Docker can get really complicated if you use it on a large scale. And even in our small context here, not everything is easy to understand. With this setup you have a working development environment for REDAXO that you'll understand more in detail the longer you work with Docker!

### What is the function of the files and folders of this package?

Let's take a look - step by step:

#### Database

    db/
    
In this folder, the __Database__ of the container _persists_, ie is permanently stored on your system. If we did not do that, the database would be empty again each time you build the container. But because we want to work permanently on the REDAXO inside this package, we have to keep the database outside the container.

:point_right: _Keep in mind: If the folder is empty when the container starts, Docker will set up the database for you. But if the folder already contains content, Docker does not change it and just starts the container._

#### Container-Configuration

    docker/
        mailhog/
            Dockerfile
        mysql/
            Dockerfile
            my.cnf
        php-apache/
            apache.conf
            default.config.yml
            demos.yml
            docker-entrypoint.sh
            docker-redaxo.php
            Dockerfile
            php.ini
            ssmtp.conf

In the `docker/` folder is the __configuration for the containers__ we use, namely `mailhog`, `mysql/ ` and `php-apache/ `. These each contain a `dockerfile`, which contains the construction manuals, with which an executable _Container_ is built from an _Image_.

The Dockerfiles for Mailhog and MySQL are quite simple, because they only contain the indication which image is used, without adapting it further. The PHP Apache Dockerfile is more complex: Here we first determine the image, but make several adjustments. For example we enable some Apache modules and install PHP extensions that REDAXO needs. Afterwards we check if our webroot - more about that follows - is still empty, and if it is, we pull a fresh REDAXO from GitHub and unpack it into the webroot.

The other folders contain setup scripts, configurations for PHP, Apache, and the database.

#### Webroot

    html/

This directory forms the __Webroot__ mentioned above. It is linked to the directory of the container (a Debian GNU / Linux by the way) in which the Apache web server expects the website to be. So if you make adjustments to your REDAXO, they are immediately available to the server, and vice versa.
This means: Like the database, your REDAXO remains permanently on your system and can be edited by you anytime. Docker only provides you with the necessary server environment.


:point_right: _Keep in mind: If the folder is empty at the start of the container, Docker will install a fresh REDAXO for you, and depending on the configuration (in `docker-compose.yml`) even a website demo. But if the folder already contains content, Docker does not change it and just starts the container._

#### Ignore

    .dockerignore

The File [dockerignore](https://docs.docker.com/engine/reference/builder/#dockerignore-file) defines which files and folders will not be given to the Docker daemon. If your project folder is very busy, you can bypass Docker's unimportant data and save resources.

#### Compose

    docker-compose.yml

[Docker Compose](https://docs.docker.com/compose/overview/) allows you to start and link __several containers at the same time__. It contains information about what the containers are called, which ports they use, and which directories of your system they are using (volumes). It can also contain information such as username and passwords - in our case for the database.

---

## Questions or Comments?

Sure. The best ask in Slack! ✌️  
An invitation to Slack you get here: https://redaxo.org/slack/
