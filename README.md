# REDAXO mit Docker 🐳

✌️ _Noch keine Erfahrung mit Docker? Weiter unten findest du eine [Anleitung für Einsteiger\_innen](#anleitung-fuer-einsteiger_innen)!_

__Inhalt:__

* Apache 2.4
* PHP 7.1
* MariaDB 10.2
* REDAXO 5.3

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

## REDAXO-Container mit Auto-Setup?

TODO

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
            apache.conf            docker-entrypoint.sh            Dockerfile            php.ini
    html/
        …
    .dockerignore
    docker-compose.yml

TODO