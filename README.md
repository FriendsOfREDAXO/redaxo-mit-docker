<p align="right">🌎 <a href="https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/README.de.md">English</a></p>

# REDAXO with Docker :whale:

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_01.jpg)

* [Introduction](#introduction)
* [Package Content](#package-content)
* [Configuration and tips](#configuration-and-tips)
* [Usage](#usage)
* [Guide for beginners](#guide-for-beginners-rocket)
* [Questions or feedback?](#questions-or-suggestions)

---

## Introduction

:rocket: _Still no experience with Docker? No problem, below you will find  [instructions for beginners](#guide-for-beginners-rocket)!_

__What does the Docker Setup Exactly?__

1. Docker provides you and your team with a __Server environment (Apache, PHP, MySQL)__ for each of your REDAXO projects. It works much like a virtual machine, but uses much less resources. You can customize the server environment and discard it at any time without losing data.
2. If desired, Docker can independently install a __fresh REDAXO in the server environment.__
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

## Configuration and tips

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

## Usage

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

## Configuration of the Container

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

In addition to the extensions that the PHP Apache image already include, we also install [GD](http://php.net/manual/de/book.image.php) and [PDO_MYSQL](http://php.net/manual/de/ref.pdo-mysql.php), look [/docker/php-apache/Dockerfile#L23-L24](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/docker/php-apache/Dockerfile#L23-L24). If you need more extensions, you can use the helper functions that the image offers: `docker-php-ext-configure` and `docker-php-ext-install`.

Some extensions need to be configured, as you see in GD, but most of them are easy to install. In that case you just need to add it after `pdo_mysql`, like this:

```dockerfile
    && docker-php-ext-install -j$(nproc) gd pdo_mysql exif opcache
```

:point_right: _Tip:To find out which extensions the php apache image already has, you can use `<?php phpinfo (); ?>`._

### Database configuration

Just customize `docker/mysql/my.cnf` and build again.
If you want to use a different version, all you have to do is adapt and rebuild the Dockerfile:

```dockerfile
FROM mysql:5.7
```

### Use Mailhog 

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_02.jpg)

We have [Mailhog](https://github.com/mailhog/MailHog) integrated in order to be able to test the e-mail dispatch within REDAXO, without having to connect a real e-mail account. Instead Mailhog intercepts the mail and offers a web interface to display it. It is accessible via:

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

:point_right: _Keep in mind: Here we use a finished image for the container, which we no longer adapt later. Therefore, we can directly integrate it with `image: phpmyadmin/phpmyadmin` and do not need a separate dockerfile in the `docker/` folder, just like our other containers._

Restart the Docker-Container:

    $ docker-compose up -d

After that you can use phpMyAdmin in the Browser:

    http://localhost:28080

---

## Guide for beginners :rocket:

### Why is?

Virtualization! Docker runs various __applications in containers__, eg. For example, a database, a web server and, in our case, a REDAXO. These containers run on your system and use its resources, but still run completely isolated. Unlike virtual machines (VMs) that run entire operating systems - such as: For example, Windows on a Mac to test websites in Internet Explorer or make tax returns - Docker containers are very small and performant! You can easily start many containers on a system.

We use Docker in this project to assemble a different environment from different containers in which we can run REDAXO: one container contains the database, another the Apache web server with PHP. Your local development environment, which you have previously set up on your system - perhaps with the help of tools such as XAMPP (Windows) or MAMP (Mac) - is thus unnecessary, because it is now displayed on Docker container. And that brings with it many advantages, of which only these are relevant for us:

1. The containers are transportable. You can distribute them within the team, so that without any special effort all in the same development environment.
2. You can customize your local environment to match the live environment.

:point_right: _If you continue docker, it goes in the direction [Microservices](https://de.wikipedia.org/wiki/Microservices), Scaling and automation. We do not care, because we want to keep our Docker setup simple and use it only for local REDAXO development._

### Was wird benötigt?

Du musst nur [Docker (Community Edition) für dein System](https://www.docker.com/community-edition#/download) installieren, mehr wird nicht benötigt. In der Konfiguration musst du die Ordner freigeben, in denen Docker-Projekte laufen dürfen. Hier gibst du nun erstmal nur den Ordner an, in dem dieses Git-Repo liegt. Danach begibst du dich in deiner Konsole in diesen Ordner und startest die Container:

    $ docker-compose up -d

Das wird beim ersten Mal ein kleines Weilchen dauern, weil zuerst die _Images_ runtergeladen werden müssen, aus denen Docker dann lauffähige Container baut. In deiner Konsole wird eine Menge Text vorbeilaufen.

:warning: Wenn die Konsole wieder bereit ist und die Befehlszeile erscheint, musst du __noch weitere 1-2 Minuten warten__, bis REDAXO vollständig installiert ist. Den Status der REDAXO-Installation siehst du nicht in deiner Konsole, weil der Vorgang im Container stattfindet. Du kannst dir die Container-Logs anschauen mittels `docker-compose logs web` (Das `web` am Ende ist unser Webserver, `db` wäre die Datenbank). Alternativ siehst du die Logs auch im kostenlosen Docker-Tool [Kitematic](https://kitematic.com), das sehr praktisch ist, wenn du mit mehreren Docker-Projekten arbeitest.

Danach steht dir ein frisches REDAXO inkl. [Demo-Website](https://github.com/FriendsOfREDAXO/demo_base) im Browser zur Verfügung unter:

    http://localhost:20080

Ins REDAXO-Backend kannst du dich einloggen mit `admin`/`admin`.

:tada:

### Wie geht es weiter?

Du solltest dich etwas mit Docker beschäftigen und vertiefst dich am besten in die [offizielle Dokumentation](https://docs.docker.com). Lass dich dabei nicht abschrecken, denn Docker kann furchtbar kompliziert werden, wenn man es in großem Stil nutzt. Und selbst in unserem kleinen Kontext ist nicht alles ganz einfach zu verstehen. Mit diesem Setup hast du eine funktionierende Entwicklungsumgebung für REDAXO, die du nach und nach im Detail verstehen wirst, wenn du dich länger mit Docker beschäftigst!

### Welche Funktion haben die Dateien und Ordner dieses Pakets?

Wir gehen mal von oben nach unten durch:

#### Datenbank

    db/
    
In diesen Ordner wird die __Datenbank__ des Containers _persistiert_, also dauerhaft auf deinem System gespeichert. Würden wir das nicht machen, wäre die Datenbank jedesmal aufs Neue leer, wenn du den Container baust. Weil wir aber dauerhaft am REDAXO arbeiten wollen, das sich in diesem Paket befindet, müssen wir die Datenbank außerhalb des Containers hinterlegen.

:point_right: _Beachte: Wenn der Ordner beim Start des Containers leer ist, richtet Docker die Datenbank frisch für dich ein. Enthält der Ordner aber bereits Inhalte, ändert Docker nichts daran und startet lediglich den Container._

#### Container-Konfiguration

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

Im `docker/`-Ordner befindet sich die __Konfiguration für die Container__, die wir benutzen, nämlich `mailhog`, `mysql/` und `php-apache/`. Diese enthalten jeweils ein `Dockerfile`, die die Bauanleitungen enthalten, mit der jeweils aus einem _Image_ ein lauffähiger _Container_ gebaut wird.

Die Dockerfiles für Mailhog und MySQL sind ganz schlicht, denn sie enthalten lediglich die Angabe, welches Image verwendet wird, ohne dass dieses dann weiter angepasst wird. Das PHP-Apache-Dockerfile ist aufwendiger: Hier bestimmen wir erst das Image, schicken aber einige Anpassungen hinterher. Zum Beispiel aktivieren wir Apache-Module und installieren PHP-Extensions, die REDAXO benötigt. Im Anschluss prüfen wir, ob unser Webroot — dazu gleich mehr! — noch leer ist, und falls es das ist, holen wir uns ein frisches REDAXO von GitHub und entpacken es in den Webroot.

Die anderen Dateien enthalten Setup-Skripte, Konfigurationen für PHP, Apache und die Datenbank.

#### Webroot

    html/

Dieses Verzeichnis bildet den __Webroot__, der oben bereits genannt wurde. Es ist verknüpft mit dem Verzeichnis des Containers (ein Debian GNU/Linux übrigens), in dem der Apache-Webserver die Website hinterlegt. Wenn du also Anpassungen am REDAXO vornimmst, stehen diese unmittelbar dem Server zur Verfügung, und ebenso andersrum.  
Das bedeutet: Ebenso wie die Datenbank liegt dein REDAXO dauerhaft auf deinem System und kann von dir bearbeitet werden, während Docker dir nur die notwendige Serverumgebung bereitstellt.

:point_right: _Beachte: Wenn der Ordner beim Start des Containers leer ist, installiert Docker ein frisches REDAXO für dich, und je nach Konfiguration (in `docker-compose.yml`) sogar noch eine Website-Demo dazu. Enthält der Ordner aber bereits Inhalte, ändert Docker nichts daran und startet lediglich den Container._

#### Ignore

    .dockerignore

In [dockerignore](https://docs.docker.com/engine/reference/builder/#dockerignore-file) wird definiert, welche Dateien und Ordner _nicht_ an den Docker-Daemon überreicht werden. Wenn dein Projektordner sehr voll ist, kannst du die für Docker unwichtigen Daten übergehen und sparst damit Ressourcen.

#### Compose

    docker-compose.yml

[Docker Compose](https://docs.docker.com/compose/overview/) ermöglicht dir, __mehrere Container gleichzeitig__ zu starten und zu verknüpfen. Es enthält z. B. Angaben darüber, wie die Container heißen, welche Ports sie benutzen und welche Verzeichnisse deines Systems sie einbinden (Volumes). Zudem kann es Informationen wie Username und Passwörter, in unserem Fall für die Datenbank, enthalten.

---

## Fragen oder Anmerkungen?

Gerne. Am besten im Slack nachfragen! ✌️  
Eine Einladung zum Slack bekommst du hier: https://redaxo.org/slack/
