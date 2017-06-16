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

# extract REDAXO + default config to target folder, but only if it’s empty
if isempty "$PWD"; then

    # extract redaxo package
    unzip -oq /tmp/redaxo5.zip
    rm -f /tmp/redaxo5.zip
    echo >&2 "REDAXO has been successfully copied to $PWD"

    # copy default config
    cp -f /tmp/default.config.yml ./redaxo/src/core/
    rm -f /tmp/default.config.yml
    echo >&2 "default.config.yml copied to $PWD/redaxo/src/core/"

    # copy setup script
    cp -f /tmp/redaxo.setup.php ./redaxo/
    rm -f /tmp/redaxo.setup.php
    echo >&2 "redaxo.setup.php copied to $PWD/redaxo/"

    # run setup script
    cd redaxo && php redaxo.setup.php --user="$REDAXO_USER" --password="$REDAXO_PASSWORD"
    rm -f redaxo.setup.php
else
    echo >&2 "WARNING: $PWD is not empty! Skip REDAXO setup."
fi

exec "$@"
