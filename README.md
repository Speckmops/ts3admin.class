# ts3admin.class
The ts3admin.class is a powerful api for communication with Teamspeak 3 Servers from your website! Your creativity knows no bounds!

ts3admin.class.php written by Stefan 'par0noid' Zehnpfennig

You can use this software under the terms of the GNU General Public License v3.

Dev-Website: http://ts3admin.info

http://par0noid.info

## Installation via Composer

1.) Install composer requirements:

```
composer require par0noid/ts3admin
```

## Minimal script
```
<?php

#Include ts3admin composer library
require_once __DIR__ . '/vendor/autoload.php';
use par0noid\ts3admin\ts3admin;

#build a new ts3admin object
$tsAdmin = new ts3admin('hostname', <queryport>);

#login as serveradmin
$tsAdmin->login('username', 'password');

#select teamspeakserver
$tsAdmin->selectServer(<ts3_server_port>);

#get clientlist
$clients = $tsAdmin->clientList();
```