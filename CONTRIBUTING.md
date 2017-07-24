# Mitmachen

Infos darüber, wie dieses Paket gepflegt und erweitert werden kann.

## Die REDAXO-Version anpassen

1. Die __ENV-Variablen__ anpassen.

    In `/docker/php-apache/Dockerfile`:

    ```dockerfile
    # set ENVs
    ENV REDAXO_VERSION=5.3.0 REDAXO_SHA=c1d3dcc2401b39cd9d32ea7de2ea358683ce81ceb8a5845f7a79937adcd5048e
    ```

    :point_right: _Die Prüfsumme eines REDAXO-Pakets erhälst du in der Konsole z. B. auf diesem Weg:_

        $ curl -Ls https://github.com/redaxo/redaxo/releases/download/5.3.0/redaxo_5.3.0.zip | shasum

2. Prüfe, ob `docker-redaxo.php` angepasst werden muss. Darin befindet sich die Installationsroutinen für REDAXO und Addons, die teilweise vom Core, vom Installer-AddOn und vom Backup-AddOn adaptiert wurden.

3. Prüfe, ob `default.config.yml` angepasst werden muss.

## Die Demos anpassen

Die Konfiguration der Demos befindet sich in `/docker/php-apache/demos.yml`.

1. Download von AddOns

    Eine Info darüber, welche AddOns eine Demo benötigt, findest du in der `package.yml` der jeweiligen Demo. Beachte, dass REDAXO manche AddOns schon mitbringt. Du musst nur die AddOns nachträglich installieren, die nicht schon vorhanden sind.
    
    Denke auch dran, die jeweilige Demo selbst als AddOn zum Download aufzulisten. Am besten gleich am Anfang der Liste.
    
    :point_right: _Die Nummer für `file` findest du am besten heraus, indem du im Installer nach dem AddOn suchst. Sie befindet sich dann in der URL._

2. Aktivierung von Addons und Plugins

    Die Reihenfolge der Aktivierung ist sehr relevant, weil AddOns Abhängigkeiten untereinander haben können! Beispielsweise wird der `phpmailer` von einigen AddOns verwendet, so dass er weit oben in der Liste stehen sollte. Gleiches gilt für `yform`.
    
    Aktiviere das Demo-Addon zum Schluss!

3. Datenbank-Import

    Prüfe, welche SQL-Dumps vom Demo-AddOn importiert werden müssen.

4. Datei-Import

    Prüfe, welche Dateien vom Demo-AddOn importiert werden müssen.
