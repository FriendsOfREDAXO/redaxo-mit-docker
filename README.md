# REDAXO mit Docker 🐳

✌️ _Noch keine Erfahrung mit Docker? Ganz unten findest du eine [Anleitung für Einsteiger\_innen](#anleitung-für-einsteiger_innen)!_

## Inhalt

* Apache 2.4
* PHP 7.1
* MariaDB 10.2
* REDAXO 5.3

Als Volume für den Webroot wird der Ordner `html/` verwendet. Ist dieser beim Build des Containers leer, wird ein aktuelles REDAXO runtergeladen ~~und automatisch installiert~~ (siehe #1).  
Die Datenbank wird in den Ordner `db/` persistiert.

__Dieses Docker-Setup bedient demnach zwei Anwendungsfälle:__

1. Bereitstellung einer frischen REDAXO-Installation
2. Betrieb und Pflege einer bestehenden REDAXO-Installation, vor allem zur lokalen Entwicklung

## Verwendung

__Docker-Container starten:__

    $ docker-compose up -d

__Docker-Container stoppen und entfernen:__

    $ docker-compose down

__Docker-Container stoppen und neu bauen, falls Änderungen am Setup gemacht wurden:__

    $ docker-compose down
    $ docker-compose build
    $ docker-compose up -d

__REDAXO im Browser aufrufen:__

    http://localhost:20080

_Hinweis: Wir benutzen Port `20080` für HTTP und `23306` für die Datenbank, um nicht in Konflikt mit den Standardports `80`/`3306` zu kommen, sollten diese bereits verwendet werden. Das macht unser Setup robuster._

## Konfiguration und Tipps

Beachte: Immer dann, wenn du Änderungen am Container machst, musst du danach neu bauen:

    $ docker-compose build

### PHP konfigurieren

Einfach `docker/php-apache/php.ini` anpassen und neu bauen.

Falls du eine andere PHP-Version verwenden möchtest, etwa 5.6 für ältere REDAXOs, musst du nur das Dockerfile anpassen und neu bauen:

```dockerfile
FROM php:5.6-apache
```

### Weitere PHP-Extensions installieren

Neben den Extensions, die das PHP-Apache-Image bereits mitbringt, installieren wir zusätzlich noch [GD](http://php.net/manual/de/book.image.php) und [PDO_MYSQL](http://php.net/manual/de/ref.pdo-mysql.php), siehe [/docker/php-apache/Dockerfile#L17-L18](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/docker/php-apache/Dockerfile#L17-L18). Falls du weitere Extensions benötigst, kannst du die Helfer-Funktionen benutzen, die das Image anbietet: `docker-php-ext-configure` und `docker-php-ext-install`.

Manche Extensions müssen konfiguriert werden, wie du bei GD siehst, die meisten jedoch lassen sich einfach so installieren. In dem Fall brauchst du sie nur hinter `pdo_mysql` ergänzen, etwa so:

```dockerfile
    && docker-php-ext-install -j$(nproc) gd pdo_mysql exif opcache
```

_Tip: Um herauszufinden, welche Extensions das PHP-Apache-Image bereits mitbringt, kannst du `<?php phpinfo(); ?>` benutzen._

### Datenbank konfigurieren

Einfach `docker/mysql/my.cnf` anpassen und neu bauen.

Falls du eine andere Version oder MySQL statt MariaDB verwenden möchtest, musst du nur das Dockerfile anpassen und neu bauen:

```dockerfile
FROM mysql:5.5
```

### phpMyAdmin einbinden

In der `docker-compose.yml` am Ende ergänzen:

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
    PMA_USER: redaxodocker
    PMA_PASSWORD: redaxodocker
```

_Beachten: Hier verwenden wir ein fertiges Image für den Container, das wir nicht mehr nachträglich anpassen. Deshalb können wir es direkt mittels `image: phpmyadmin/phpmyadmin` einbinden und benötigen kein separates Dockerfile im `docker/`-Ordner, so wie bei unseren anderen Containern._

Docker-Container neustarten:

    $ docker-compose up -d

Danach ist phpMyAdmin erreichbar über:

    http://localhost:28080

---

## Anleitung für Einsteiger\_innen

### Worum geht’s?

Virtualisierung! Docker lässt verschiedene Anwendungen in Containern laufen, z. B. eine Datenbank, einen Webserver und ein REDAXO dazu. Diese Container werden auf deinem System ausgeführt und benutzen dessen Ressourcen. …TODO…

### Verzeichnisstruktur

    db/
        …
    docker/
        mysql/
            Dockerfile
            my.cnf
        php-apache/
            apache.conf
            docker-entrypoint.sh
            Dockerfile
            php.ini
    html/
        …
    .dockerignore
    docker-compose.yml

TODO

---

## Fragen oder Anmerkungen?

Gerne. Am besten im Slack nachfragen! ✌️  
Eine Einladung zum Slack bekommst du hier: https://redaxo.org/slack/
