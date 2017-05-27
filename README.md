# REDAXO mit Docker 🐳

✌️ _Noch keine Erfahrung mit Docker? Weiter unten findest du eine [Anleitung für Einsteiger\_innen](#anleitung-für-einsteiger_innen)!_

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