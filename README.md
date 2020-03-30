_Das frühere Docker-Setup, was zuvor an dieser Stelle zu finden war, befindet sich nun im Branch [`version-1`](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/tree/version-1)._


# REDAXO mit Docker 🐳

**Eine flexible Entwicklungsumgebung für REDAXO auf Basis von Docker:**

- **REDAXO** mit **Demo-Website**
- **Apache** (Optional: NGINX)
- **MySQL** (Optional: MariaDB)
- **SSL** zum lokalen Testen
- **ImageMagick** als Bildgenerator
- **PhpMyAdmin** zur Datenbank-Verwaltung
- **Mailhog** zum Testen des E-Mail-Versands
- **Blackfire** zur Performance-Analyse


&nbsp;


## Einleitung

Dieses Setup stellt dir und deinem Team für jedes eurer REDAXO-Projekte eine flexible Entwicklungsumgebung (Apache, PHP, MySQL) bereit. Dafür kommt [Docker](https://de.wikipedia.org/wiki/Docker_(Software)) zum Einsatz, und das funktioniert so ähnlich wie eine Virtuelle Maschine, benötigt aber viel weniger Ressourcen.

Docker startet für dein Projekt einen *Container*, der einen **Webserver** enthält, und in dem ein frisches REDAXO samt Demo-Website installiert ist. Der gesamte Code wird dabei jedoch auf deinem Computer abgelegt und mit dem Container synchronisiert.

Ein weiterer Docker-Container enthält die **Datenbank**. Auch deren Inhalte werden auf deinem Computer gespeichert und synchronisiert.

Je nach Bedarf kommen weitere Container hinzu, die *jeweils eine Aufgabe* (Das Konzept von »[Microservices](https://de.wikipedia.org/wiki/Microservices)«) übernehmen. PhpMyAdmin etwa, mit dem du deine Datenbank verwalten kannst. Oder Mailhog, das alle E-Mails abfängt, die deine REDAXO-Installation versendet, und diese in einer Weboberfläche anzeigt.

Alle Container eines Projekts sind miteinander vernetzt, und du kannst beliebige weitere hinzufügen oder die bestehenden anpassen — zum Beispiel auch, um NGINX statt Apache zu verwenden oder MariaDB statt MySQL.

Deine Projektdaten bleiben dauerhaft auf deinem Computer erhalten und können wie gewohnt mittels Git versioniert werden, um die Arbeit im Team zu ermöglichen.


&nbsp;


## Technische Anforderungen

Du benötigst **zwei Bauteile**, um dieses Projekt zum Laufen zu bringen:

Das erste Bauteil ist die **Konfiguration**, sozusagen der Bauplan, für die Docker-Container. Dieser Teil liegt dir bereits vor, wenn du dieses Git-Repository auf deinem Rechner gespeichert hast. Er besteht aus einer Datei `docker-compose.yml`, in der angegeben ist, welche Container mit welchen Einstellungen verwendet werden. Und er besteht weiterhin aus dem Ordner `docker` und seinen Unterverzeichnissen, in denen wir *Images* konfigurieren und anpassen, bevor daraus *Container* gestartet werden. — Das wird später noch genauer erklärt!

Das zweite Bauteil, was du benötigst, ist **Docker** selbst, sozusagen die Maschine in unserem Setup. Das Programm muss auf deinem Computer installiert werden. Es kann kostenlos für alle gängigen Systeme (Windows, Mac, Linux) runtergeladen werden.


&nbsp;


## Installation

Wenn du erstmalig mit Docker arbeitest, sind dies die 3 Schritte, die du ausführen musst, um das Projekt zum Laufen zu bringen:

### Schritt 1: Bereite deinen Projektordner vor

Vielleicht hast du diesen Schritt bereits erledigt, wenn du dies liest. Falls nicht: Lade den Inhalt dieses Git-Repos runter und speichere ihn in einen passenden Ordner auf deinem Computer.

Tipp: Es bietet sich an, einen gemeinsamen »Projektordner« anzulegen, in dem du deine Projekte jeweils in Unterordnern ablegst.

### Schritt 2: Richte Docker auf deinem System ein

Lade [Docker Desktop](https://www.docker.com/products/docker-desktop) runter, wenn du Windows oder Mac benutzt. Linux-Nutzer benötigen die [Docker-Engine](https://hub.docker.com/search?q=&type=edition&offering=community&operating_system=linux) als kostenlose Community-Edition.

Nach der Installation startest du das Programm. An den Einstellungen muss normalerweise nichts geändert werden, allerdings musst du den Projektordner für Docker freigeben (Preferences > Resources > **File Sharing**).

### Schritt 3: Starte das Projekt!

Docker bedienst du am besten auf der **Kommandozeile**. Zwar kannst du auch im grafischen Dashboard, das du eben für die Einstellungen aufgerufen hast, Container starten und stoppen. Besser ist jedoch, du gewöhnst dich von Anfang an an die Kommandozeile.

Öffne also die Kommandozeile und wechsle in den Ordner deines Projekts (etwa so: `cd /projekte/redaxo-mit-docker`).

Benutze nun `docker-compose up -d`, um die Container zu starten! 🚀


&nbsp;


## Images und Container

Was nun passiert, nachdem du `docker-compose up -d` abgefeuert hast:

### 1. Pull

Docker erkennt, dass du in deiner `docker-compose.yml` verschiedene **Images** angegeben hast, z. B. für die Datenbank `image: mysql:8` oder für Mailhog `image: mailhog/mailhog`. Diese wird Docker nun für dich besorgen, und zwar aus dem [Docker Hub](https://hub.docker.com/). Das ist die offizielle *Registry* und damit sowas wie [npm](https://www.npmjs.com/) für JavaScript oder Composers [Packagist](https://packagist.org/) für PHP.

Docker erkennt außerdem, dass ganz oben in der `docker-compose.yml` beim Service für »redaxo« kein Image angegeben ist. Stattdessen ist dort ein Build-Pfad hinterlegt: `build: ./docker/redaxo`. In diesem Ordner erwartet Docker nun ein `Dockerfile` mit der Bauanleitung eines Images.

Das besagte Dockerfile enthält als erste Zeile `FROM friendsofredaxo/demo:base`. Das wiederum ist erneut der Hinweis auf ein Image aus dem Docker Hub, nämlich die **Demo-Website** von Friends Of REDAXO. Also unser eigenes Image! 🙌  

Docker wird nun auch dieses Image runterladen (»pull«).

### 2. Build

Nun liegen alle Inhalte aus dem Docker Hub vor, und der **Build**-Prozess startet, also das *Bauen* der Images.

Die Images aus dem Docker Hub müssen allerdings gar nicht mehr gebaut werden, denn das hat der Hub bereits selbst erledigt. Es ist seine Aufgabe, Images zu bauen und zum Download bereit zu stellen!

Was wir noch selbst bauen müssen, ist das, was im eben genannten `Dockerfile` steht. Alles ab Zeile 2. Das ist allerdings gar nicht mehr viel, denn das Image unserer Demo-Website bringt bereits ganz viel mit — dazu später mehr —, und wir wollen lediglich ein paar Dinge anpassen und ergänzen.

Wenn du in den Code des Dockerfiles schaust, siehst du anhand der Kommentare, was ungefähr passiert:

* Es werden verschiedene **Konfigurationsdateien** in den Container kopiert, etwa für PHP oder Apache
* Es wird ein **SSL-Testzertifikat** angelegt
* Es werden weitere **Apache-Module** aktiviert
* Es wird **Blackfire** aktiviert
* Am Ende wird der **Apache** gestartet 

🥳 In unserem vorherigen Docker-Setup, das nachwievor im Branch [`version-1`](https://github.com/FriendsOfREDAXO/redaxo-mit-docker/tree/version-1) verfügbar ist, haben wir viel mehr selbst gebaut. Wir haben uns lediglich ein Image für PHP mit Apache aus dem Hub geholt und mussten danach diverse PHP-Extensions installieren, die wir für REDAXO benötigen, um anschließend REDAXO selbst runter zu laden und zu installieren. Das war sehr aufwendig und hat viel Zeit benötigt. Weil Friends Of REDAXO inzwischen fertige Images im Hub anbieten, sind diese Build-Schritte nicht mehr notwendig!

### 3. Up

…


&nbsp;


## REDAXO-Images im Docker Hub

…


&nbsp;


## Betrieb deiner Container

…


&nbsp;


## Konfiguration anpassen

…


&nbsp;


## Häufige Fragen


#### …?

…


#### …?

…


&nbsp;


## Hilfe und Support

Falls du Fragen hast oder Hilfe benötigst, kontakte uns gerne im **Slack-Chat**! Eine Einladung bekommst du hier: [https://redaxo.org/slack/](https://redaxo.org/slack/).
