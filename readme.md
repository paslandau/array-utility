#ArrayUtility
[![Build Status](https://travis-ci.org/paslandau/ArrayUtility.svg?branch=master)](https://travis-ci.org/paslandau/ArrayUtility)

Library to extend PHP core functions by common (missing) array functions

##Description
[todo]

##Requirements

- PHP >= 5.5

##Installation

The recommended way to install ArrayUtility is through [Composer](http://getcomposer.org/).

    curl -sS https://getcomposer.org/installer | php

Next, update your project's composer.json file to include ArrayUtility:

    {
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/paslandau/ArrayUtility.git"
            }
        ],
        "require": {
             "paslandau/ArrayUtility": "~0"
        }
    }

After installing, you need to require Composer's autoloader:
```php

require 'vendor/autoload.php';
```