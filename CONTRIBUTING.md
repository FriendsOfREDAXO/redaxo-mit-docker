# Get Involved

Information about how this package can be maintained and extended.

## Edit the REDAXO Version

1. Edit the __ENV variables__.

    In `/docker/php-apache/Dockerfile`:

    ```dockerfile
    # set ENVs
    ENV REDAXO_VERSION=5.3.0 REDAXO_SHA=c1d3dcc2401b39cd9d32ea7de2ea358683ce81ceb8a5845f7a79937adcd5048e
    ```

    :point_right: _You can find out the checksum of a REDAXO package in the console e.g. like this:_

        $ curl -Ls https://github.com/redaxo/redaxo/releases/download/5.3.0/redaxo_5.3.0.zip | shasum

2. Check if `docker-redaxo.php` needs to be updated. It contains installation routines for REDAXO and addOns, which have been partially adapted from the core, the installer addOn and the backup addOn.

3. Check if `default.config.yml` needs to be updated.

## Adjust the demos

The configuration of the demos is located in `/docker/php-apache/demos.yml`.

1. __Download of addOns__

     You can find information about which addOns are required for a demo in the `package.yml` of the respective demo. Note that REDAXO already brings some addOns. You only have to install the addOns that are not already available.
    
    Also, remember to list the demo itself as an addOn for download. Best right at the beginning of the list.
    
    :point_right: _The best way to find out the number for `file` is to search for the addOn in the installer. It will be located in the URL._

2. __Activation of addOns and plugIns__

    The order of activation is important because addOns may depend on each other! For example, since `phpmailer` is required by some addOns, it should be placed high up in the list. The same applies to `yform`.
    
    The demo addOn should be the last addOn you activate!

3. __Database import__

    Check which SQL dumps have to be imported from the demo add-on.

4. __File-Import__

    Check which files need to be imported from the demo adOn.
