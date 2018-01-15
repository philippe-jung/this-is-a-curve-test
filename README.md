## Prerequisites:
- PHP7 + apache
- composer 

## Example of install on linux:
### Clone and initialise:
```
git clone git@github.com:philippe-jung/this-is-a-test.git
cd this-is-a-test
composer install
```

### Setup apache:
Create a new virtual host
```
sudo pico /etc/apache2/sites-available/curve.local.conf
```
And add the following content:
```
<VirtualHost *:80>
    ServerName curve.local
    DocumentRoot /home/snatch/dev/curve/src/web

    <Directory />
        Options Indexes FollowSymLinks MultiViews
        Require all granted
        AllowOverride All
    </Directory>
</VirtualHost>
```

Enable the site
```
sudo a2ensite curve.local
```

Add the domain name to your /etc/hosts file
```
127.0.0.1       curve.local
```

## Usage
The service is now accessible via "http://curve.local/api.php/distance?user1=jim&user2=dan"

The tests are launched that way:
```
cd tests
phpunit
```

## Conception notes
This exercise uses a very light framework that I have created previously.
This framework has flaws (the routing mechanism is quite ugly), but it allows to implement/connect to APIs in a simple and easy way.
 
The relevant pieces of code for the exercise will be found in the following directories:
- src/module/Service/Distance
- tests/Service

The configuration is held in src/conf/config.php. This is the file you want to edit to:
- change "curve.local" to any other domain in the API calls.
- change the mockup data (note that the tests rely on the initial data, so changing them will most likely break them!)

The mockup Github data is retrieved via 2 services:
- src/module/Service/Repos
- src/module/Service/Users
These are used in src/module/Service/Distance/Tree/GithubHelper.php

## About the algorithm used
I have gone the linked list way to build a tree of connections. This allows to work with an unlimited number of connections, and to easily build the path for any given element.
The idea is to build a tree of connection level by level (the distance of search is increased on each iteration).
Whenever we find the desired user, we can stop the process.

In order to maintain good performances, a cache mechanism is used at the GithubHelper level, in order to request the same data only once.
Also, as building an indefinite depth tree can quickly become *very* resource greedy, a max depth constant is used to limit that.



