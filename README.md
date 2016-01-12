# Postit Board Frontend

## Installation

### Requirements

Needs PHP >= 5.5.

### Protocole

First, you have to clone project from git repositiory :

```
$ git clone https://github.com/aixiandfriends/postit-board-front.git
```

Run [composer](https://getcomposer.org/download/) installer, from your new project directory :
```
$ curl -sS https://getcomposer.org/installer | php
$ composer install
```

Run [NPM](https://docs.npmjs.com/cli/install) installation command :
```
$ npm install
```

Run [Bower](http://bower.io/) dependencies installation command :
```
$ ./node_modules/.bin/bower install
```

Generate CSS files from Less files :
```
$ ./node_modules/.bin/lessc web/bower_components/bootstrap/less/bootstrap.less web/public/css/bootstrap.css
$ ./node_modules/.bin/gulp less
```
