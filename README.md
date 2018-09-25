# simplesamlphp-module-updater
> A SimpleSAMLphp module adding support for configuration backups and update SimpleSAMLphp package.

## Installation

Installation can be as easy as executing:

    composer require rediris-es/simplesamlphp-module-updater
    
## Configuration

### Configure the module

Copy the template file to the config directory:

    cp modules/updater/config-template/updater_config.php config/

and edit it. The options are self explained.

### Create the backups directory

Create the path that has been configured with apache:apache owner and read and write permissions.
