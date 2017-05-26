#!/bin/bash
set -e

# Checks whether a directory contains any nonhidden files.
# usage: if isempty "$HOME"; then echo "Welcome home"; fi
#
isempty() {
    for _ief in $1/*; do
        if [ -e "$_ief" ]; then
            return 1
        fi
    done
    return 0
}

# extract REDAXO to target folder, but only if it’s empty
if isempty "$PWD"; then
    unzip -oq /tmp/redaxo5.zip
    rm -f /tmp/redaxo5.zip
    echo >&2 "REDAXO has been successfully copied to $PWD"
else
    echo >&2 "WARNING: $PWD is not empty! Skip REDAXO setup."
fi

exec "$@"