# REDAXO mit Docker :whale:

:rocket: _Noch keine Erfahrung mit Docker? Gar kein Problem, weiter unten findest du eine [Anleitung für Einsteiger\_innen](#anleitung-für-einsteiger_innen-rocket)!_

* [Paketinhalt](#paketinhalt)
* [Verwendung](#verwendung)
* [Konfiguration und Tipps](#konfiguration-und-tipps)
* [Anleitung für Einsteiger_innen](#anleitung-für-einsteiger_innen-rocket)
* [Fragen oder Anmerkungen?](#fragen-oder-anmerkungen)

---

## Paketinhalt

* Apache 2.4
* PHP 7.1
* MariaDB 10.2
* REDAXO 5.3
* [REDAXO-Demo](https://github.com/FriendsOfREDAXO/demo_base) (optional)

Als Volume für den Webroot wird der Ordner `html/` verwendet. Ist dieser beim Build des Containers leer, wird ein aktuelles REDAXO runtergeladen und automatisch installiert (Login ins Backend mittels `admin`/`admin`).  
Die Datenbank wird in den Ordner `db/` persistiert.

__Dieses Docker-Setup bedient demnach zwei Anwendungsfälle:__

1. Bereitstellung einer frischen REDAXO-Installation, wahlweise mit verschiedenen Website-Demos
2. Betrieb und Pflege einer bestehenden REDAXO-Installation, vor allem zur lokalen Entwicklung

## Demo-Websites

Das Paket enthält die [REDAXO-Basisdemo](https://github.com/FriendsOfREDAXO/demo_base) und kann weitere Demos automatisch installieren, etwa die [Community-Demo](https://github.com/FriendsOfREDAXO/demo_community).

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/demo_base/assets/demo_base_01.jpg)

## Verwendung

__Docker-Container starten:__

    $ docker-compose up -d

__Docker-Container stoppen und entfernen:__

    $ docker-compose down

__Docker-Container neu bauen, falls Änderungen am Setup gemacht wurden:__

    $ docker-compose up -d --build --force-recreate

__REDAXO im Browser aufrufen:__

    http://localhost:20080

:point_right: _Wir benutzen Port `20080` für HTTP und `23306` für die Datenbank, um nicht in Konflikt mit den Standardports `80`/`3306` zu kommen, sollten diese bereits verwendet werden. Das macht unser Setup robuster._

---

## Konfiguration und Tipps

:warning: Beachte: Immer dann, wenn du Änderungen am Container machst, musst du danach neu bauen:

    $ docker-compose build

### PHP konfigurieren

Einfach `docker/php-apache/php.ini` anpassen und neu bauen.

Falls du eine andere PHP-Version verwenden möchtest, etwa 5.6 für ältere REDAXOs, musst du nur das Dockerfile anpassen und neu bauen.

```dockerfile
FROM php:5.6-apache
```

### Weitere PHP-Extensions installieren

Neben den Extensions, die das PHP-Apache-Image bereits mitbringt, installieren wir zusätzlich noch [GD](http://php.net/manual/de/book.image.php) und [PDO_MYSQL](http://php.net/manual/de/ref.pdo-mysql.php), siehe [/docker/php-apache/Dockerfile#L17-L18](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/docker/php-apache/Dockerfile#L17-L18). Falls du weitere Extensions benötigst, kannst du die Helfer-Funktionen benutzen, die das Image anbietet: `docker-php-ext-configure` und `docker-php-ext-install`.

Manche Extensions müssen konfiguriert werden, wie du bei GD siehst, die meisten jedoch lassen sich einfach so installieren. In dem Fall brauchst du sie nur hinter `pdo_mysql` ergänzen, etwa so:

```dockerfile
    && docker-php-ext-install -j$(nproc) gd pdo_mysql exif opcache
```

:point_right: _Tip: Um herauszufinden, welche Extensions das PHP-Apache-Image bereits mitbringt, kannst du `<?php phpinfo(); ?>` benutzen._

### Datenbank konfigurieren

Einfach `docker/mysql/my.cnf` anpassen und neu bauen.

Falls du eine andere Version oder MySQL statt MariaDB verwenden möchtest, musst du nur das Dockerfile anpassen und neu bauen.

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

:point_right: _Beachte: Hier verwenden wir ein fertiges Image für den Container, das wir nicht mehr nachträglich anpassen. Deshalb können wir es direkt mittels `image: phpmyadmin/phpmyadmin` einbinden und benötigen kein separates Dockerfile im `docker/`-Ordner, so wie bei unseren anderen Containern._

Docker-Container neustarten:

    $ docker-compose up -d

Danach ist phpMyAdmin erreichbar über:

    http://localhost:28080

---

## Anleitung für Einsteiger\_innen :rocket:

### Worum geht es?

Virtualisierung! Docker lässt verschiedene __Anwendungen in Containern__ laufen, z. B. eine Datenbank, einen Webserver und in unserem Fall ein REDAXO dazu. Diese Container werden auf deinem System ausgeführt und benutzen dessen Ressourcen, laufen aber trotzdem vollständig isoliert. Anders als virtuelle Maschinen (VM), die komplette Betriebssysteme ausführen — wie z. B. Windows auf einem Mac, um Websites im Internet Explorer zu testen oder die Steuererklärung zu machen — sind Docker-Container sehr klein und performant! Man kann problemlos zahlreiche Container auf einem Sytem starten.

Wir benutzen Docker in diesem Projekt, um uns aus verschiedenen Containern eine __Entwicklungsumgebung__ _zusammenzustecken_, in der wir REDAXO betreiben können: Ein Container enthält die Datenbank, ein anderer den Apache-Webserver mit PHP. Deine lokale Entwicklungsumgebung, die du vorher auf deinem System eingerichtet hast — vielleicht auch mit Hilfe von Tools wie XAMPP (Windows) oder MAMP (Mac) — wird damit überflüssig, denn sie wird nun über Docker-Container abgebildet. Und das bringt viele Vorteile mit, von denen für uns erstmal nur diese relevant sind:

1. Die Container sind transportabel. Du kannst sie innerhalb des Teams verteilen, so dass damit ohne besonderen Aufwand alle in der gleichen Entwicklungsumgebung arbeiten.
2. Du kannst deine lokale Umgebung so modellieren, dass sie der Live-Umgebung entspricht.

:point_right: _Wenn man Docker weiter treibt, geht es in Richtung [Microservices](https://de.wikipedia.org/wiki/Microservices), Skalierung und Automatisierung. Das kann uns erstmal egal sein, denn wir wollen unser Docker-Setup ganz einfach halten und nur für die lokale REDAXO-Entwicklung benutzen._

### Was wird benötigt?

Du musst nur [Docker (Community Edition) für dein System](https://www.docker.com/community-edition#/download) installieren, mehr wird nicht benötigt. Danach begibst du dich in deiner Konsole in den Ordner dieses Repos und startest die Container:

    $ docker-compose up -d

Das wird beim ersten Mal ein kleines Weilchen dauern, weil zuerst die _Images_ runtergeladen werden müssen, aus denen Docker dann lauffähige Container baut. In deiner Konsole wird eine Menge Text vorbeilaufen.

:warning: Wenn die Konsole wieder bereit ist und die Befehlszeile erscheint, musst du __noch weitere 1-2 Minuten warten__, bis REDAXO vollständig installiert ist. Darüber erhälst du leider keine Rückmeldung in der Konsole, sondern müsstest z. B. das kostenlose Docker-Tool [Kitematic](https://kitematic.com) verwenden, um den Fortschritt zu sehen.

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

#### Container-Konfiguration

    docker/
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

Im `docker/`-Ordner befindet sich die __Konfiguration für die Container__, die wir benutzen, nämlich `mysql/` und `php-apache/`. Diese enthalten jeweils ein `Dockerfile`, die die Bauanleitungen enthalten, mit der jeweils aus einem _Image_ ein lauffähiger _Container_ gebaut wird.

Das Dockerfile für MySQL ist ganz schlicht, denn es enthält lediglich die Angabe, welches Image verwendet wird, ohne dass dieses dann weiter angepasst wird. Das PHP-Apache-Dockerfile ist aufwendiger: Hier bestimmen wir erst das Image, schicken aber einige Anpassungen hinterher. Zum Beispiel aktivieren wir Apache-Module und installieren PHP-Extensions, die REDAXO benötigt. Im Anschluss prüfen wir, ob unser Webroot — dazu gleich mehr! — noch leer ist, und falls es das ist, holen wir uns ein frisches REDAXO von GitHub und entpacken es in den Webroot.

Die anderen Dateien enthalten Setup-Skripte, Konfigurationen für PHP, Apache und die Datenbank.

#### Webroot

    html/

Dieses Verzeichnis bildet den __Webroot__, der oben bereits genannt wurde. Es ist verknüpft mit dem Verzeichnis des Containers (ein Debian GNU/Linux übrigens), in dem der Apache-Webserver die Website hinterlegt. Wenn du also Anpassungen am REDAXO vornimmst, stehen diese unmittelbar dem Server zur Verfügung, und ebenso andersrum.  
Das bedeutet: Ebenso wie die Datenbank liegt dein REDAXO dauerhaft auf deinem System und kann von dir bearbeitet werden, während Docker dir nur die notwendige Serverumgebung bereitstellt.

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
