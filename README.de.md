<p align="right">🌎 <a href="https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/README.md">English</a></p>

# REDAXO mit Docker :whale:

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_01.jpg)

* [Einleitung](#einleitung)
* [Paketinhalt](#paketinhalt)
* [Verwendung](#verwendung)
* [Anpassungen für deine Projekte](#anpassungen-für-deine-projekte)
* [Konfiguration und Tipps](#konfiguration-und-tipps)
* [Anleitung für Einsteiger_innen](#anleitung-für-einsteiger_innen-rocket)
* [Fragen oder Anmerkungen?](#fragen-oder-anmerkungen)

---

## Einleitung

:rocket: _Noch keine Erfahrung mit Docker? Gar kein Problem, weiter unten findest du eine [Anleitung für Einsteiger\_innen](#anleitung-für-einsteiger_innen-rocket)!_

__Ganz kurz, welchen Zweck erfüllt dieses Docker-Setup?__

1. Docker stellt dir und deinem Team für jedes eurer REDAXO-Projekte eine __Serverumgebung (Apache, PHP, MySQL)__ bereit. Das funktioniert so ähnlich wie eine Virtuelle Maschine, benötigt aber viel weniger Ressourcen. Die Serverumgebung kannst du beliebig anpassen und jederzeit verwerfen, ohne dass Daten verloren gehen.
2. Falls gewollt, kann Docker in der Serverumgebung eigenständig ein __frisches REDAXO installieren__.
3. Noch besser: Docker kann sogar komplette __Demo-Websites installieren__, z. B. die beliebte [Basisdemo](https://github.com/FriendsOfREDAXO/demo_base) oder die [Community-Demo](https://github.com/FriendsOfREDAXO/demo_community). Damit kannst du jederzeit ohne besonderen Aufwand REDAXO-Features ausprobieren.

__Für wen ist sowas sinnvoll?__

* Für alle, die intensiv mit REDAXO arbeiten und __mehrere Projekte__ betreuen. Mit Docker kannst du jedem Projekt die passende Serverumgebung mitgeben, und die Auto-Installation ermöglicht dir, ohne besonderen Aufwand frische REDAXOs samt Demo-Websites zu generieren, an denen du Funktionen testen und entwickeln kannst.
* Für Teams, denn sie erhalten dadurch eine __einheitliche Serverumgebung__ und sparen die Zeit, ihre Systeme jeweils manuell einrichten und pflegen zu müssen.
* Für alle, die __komplexe Anwendungen__ entwickeln: Falls dein REDAXO eine Elasticsearch einsetzen möchte, einen externen Mailserver benötigt, Daten bei S3 auslagert oder sonstige Dienste benutzt, kannst du die Umgebung mit verschiedenen Docker-Containern abbilden.

__Okay cool, wie geht’s los?__

* Falls du schon Docker-Erfahrung hast: `docker-compose up -d`, siehe [Verwendung](#verwendung).
* Falls Docker für dich noch ziemlich neu ist: Gar kein Problem, es gibt eine [Anleitung für Einsteiger\_innen](#anleitung-für-einsteiger_innen-rocket). :rocket: Falls du Fragen hast oder Hilfe benötigst, kontakte uns jederzeit gerne im Slack-Chat! Eine Einladung bekommst du hier: https://redaxo.org/slack/

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/demo_base/assets/demo_base_01.jpg)

---

## Paketinhalt

* Apache 2.4
* PHP 7.2
* MySQL 5.7
* [Mailhog](https://github.com/mailhog/MailHog) (zum Testen des E-Mailversands)
* REDAXO 5.x
* [REDAXO-Demo](https://github.com/FriendsOfREDAXO/demo_base) (optional)

Als Volume für den Webroot wird der Ordner `html/` verwendet. Ist dieser beim Build des Containers leer, wird ein aktuelles REDAXO runtergeladen und automatisch installiert (Login ins Backend mittels `admin`/`admin`).  
Die Datenbank wird in den Ordner `db/` persistiert.

---

## Verwendung

__Docker-Container starten:__

    $ docker-compose up -d

__Docker-Container stoppen und entfernen:__

    $ docker-compose down

__Docker-Images neu bauen, falls Änderungen am Setup gemacht wurden:__

    $ docker-compose build

Oder praktischerweise zusammengefasst (Alle Images bauen und alle Container neustarten, siehe [Docs](https://docs.docker.com/compose/reference/up/)):

    $ docker-compose up -d --build --force-recreate

__REDAXO im Browser aufrufen:__

     http://localhost:20080
    https://localhost:20443

:point_right: _Wir benutzen Port `20080` für HTTP, `20443` für HTTPS und `23306` für die Datenbank, um nicht in Konflikt mit den Standardports `80`/`443`/`3306` zu kommen, sollten diese bereits verwendet werden. Das macht unser Setup robuster.  
Wenn du mehrere Docker-Projekte verwendest, musst du noch beachten, dass alle diese Ports verwenden und deshalb immer nur eins laufen kann, nicht mehrere gleichzeitig._

:point_right: _Für den Zugriff mittels HTTPS wird ein SSL-Zertifikat generiert, das nur für Testzwecke funktioniert. Dein Browser wird dich darauf hinweisen, dass die Verbindung nicht sicher ist. Zum lokalen Testen allerdings reicht das völlig aus, und du kannst den Sicherheitshinweis übergehen._

---

## Anpassungen für deine Projekte

An welchen Stellen musst du irgendwas anpassen, wenn du das Paket für deine Projekte verwenden möchtest?

1. __Die Namen deiner Container__  
`docker-compose.yml`  
In diesem Paket beginnen die Container-Namen mit `redaxodocker`. Für deine Projekte solltest du den Namen anpassen, am besten jeweils so, dass du das Projekt am Namen erkennen kannst. Am Ende wirst du nämlich viele Container auf deinem System haben und brauchst eine gute Übersicht!
2. __Die Datenbank-Konfiguration__  
`docker-compose.yml` und `docker/php-apache/default.config.yml`  
Für die lokale Entwicklung sind `MYSQL_USER` und `MYSQL_PASSWORD` nicht allzu relevant, denn deine Datenbank läuft gekapstelt in einem Docker-Container. Solltest du keinen Deployment-Workflow haben und Datenbank-Dumps manuell auf dem Live-Server importieren, brauchst du an dieser Stelle auch nicht unbedingt etwas zu ändern.  
Aber natürlich solltest du die Credentials anpassen, falls sie deine Entwicklungsumgebung jemals verlassen und auf einem Produktivserver landen!
3. __Den Login für deinen REDAXO-Admin__  
`docker-compose.yml`  
Falls Docker für dich REDAXO automatisch einrichtet, werden `REDAXO_USER` und `REDAXO_PASSWORD` verwendet, um einen Adminnutzer anzulegen. Sollte dein Projekt jemals so live gehen, verwendest du also besser andere Angaben als `admin` :)
4. __Eine REDAXO-Demo__  
`docker-compose.yml`  
Falls Docker für dich eine Website-Demo automatisch einrichten soll, kannst du diese unter `REDAXO_DEMO` festlegen. Lasse den Wert leer, falls keine Demo eingerichtet werden soll.  
Die Liste der vorhandenen Demos findest du in `docker/php-apache/demos.yml`.

:point_up: Um es kurz zu machen: Wenn du dieses Setup für deine REDAXO-Projekte zur lokalen Entwicklung verwendest, brauchst du vermutlich nur Punkt 1 beachten, also die Container-Namen für jedes Projekt anzupassen.

---

## Konfiguration und Tipps

:warning: Beachte: Immer dann, wenn du Änderungen am Container machst, musst du danach neu bauen!

    $ docker-compose build

### REDAXO-Version festlegen

In `docker/php-apache/Dockerfile` wird die Version als `ENV`, also Umgebungsvariable, hinterlegt. Sie besteht aus zwei Teilen, der Version und einem Hash, der verwendet wird, um nach dem Download auf Richtigkeit zu prüfen. Wie du den Hash einer neuen Version herausfindest, steht in der [CONTRIBUTING.md](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/CONTRIBUTING.md).  

Die REDAXO-Version ist übrigens nur relevant, falls Docker das System für dich automatisch installiert. Falls du manuell installierst oder ein bestehendes REDAXO updatest, musst du hier nichts ändern.

### PHP-Version festlegen und konfigurieren

Einfach `docker/php-apache/php.ini` anpassen und neu bauen.  
Falls du eine andere PHP-Version verwenden möchtest, etwa 5.6 für ältere REDAXOs, musst du nur das Dockerfile anpassen und neu bauen:

```dockerfile
FROM php:5.6-apache
```

### Weitere PHP-Extensions installieren

Neben den Extensions, die das PHP-Apache-Image bereits mitbringt, installieren wir zusätzlich noch [GD](http://php.net/manual/de/book.image.php) und [PDO_MYSQL](http://php.net/manual/de/ref.pdo-mysql.php), siehe [/docker/php-apache/Dockerfile#L23-L24](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/blob/master/docker/php-apache/Dockerfile#L23-L24). Falls du weitere Extensions benötigst, kannst du die Helfer-Funktionen benutzen, die das Image anbietet: `docker-php-ext-configure` und `docker-php-ext-install`.

Manche Extensions müssen konfiguriert werden, wie du bei GD siehst, die meisten jedoch lassen sich einfach so installieren. In dem Fall brauchst du sie nur hinter `pdo_mysql` ergänzen, etwa so:

```dockerfile
    && docker-php-ext-install -j$(nproc) gd pdo_mysql exif opcache
```

:point_right: _Tip: Um herauszufinden, welche Extensions das PHP-Apache-Image bereits mitbringt, kannst du `<?php phpinfo(); ?>` benutzen._

### Datenbank konfigurieren

Einfach `docker/mysql/my.cnf` anpassen und neu bauen.  
Falls du eine andere Version verwenden möchtest, musst du nur das Dockerfile anpassen und neu bauen:

```dockerfile
FROM mysql:5.7
```

### Mailhog verwenden

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_02.jpg)

Wir haben [Mailhog](https://github.com/mailhog/MailHog) integriert, um den E-Mailversand innerhalb von REDAXO testen zu können, ohne dass dabei ein echtes E-Mailkonto angebunden werden muss. Mailhog fängt stattdessen die Mails ab und bietet eine Weboberfläche, um sie anzuzeigen. Sie ist erreichbar über:

    http://localhost:28025

:point_right: _Tip: Im REDAXO-Backend musst du im AddOn PHPMailer nichts weiter konfigurieren. Benutze den Standardversand über `mail()` und sende eine Testmail an dich. Diese sollte direkt im Mailhog auftauchen._

### phpMyAdmin einbinden

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_03.jpg)

Falls du phpMyAdmin integrieren möchtest, musst du lediglich diesen Codeschnipsel in der `docker-compose.yml` am Ende ergänzen:

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
