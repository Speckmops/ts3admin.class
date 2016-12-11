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

//Include ts3admin composer library
require_once __DIR__ . '/vendor/autoload.php';
use par0noid\ts3admin\ts3admin;

//build a new ts3admin object
$tsAdmin = new ts3admin('tsserver.cc', 10011);

if($tsAdmin->getElement('success', $tsAdmin->connect())) {
    //login as serveradmin
    $tsAdmin->login('serveradmin', 'R+oAOt9D');

    //select teamspeakserver
    $tsAdmin->selectServer(9987);

    //get clientlist
    $clients = $tsAdmin->clientList();

    echo count($clients['data']) . ' clients on selected server<br><br>';

    //print clients to browser
    foreach($clients['data'] as $client) {
        echo $client['client_nickname'] . '<br>';
    }
} else {
    echo 'Connection could not be established.';
}
```