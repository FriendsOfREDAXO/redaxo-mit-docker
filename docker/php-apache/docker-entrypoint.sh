#!/bin/bash
set -e

# Checks whether a directory contains any nonhidden files.
# usage: `if isempty "$HOME"; then echo "Welcome home"; fi`
isempty() {
    for _ief in $1/*; do
        if [ -e "$_ief" ]; then
            return 1
        fi
    done
    return 0
}

if isempty "${PWD}"; then

    echo >&2 "👉 Prepare REDAXO setup..."

    # copy REDAXO to webroot
    cp -R /tmp/redaxo/src/. ./
    echo >&2 "✅ REDAXO has been successfully copied to ${PWD}"

    # copy default config
    cp -f /tmp/redaxo/default.config.yml ./redaxo/src/core/
    echo >&2 "✅ default.config.yml copied to ${PWD}/redaxo/src/core/"

    # copy demos config
    cp -f /tmp/redaxo/demos.yml ./redaxo/
    echo >&2 "✅ demos.yml copied to ${PWD}/redaxo/"

    # copy setup script
    cp -f /tmp/redaxo/docker-redaxo.php ./redaxo/
    echo >&2 "✅ docker-redaxo.php copied to ${PWD}/redaxo/"
    echo >&2 " "

    cd redaxo

    # install REDAXO
    echo >&2 "👉 Install REDAXO..."
    php docker-redaxo.php --user="$REDAXO_USER" --password="$REDAXO_PASSWORD"
    echo >&2 " "

    # install demo
    if [[ $REDAXO_DEMO ]]; then
        echo >&2 "👉 Install ${REDAXO_DEMO}..."
        php docker-redaxo.php --demo="$REDAXO_DEMO"
        echo >&2 " "
    fi

    # clean up
    rm -f docker-redaxo.php
    rm -f demos.yml
else
    echo >&2 "✋ WARNING: ${PWD} is not empty! Skip REDAXO setup."
    echo >&2 " "
fi

# clean up tmp folder
rm -rf /tmp/redaxo

# set chmod for sim linked addon paths and assets
chown -R www-data:www-data /redaxo/src/addons && chown -R www-data:www-data /redaxo/assets

# execute CMD
exec "$@"
