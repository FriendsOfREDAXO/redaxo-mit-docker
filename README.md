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


<a name="toc"></a>
<details>
<summary><b>Inhaltsverzeichnis aufklappen</b></summary>

- [Einleitung](#einleitung)
- [Technische Anforderungen](#anforderungen)
- [Installation](#installation)
	- [Schritt 1: Bereite deinen Projektordner vor](#installation-1)
	- [Schritt 2: Richte Docker auf deinem System ein](#installation-2)
	- [Schritt 3: Starte das Projekt!](#installation-3)
- [Images und Container](#images-und-container)
	- [1. Pull](#images-und-container-1)
	- [2. Build](#images-und-container-2)
	- [3. Up (Start)](#images-und-container-3)
- [Container: Einrichtung nach dem Start](#container-einrichtung)
	- [Datenbank](#container-einrichtung-datenbank)
	- [REDAXO](#container-einrichtung-redaxo)
- [Das Dashboard (GUI)](#dashboard)
- [Gängige Konsolen-Kommandos](#konsole)
	- [Container starten (`up`)](#konsole-up)
	- [Container stoppen (`stop`)](#konsole-stop)
	- [Container stoppen und verwerfen (`down`)](#konsole-down)
	- [Updates holen (`pull`)](#konsole-pull)
	- [Images bauen (`build`)](#konsole-build)
	- [Kommandos im Container ausführen (`exec`)](#konsole-exec)
	- [Aufräumen (`prune`)](#konsole-prune)
- [Daten speichern und versionieren](#speichern-und-versionieren)
	- [*Bind Mounts:* Synchronisierte Daten](#speichern-und-versionieren-bind-mounts)
	- [Versionierung mittels Git](#speichern-und-versionieren-git)
- [Konfiguration anpassen](#konfiguration)
	- [PHP-Konfiguration anpassen](#konfiguration-php)
	- [Apache-Konfiguration anpassen](#konfiguration-apache)
	- [NGINX statt Apache nutzen](#konfiguration-nginx)
	- [MariaDB statt MySQL nutzen](#konfiguration-mariadb)
	- [Nicht benötigte Dienste deaktivieren](#konfiguration-dienste-deaktivieren)
- [Dokumentation und ergänzende Links](#doku-und-links)
- [Häufige Fragen](#faq)
- [Hilfe und Support](#support)

</details>


<a name="einleitung"></a>
## Einleitung

Dieses Setup stellt dir und deinem Team für jedes eurer REDAXO-Projekte eine flexible Entwicklungsumgebung (Apache, PHP, MySQL) bereit. Dafür kommt [Docker](https://de.wikipedia.org/wiki/Docker_(Software)) zum Einsatz, und das funktioniert so ähnlich wie eine *Virtuelle Maschine*, benötigt aber viel weniger Ressourcen.

Docker startet für dein Projekt einen *Container*, der einen **Webserver** enthält, und in dem ein frisches REDAXO samt Demo-Website installiert ist. Der gesamte Code wird dabei jedoch auf deinem Computer abgelegt und mit dem Container synchronisiert.

Ein weiterer Docker-Container enthält die **Datenbank**. Auch deren Inhalte werden auf deinem Computer gespeichert und synchronisiert.

Je nach Bedarf kommen weitere Container hinzu, die *jeweils eine Aufgabe* (Das Konzept von »[Microservices](https://de.wikipedia.org/wiki/Microservices)«) übernehmen. PhpMyAdmin etwa, mit dem du deine Datenbank verwalten kannst. Oder Mailhog, das alle E-Mails abfängt, die deine REDAXO-Installation versendet, und diese in einer Weboberfläche anzeigt.

Alle Container eines Projekts sind miteinander vernetzt, und du kannst beliebige weitere hinzufügen oder die bestehenden anpassen — zum Beispiel auch, um NGINX statt Apache zu verwenden oder MariaDB statt MySQL.

Deine Projektdaten bleiben dauerhaft auf deinem Computer erhalten und können wie gewohnt mittels Git versioniert werden, um die Arbeit im Team zu ermöglichen.


&nbsp;


<a name="anforderungen"></a>
## Technische Anforderungen

Du benötigst **zwei Bauteile**, um dieses Projekt zum Laufen zu bringen:

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_01.png)

Das erste Teil ist die **Konfiguration**, sozusagen der Bauplan, für die Docker-Container. Dieser Teil liegt dir bereits vor, wenn du dieses Git-Repository auf deinem Rechner gespeichert hast. Er besteht aus einer Datei `docker-compose.yml`, in der angegeben ist, welche Container mit welchen Einstellungen verwendet werden. Und er besteht weiterhin aus dem Ordner `docker/redaxo`, in dem wir das REDAXO-*Image* erweitern und zusammenbauen, bevor daraus ein *Container* gestartet wird. — Das wird später noch genauer erklärt!

Das zweite Bauteil, was du benötigst, ist **Docker** selbst, sozusagen die Maschine in unserem Setup. Das Programm muss auf deinem Computer installiert werden. Es kann kostenlos für alle gängigen Systeme (Windows, Mac, Linux) runtergeladen werden.


&nbsp;


<a name="installation"></a>
## Installation

Wenn du erstmalig mit Docker arbeitest, sind dies die 3 Schritte, die du ausführen musst, um das Projekt zum Laufen zu bringen:

<a name="installation-1"></a>
### Schritt 1: Bereite deinen Projektordner vor

Vielleicht hast du diesen Schritt bereits erledigt, wenn du dies liest. Falls nicht: Lade den Inhalt dieses Git-Repos runter und speichere ihn in einen passenden Ordner auf deinem Computer.

Tipp: Es bietet sich an, einen gemeinsamen »Projektordner« anzulegen, in dem du deine Projekte jeweils in Unterordnern ablegst.

<a name="installation-2"></a>
### Schritt 2: Richte Docker auf deinem System ein

Lade [Docker Desktop](https://www.docker.com/products/docker-desktop) runter, wenn du Windows oder Mac benutzt. Linux-Nutzer benötigen die [Docker-Engine](https://hub.docker.com/search?q=&type=edition&offering=community&operating_system=linux) als kostenlose Community-Edition.

Nach der Installation startest du das Programm. An den Einstellungen muss normalerweise nichts geändert werden, allerdings musst du den Projektordner für Docker freigeben (Preferences > Resources > **File Sharing**).

<a name="installation-3"></a>
### Schritt 3: Starte das Projekt!

Docker bedienst du am besten auf der **Kommandozeile**. Zwar kannst du auch im grafischen Dashboard, das du eben für die Einstellungen aufgerufen hast, Container starten und stoppen. Besser ist jedoch, du gewöhnst dich von Anfang an an die Kommandozeile.

Öffne also die Kommandozeile und wechsle in den Ordner deines Projekts (etwa so: `cd /projekte/redaxo-mit-docker`).

Und dort startest du deine Container! 🚀

	$ docker-compose up -d


&nbsp;


<a name="images-und-container"></a>
## Images und Container

Was nun passiert, nachdem du `docker-compose up -d` aufgerufen hast:

<a name="images-und-container-1"></a>
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

<a name="images-und-container-2"></a>
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

<a name="images-und-container-3"></a>
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


<a name="container-einrichtung"></a>
## Container: Einrichtung nach dem Start

Oftmals müssen Services noch weiter eingerichtet werden, sobald ihr Container gestartet worden ist. Für diesen Zweck gibt es im `Dockerfile` die beiden Werkzeuge [`CMD`](https://docs.docker.com/engine/reference/builder/#cmd) und [`ENTRYPOINT`](https://docs.docker.com/engine/reference/builder/#entrypoint). Deren Anwendung und Unterschiede sind nicht ganz einfach zu verstehen, aber es reicht hier zu wissen, dass sie benutzt werden, um Kommandos oder Skripte **innerhalb des Containers** auszuführen, wenn diese starten.

🍄 *Zum Verständnis: In deiner Konsole passiert jetzt nichts mehr. Die ist fertig, und der Cursor blinkt. Alle nachfolgenden Prozesse finden innerhalb der Container statt, und es wird später noch erkärt, wie du damit umgehst!*


<a name="container-einrichtung-datenbank"></a>
### Datenbank

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_06.png)

Wir haben zwar jetzt einen MySQL-Dienst am Start, aber noch keine **Datenbank**. Diese muss erst eingerichtet werden, und die notwendigen Angaben dazu stehen in der `docker-compose.yml`. Im Abschnitt `environment` haben wir Werte hinterlegt für den Nutzer, das Passwort und den Namen der Datenbank, die angelegt werden soll.

Welche **Environment-Variablen** benötigt werden und genutzt werden können, ist übrigens Sache der Images. Informationen dazu findest du jeweils im Docker Hub, für MySQL etwa hier: [https://hub.docker.com/_/mysql/](https://hub.docker.com/_/mysql/).

Der MySQL-Dienst beginnt nun also, eine frische Datenbank `redaxo` einzurichten, und das kann 30–60 Sekunden dauern.

&nbsp;

<a name="container-einrichtung-redaxo"></a>
### REDAXO

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_07.png)

Parallel zur Datenbank und den anderen Services (PhpMyAdmin, Mailhog, Blackfire) beginnt auch der REDAXO-Container mit der Einrichtung.

In der `docker-compose.yml` befinden sich einige **Environment-Variablen** für REDAXO, viel mehr als eben für die Datenbank. Informationen dazu findest du wieder im Docker Hub auf der Seite des REDAXO-Images: [https://hub.docker.com/r/friendsofredaxo/redaxo](https://hub.docker.com/r/friendsofredaxo/redaxo).

Ohne zu viele Details anzubringen, passiert nun etwa folgendes — vorher tief Luft holen:

1. Das von uns verwendete **Image mit der Demo-Website** nutzt keinen eigenen `ENTRYPOINT`, führt also keine Kommandos oder Skripte beim Start des Containers aus.
2. Das Image benutzt als Basis das **REDAXO-Image** — Zeile 1 im Dockerfile: `FROM friendsofredaxo/redaxo:5` —, und das nutzt als `ENTRYPOINT` ein Shell-Skript namens [`docker-entrypoint.sh`](https://github.com/FriendsOfREDAXO/docker-redaxo/blob/master/php7.4/apache/docker-entrypoint.sh). Dieses Skript wird nun ausgeführt.
3. Das Skript prüft als erstes, ob das Root-Verzeichnis des Webservers leer ist. Ist dies der Fall, wird **REDAXO hinein kopiert**.
4. Anschließend prüft es in einer Schleife mit 5 Sekunden Abstand immer wieder, ob die **Datenbank fertig eingerichtet** ist.
5. Sobald die Datenbank bereit steht, wird **REDAXO installiert**. Mit Hilfe der Konsolen-Kommandos übrigens, die REDAXO seit 5.9 anbietet. 🤖
6. Achtung, [Inception](https://de.wikipedia.org/wiki/Inception): Das Skript versucht nun, ein anderes Shell-Skript namens `custom-setup.sh` auszuführen, sofern dies vorhanden ist. In unserem Fall ist es das, denn das Image der Demo-Website (!) hat es beim Bauen an die richtige Stelle kopiert.
7. Nun wird also [`custom-setup.sh`](https://github.com/FriendsOfREDAXO/docker-demos/blob/master/base/custom-setup.sh) ausgeführt, und das benutzt wiederum REDAXOs Konsolen-Kommandos, um das Basis-Demo-AddOn aus dem Installer zu laden, es zu installieren und schließlich **die Demo zu installieren**.
8. Demo ist fertig, REDAXO ist fertig. Jetzt wird noch der **Apache gestartet** und…

**BÄMM!** 🚀

Vor dir steht, wenn alles gut gegangen ist, eine fertig eingerichtete Entwicklungsumgebung mit allem PiPaPo! Und während du gelesen hast, was alles passiert, hatte es genug Zeit, um wirklich zu passieren. Deshalb kannst du dir nun das Ergebnis im Browser anschauen:  

**[http://localhost:20080](http://localhost:20080)**


&nbsp;
---
&nbsp;


<a name="dashboard"></a>
## Das Dashboard (GUI)

Docker bringt eine **grafische Benutzeroberfläche** mit, das Dashboard. Dort siehst du alle Container, kannst sie starten oder stoppen, kannst dir deren Logs ausgeben lassen — sehr praktisch! —, kannst Einstellungen vornehmen und diverse Details aufrufen.

Die Dokumentation zum Dashboard findest du hier: [Windows](https://docs.docker.com/docker-for-windows/dashboard/), [Mac](https://docs.docker.com/docker-for-mac/dashboard/).

🍄 *Zum Verständnis: Wir haben weiter oben, als die Container erstmalig gestartet sind, die Logs nicht weiter verfolgt. Zukünftig solltest du das tun, denn die wichtigen Dinge passieren oftmals im Container. Das Dashboard ist ein praktisches Werkzeug.*

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_08.png)


<a name="konsole"></a>
## Gängige Konsolen-Kommandos

<a name="konsole-up"></a>
### Container starten (`up`)

	$ docker-compose up -d

Das `-d` (*detached mode*) ermöglicht, dass deine Container im Hintergrund laufen und nicht stoppen, wenn dein Konsolenprozess beendet wird.

Das Kommando `up` startet nicht nur die Container, sondern ist vielseitiger: Vorm Start werden noch nicht vorliegende Images aus dem Hub geholt (pull), noch nicht gebaute Images gebaut (build) und bereits gestartete Container neu gestartet (recreate), falls das aufgrund von Anpassungen notwendig ist.

Zu beachten ist allerdings, dass `up` nicht prüft, ob es Updates im Docker Hub gibt.


<a name="konsole-stop"></a>
### Container stoppen (`stop`)

	$ docker-compose stop

Stoppt die Container, so dass sie keine weiteren Systemressourcen benötigten. Sie behalten ihren aktuellen Zustand bei und laufen nahtlos weiter, wenn sie wieder (mittels `up`) gestartet werden.


<a name="konsole-down"></a>
### Container stoppen und verwerfen (`down`)

	$ docker-compose down

Beachte, dass dabei Daten verloren gehen, sofern sie nicht mit deinem Rechner synchronisiert werden! Synchronisiert werden in unserem Setup REDAXO im `html`-Ordner und die Datenbank im `db`-Ordner, diese Daten bleiben also dauerhaft erhalten. Solche Ordner nennt Docker übrigens *[bind mounts](https://docs.docker.com/storage/bind-mounts/)*.

Das Kommando `down` benötigst du in der Praxis eher selten, und du solltest es dir lieber *nicht* als Gegenteil von `up` merken, sondern stattdessen `stop` verwenden!


<a name="konsole-pull"></a>
### Updates holen (`pull`)

	$ docker-compose pull

Aktualisiert die Images für alle Services innerhalb der `docker-compose.yml`.

Leider werden Images im `Dockerfile` nicht beachtet. Das bedeutet für uns, dass das **Image mit der Demo-Website** nicht aktualisiert wird. Das müssen wir manuell erledigen, in diesem Fall nicht mittels `docker-compose`, sondern mittels `docker`:

	$ docker pull friendsofredaxo/demo:base

Es kommt aber noch hinzu, dass damit nicht automatisch auch das **REDAXO-Image** aktualisiert wird, auf das die Demo aufsetzt. Auch das müssen wir manuell pullen:

	$ docker pull friendsofredaxo/redaxo:5
	

<a name="konsole-build"></a>
### Images bauen (`build`)

	$ docker-compose build

Wenn du Anpassungen an einem Image vornimmst, konkret also, wenn du das `Dockerfile` oder Dateien innerhalb des Build-Ordners änderst, musst du das Image neu bauen, damit die Änderungen wirksam werden.


<a name="konsole-exec"></a>
### Kommandos im Container ausführen (`exec`)

	$ docker-compose exec redaxo /bin/bash

Dieses Zeile öffnet eine Bash Shell im `redaxo`-Container. Du kannst mittels `exec` auch andere Kommandos ausführen, aber die Shell ist das, was du vermutlich sehr häufig benötigen wirst, um z. B. REDAXO über die Konsole zu bedienen.


<a name="konsole-prune"></a>
### Aufräumen (`prune`)

	$ docker system prune

Docker benötigt viel Platz. Im Laufe der Zeit können sich einige Images oder vergessene Container auf deinem Rechner ansammeln, die nicht mehr benötigt werden. Dieses Kommando löscht alle Daten, die keinem Container mehr zugeordnet sind, und du kannst es bedenkenlos ausführen, um Platz zu schaffen.


&nbsp;


<a name="speichern-und-versionieren"></a>
## Daten speichern und versionieren

Sobald du dieses Setup erstmalig gestartet hast, wirst du zwei neue Ordner `db` und `html` vorfinden:

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/redaxo-mit-docker/assets/redaxo-mit-docker_v2_09.png)

<a name="speichern-und-versionieren-bind-mounts"></a>
### *Bind Mounts:* Synchronisierte Daten

Diese Ordner werden von Docker angelegt, weil wir in der `docker-compose.yml` sogenannte *[bind mounts](https://docs.docker.com/storage/bind-mounts/)* definieren, um Daten vom Host-System (dein Computer) in die Container zu *mounten*. Das bedeutet, dass die Inhalte dieser Ordner mit denen innerhalb der Container **synchronisiert** werden: Sobald Änderungen passieren, egal ob lokal oder im Container, werden diese unmittelbar an die jeweils andere Stelle durchgegeben.

Wir verwenden den `db`-Ordner, um darin die **Datenbank** zu speichern. Wir *persistieren* damit die Datenbank auf dem Host-System, halten sie also dauerhaft vor, auch wenn die Container mal entfernt werden sollten.

Der `html`-Ordner wird ins Root-Verzeichnis des Webservers *gemounted*, was konkret bedeutet: Er enthält **REDAXO**, das während des Setups automatisch von Docker installiert und mit der Website-Demo bestückt wird.

<a name="speichern-und-versionieren-git"></a>
### Versionierung mittels Git

Dein Projektordner enthält alle notwendigen Daten, die du zum Betrieb des Projekts benötigst:

1. REDAXO
2. Die Datenbank
3. Die Docker-Konfiguration, und damit also die komplette Serverumgebung! 🔥

Sinnvoll ist, **alles außer der Datenbank** im Git zu versionieren. Die Datenbank eignet sich nicht dafür, und sie enthält außerdem sensible Daten, die nicht in ein Repository wandern sollten.

In diesem Projekt ist bereits eine `.gitignore` enthalten, die du für die Praxis übernehmen kannst. Sie ignoriert den Datenbank-Ordner und auch ein paar Ressourcen im REDAXO-Ordner, die nicht versioniert werden sollten, etwa die Konfiguration (sensible Daten!), den Cache (nicht sinnvoll!) und den Media-Ordner (zu groß!).

Als Ergebnis, um das nochmal zu sagen, hast du ein Projekt-Repository, in dem alle relevanten Daten enthalten sind. Und falls ihr im Team arbeitet, benutzen alle das gleiche Setup!


&nbsp;


<a name="konfiguration"></a>
## Konfiguration anpassen

Ein paar Informationen darüber, wie du die Konfiguration deines Setups anpassen kannst:

<a name="konfiguration-php"></a>
### PHP-Konfiguration anpassen

…


<a name="konfiguration-apache"></a>
### Apache-Konfiguration anpassen

…


<a name="konfiguration-nginx"></a>
### NGINX statt Apache nutzen

…


<a name="konfiguration-mariadb"></a>
### MariaDB statt MySQL nutzen

…


<a name="konfiguration-dienste-deaktivieren"></a>
### Nicht benötigte Dienste deaktivieren

…


&nbsp;


<a name="doku-und-links"></a>
## Dokumentation und ergänzende Links

#### Dokumentation:

* Dokumentation: [Overview](https://docs.docker.com/engine/docker-overview/), [Glossary](https://docs.docker.com/glossary/)
* Docker Desktop: [Windows](https://docs.docker.com/docker-for-windows/), [Mac](https://docs.docker.com/docker-for-mac/)
* `docker` [CLI](https://docs.docker.com/engine/reference/commandline/cli/)
* `docker-compose` [CLI](https://docs.docker.com/compose/reference/overview/)
* `DOCKERFILE` [Reference](https://docs.docker.com/engine/reference/builder/)
* `docker-compose.yml` [Reference](https://docs.docker.com/compose/compose-file/)

#### REDAXO-Images:

* REDAXO: [Hub](https://hub.docker.com/r/friendsofredaxo/redaxo), [Git](https://github.com/FriendsOfREDAXO/docker-redaxo), [Recipes](https://github.com/FriendsOfREDAXO/docker-redaxo/tree/master/recipes)
* Demos: [Hub](https://hub.docker.com/r/friendsofredaxo/demo), [Git](https://github.com/FriendsOfREDAXO/docker-demos)

#### Nützliche Links:

* [How To Remove Docker Images, Containers, and Volumes](https://www.digitalocean.com/community/tutorials/how-to-remove-docker-images-containers-and-volumes): A Docker Cheat Sheet


&nbsp;


<a name="faq"></a>
## Häufige Fragen

#### Warum wird die Demo-Website verwendet und nicht einfach nur ein frisches REDAXO ohne Inhalte?

Aus drei Gründen:

1. Weil man dann schnell mal **Features testen oder Dinge ausprobieren kann**, ohne vorher selbst Inhalte erstellen zu müssen.
2. Weil mit der Demo-Website ein paar zusätzliche **Themen zu Docker erklärt** werden können, etwa `custom-setup.sh`.
3. Weil das Setup sehr **einfach angepasst** werden kann: Zeile 1 im Dockerfile ändern in `FROM friendsofredaxo/redaxo:5`, dann bekommst du beim nächsten Build REDAXO ohne Demo-Website.

#### Kann ich nicht mehrere REDAXO-Projekte mit Docker gleichzeitig laufen lassen?

Doch, das geht, allerdings musst du dann verschiedene Ports für deine Container verwenden, damit es keine Konflikte gibt. Einfacher — vom Verständnis — ist es, die gleichen Ports für alle Projekte zu verwenden und immer nur eines am Laufen zu haben.


&nbsp;


<a name="support"></a>
## Hilfe und Support

Falls du (weitere) Fragen hast oder Hilfe benötigst, kontakte uns gerne im **Slack-Chat**!  
Eine Einladung bekommst du hier: [https://redaxo.org/slack/](https://redaxo.org/slack/).
