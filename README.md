# simplesamlphp-module-updater

A SimpleSAMLphp module adding support for backing up and updating SimpleSAMLphp

## Installation

Installation can be as easy as executing:

    composer require rediris-es/simplesamlphp-module-updater
    
## Configuration

### Configure the module

Just copy the template file to the config directory:

    cp modules/updater/config-template/updater_config.php config/

and edit it. The options in this file are self-explained.

Enable it [as usual in SimpleSAMLphp](https://simplesamlphp.org/docs/stable/simplesamlphp-modules#section_2). 

### Create the backups directory

Create the path that has been configured with the Apache process owner (usually `apache:apache`) and read and write permissions for that user.

### Usage

Module is listed in 'Configuration' tab
