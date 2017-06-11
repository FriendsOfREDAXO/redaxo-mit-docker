# Mitmachen

Infos darüber, wie dieses Paket gepflegt und erweitert werden kann.

## Die REDAXO-Version anpassen

1. Die __ENV-Variablen__ anpassen.

    In `/docker/php-apache/Dockerfile`:

    ```dockerfile
    # set ENVs
    ENV REDAXO_VERSION 5.3.0
    ENV REDAXO_SHA c1d3dcc2401b39cd9d32ea7de2ea358683ce81ceb8a5845f7a79937adcd5048e
    ```

    :point_right: _Die Prüfsumme eines REDAXO-Pakets erhälst du in der Konsole z. B. auf diesem Weg:_

        $ curl -Ls https://github.com/redaxo/redaxo/releases/download/5.3.0/redaxo_5.3.0.zip | shasum

2. Das __GitHub-Package__ anpassen, das runtergeladen wird.

    In `/docker/php-apache/Dockerfile`:

    ```dockerfile
    # fetch REDAXO and validate checksum
    RUN set -e; \
        curl -Ls -o /tmp/redaxo5.zip https://github.com/redaxo/redaxo/releases/download/5.3.0/redaxo_5.3.0.zip; \
        echo "$REDAXO_SHA */tmp/redaxo5.zip" | shasum -c -a 256;
    ```

3. Prüfen, ob `default.config.yml` angepasst werden muss.

4. Prüfen, ob `redaxo.setup.php` angepasst werden muss.
