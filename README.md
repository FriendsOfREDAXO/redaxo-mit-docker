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

Das zweite Bauteil, was du benötigst, ist **Docker** selbst, sozusagen die Maschine in unserem Setup. Das Programm muss auf deinem Computer installiert werden. Es kann als »Community Edition« kostenlos für alle gängigen Systeme (Windows, Mac, Linux) runtergeladen werden.


&nbsp;


## Installation

Wenn du erstmalig mit Docker arbeitest, sind dies die 3 Schritte, die du ausführen musst, um das Projekt zum Laufen zu bringen:

### Schritt 1: Bereite deinen Projektordner vor

Vielleicht hast du diesen Schritt bereits erledigt, wenn du dies liest. Falls nicht: Lade den Inhalt dieses Git-Repos runter und speichere ihn in einen passenden Ordner auf deinem Computer.

Tipp: Es bietet sich an, einen gemeinsamen »Projektordner« anzulegen, in dem du deine Projekte jeweils in Unterordnern ablegst.

### Schritt 2: Richte Docker auf deinem System ein

…

### Schritt 3: Starte das Projekt!

…


&nbsp;


## Verwendung

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
