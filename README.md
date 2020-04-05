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

Dieses Setup stellt dir und deinem Team für jedes eurer REDAXO-Projekte eine flexible Entwicklungsumgebung (Apache, PHP, MySQL) bereit. Dafür kommt [Docker](https://de.wikipedia.org/wiki/Docker_(Software)) zum Einsatz, und das funktioniert so ähnlich wie eine *Virtuelle Maschine*, benötigt aber viel weniger Ressourcen.

Docker startet für dein Projekt einen *Container*, der einen **Webserver** enthält, und in dem ein frisches REDAXO samt Demo-Website installiert ist. Der gesamte Code wird dabei jedoch auf deinem Computer abgelegt und mit dem Container synchronisiert.

Ein weiterer Docker-Container enthält die **Datenbank**. Auch deren Inhalte werden auf deinem Computer gespeichert und synchronisiert.

Je nach Bedarf kommen weitere Container hinzu, die *jeweils eine Aufgabe* (Das Konzept von »[Microservices](https://de.wikipedia.org/wiki/Microservices)«) übernehmen. PhpMyAdmin etwa, mit dem du deine Datenbank verwalten kannst. Oder Mailhog, das alle E-Mails abfängt, die deine REDAXO-Installation versendet, und diese in einer Weboberfläche anzeigt.

Alle Container eines Projekts sind miteinander vernetzt, und du kannst beliebige weitere hinzufügen oder die bestehenden anpassen — zum Beispiel auch, um NGINX statt Apache zu verwenden oder MariaDB statt MySQL.

Deine Projektdaten bleiben dauerhaft auf deinem Computer erhalten und können wie gewohnt mittels Git versioniert werden, um die Arbeit im Team zu ermöglichen.


&nbsp;


## Technische Anforderungen

Du benötigst **zwei Bauteile**, um dieses Projekt zum Laufen zu bringen:

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_01.png)

Das erste Teil ist die **Konfiguration**, sozusagen der Bauplan, für die Docker-Container. Dieser Teil liegt dir bereits vor, wenn du dieses Git-Repository auf deinem Rechner gespeichert hast. Er besteht aus einer Datei `docker-compose.yml`, in der angegeben ist, welche Container mit welchen Einstellungen verwendet werden. Und er besteht weiterhin aus dem Ordner `docker/redaxo`, in dem wir das REDAXO-*Image* erweitern und zusammenbauen, bevor daraus ein *Container* gestartet wird. — Das wird später noch genauer erklärt!

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

Und dort startest du deine Container! 🚀

	$ docker-compose up -d


&nbsp;


## Images und Container

Was nun passiert, nachdem du `docker-compose up -d` aufgerufen hast:

### 1. Pull

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_02.png)

Docker erkennt, dass du in deiner `docker-compose.yml` verschiedene **Images** angegeben hast, z. B. für die Datenbank `image: mysql:8` oder für Mailhog `image: mailhog/mailhog`. Weil diese auf deinem Rechner noch nicht vorliegen, wird Docker sie nun für dich besorgen, und zwar aus dem [Docker Hub](https://hub.docker.com/). Das ist die offizielle *Registry* und damit sowas wie [npm](https://www.npmjs.com/) für JavaScript oder Composers [Packagist](https://packagist.org/) für PHP.

&nbsp;

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_03.png)

Docker erkennt außerdem, dass ganz oben in der `docker-compose.yml` unter »services« für »redaxo« kein Image angegeben ist. Stattdessen ist dort ein **Build-Pfad** hinterlegt: `build: ./docker/redaxo`. Das bedeutet, dass hier nicht einfach ein fertiges Image verwendet wird, sondern dass es in dem angegebenen Ordner ein `Dockerfile` mit der Bauanleitung eines Images geben muss!

&nbsp;

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_04.png)

Wenn du dir das besagte `Dockerfile` anschaust, findest du in der ersten Zeile ein `FROM friendsofredaxo/demo:base`. Das wiederum ist erneut der Hinweis auf ein Image aus dem Docker Hub, nämlich die **Demo-Website** von Friends Of REDAXO — Unser eigenes Image! 🙌  

Docker wird nun auch dieses Image runterladen (»pull«).

&nbsp;

### 2. Build

Alle Images aus dem Hub liegen nun lokal vor, und die vier aus unserer `docker-compose.yml` verwenden wir ohne Anpassungen so, wie sie aus dem Hub kommen. Sie sind bereits fertig *gebaut*, denn das Bauen und Verteilen ist die Aufgabe des Docker Hubs.

Das Image mit der REDAXO-Demo-Website könnten wir nun in gleicher Weise verwenden. Allerdings möchten wir es stattdessen innerhalb dieses Projekts weiter anpassen und um Features erweitern! Deshalb haben wir in der `docker-compose.yml` einen Build-Ordner angegeben, in dem ein `Dockerfile` — also eine Bauanleitung für ein Image — liegt.

In Zeile 1 wird also mittels `FROM` das Image mit der Demo-Website als Basis angegeben, so ist es Konvention innerhalb von Dockerfiles. Ab Zeile 2 folgen dann unsere Anpassungen:

* Es werden verschiedene **Konfigurationsdateien** kopiert, etwa für PHP oder Apache
* Es wird ein **SSL-Testzertifikat** angelegt
* Es werden weitere **Apache-Module** aktiviert
* Es wird **Blackfire** aktiviert (Ein Dienst zur Performance-Analyse)
* Am Ende wird der **Apache**-Webserver gestartet 

Aus dem Image der Demo-Website und unseren Anpassungen muss nun ein neues Image *gebaut* werden. Docker erkennt, dass dies noch nicht geschehen ist, und startet deshalb einen **Build**-Prozess.

🍄 *Zum Verständnis: Auch dieses neu entstandene Image könnten wir im Docker Hub veröffentlichen, z. B. als `friendsofredaxo/demo-base-extended` oder einem anderen Namen. Allerdings hat dieses Projekt nicht die Absicht, das zu tun, sondern wir möchten stattdessen die Anpassungen, die wir oben gemacht haben, für jedes unserer REDAXO-Projekte individuell vornehmen können. Deshalb verwenden wir nur die Demo-Website als Basis, stecken den Rest nach Bedarf dazu und bauen dann selbst!*

&nbsp;

### 3. Up (Start)

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_05.png)

Sobald alle Images fertig gebaut sind, können daraus lauffähige **Container** gestartet werden. Der Unterschied zwischen Images und Containern ist ein bisschen vergleichbar mit Klassen und Instanzen bei objekt-orientierter Programmierung (OOP): Das Image ist die *Klasse*, die alle notwendigen Ressourcen enthält. Container sind die *Instanzen*, die daraus erzeugt werden.

Es starten nun also folgende Container:

1. Ein Container mit **Mailhog**
2. Ein Container mit **Blackfire**
3. Ein Container mit **MySQL-Datenbank**
4. Ein Container mit **phpMyAdmin**
5. Ein Container mit **Apache-Webserver, PHP und REDAXO samt Demo-Website und unseren Anpassungen** 🤹

Weiterhin wird ein **gemeinsames Netzwerk** für diese Container eingerichtet (Zeile 1 im Screenshot oben). Das passiert automatisch, weil wir [docker-compose](https://docs.docker.com/compose/) zur *Orchestrierung* 🎻 mehrerer Container verwenden. Würden wir unserer Container einzeln verwalten mittels [docker](https://docs.docker.com/engine/reference/commandline/cli/), müssten wir auch das *Network* manuell anlegen.

Was übrigens die Benamung der Container und des Netzwerks angeht: Wir verzichten innerhalb dieses Projekts auf spezifische Angaben dazu. Dann nämlich verwendet Docker ganz pragmatisch den Namen des Ordners, in dem du das Setup ausführst, in diesem Fall `redaxo-mit-docker`. Es folgt die Bezeichnung der Services, etwa `db` oder `redaxo`, und eine fortlaufende Zahl, hier ist es `1`.

An dieser Stelle ist unser Setup nun fast vollständig. Alle *Services* sind in Betrieb, allerdings sind manche von ihnen noch nicht vollständig eingerichtet. Das folgt im nächsten — letzten! — Schritt.


&nbsp;


## Container: Einrichtung nach dem Start

Oftmals müssen Services noch weiter eingerichtet werden, sobald ihr Container gestartet worden ist. Für diesen Zweck gibt es im `Dockerfile` die beiden Werkzeuge [`CMD`](https://docs.docker.com/engine/reference/builder/#cmd) und [`ENTRYPOINT`](https://docs.docker.com/engine/reference/builder/#entrypoint). Deren Anwendung und Unterschiede sind nicht ganz einfach zu verstehen, aber es reicht hier zu wissen, dass sie benutzt werden, um Kommandos oder Skripte **innerhalb des Containers** auszuführen, wenn diese starten.

### Datenbank

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_06.png)

Wir haben zwar jetzt einen MySQL-Dienst am Start, aber noch keine **Datenbank**. Diese muss erst eingerichtet werden, und die notwendigen Angaben dazu stehen in der `docker-compose.yml`. Im Abschnitt `environment` haben wir Werte hinterlegt für den Nutzer, das Passwort und den Namen der Datenbank, die angelegt werden soll.

Welche **Environment-Variablen** benötigt werden und genutzt werden können, ist übrigens Sache der Images. Informationen dazu findest du jeweils im Docker Hub, für MySQL etwa hier: [https://hub.docker.com/_/mysql/](https://hub.docker.com/_/mysql/).

Der MySQL-Dienst beginnt nun also, eine frische Datenbank `redaxo` einzurichten, und das kann 30–60 Sekunden dauern.

&nbsp;

### REDAXO

Parallel zur Datenbank und den anderen Services (PhpMyAdmin, Mailhog, Blackfire) beginnt auch der REDAXO-Container…


&nbsp;


## Betrieb deiner Container

…


&nbsp;


## REDAXO-Images im Docker Hub

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
