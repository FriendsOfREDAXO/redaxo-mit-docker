# Get Involved

Information about how this package can be maintained and extended.

## Edit the REDAXO Version

1. Edit the __ENV-Variables__.

    In `/docker/php-apache/Dockerfile`:

    ```dockerfile
    # set ENVs
    ENV REDAXO_VERSION=5.3.0 REDAXO_SHA=c1d3dcc2401b39cd9d32ea7de2ea358683ce81ceb8a5845f7a79937adcd5048e
    ```

    :point_right: _The checksum of a REDAXO package you get in the console for example on this way:_

        $ curl -Ls https://github.com/redaxo/redaxo/releases/download/5.3.0/redaxo_5.3.0.zip | shasum

2. Check if `docker-redaxo.php` needs to be adjusted. Inside are the installation routines for REDAXO and Addons, some of which have been adapted from Core, Installer Add-on, and Backup Add-on.

3. Check if `default.config.yml` needs to be adjusted.

## Adjust the demos

The configuration of the demos is located in `/docker/php-apache/demos.yml`.

1. __Download of the AddOns__

     You can find information about which AddOns requires a demo in the `package.yml` of the respective demo. Note that REDAXO already brings some add-ons. You just have to install the add-ons that do not already exist.
    
    Also, remember to list the demo itself as an add-on for download. Best right at the beginning of the list.
    
    :point_right: _The number for `file` is best found out by looking in the installer for the add-on. It is then in the URL._

2. __Activation of addons and plugins__

    The order of activation is very relevant because AddOns can have dependencies on each other! For example, the `phpmailer` is used by some AddOns, so it should be high on the list. The same applies to `yform`.
    
    At least, activate the Demo-Addon

3. __Database-Import__

    Check which SQL dumps have to be imported from the demo add-on.

4. __File-Import__

    Check which files need to be imported from the demo add-on.
