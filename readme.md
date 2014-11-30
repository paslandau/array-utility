#array-utility
[![Build Status](https://travis-ci.org/paslandau/array-utility.svg?branch=master)](https://travis-ci.org/paslandau/array-utility)

Library to extend PHP core functions by common (missing) array functions

##Description
[todo]

##Requirements

- PHP >= 5.5

##Installation

The recommended way to install array-utility is through [Composer](http://getcomposer.org/).

    curl -sS https://getcomposer.org/installer | php

Next, update your project's composer.json file to include ArrayUtility:

    {
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/paslandau/array-utility.git"
            }
        ],
        "require": {
             "paslandau/array-utility": "~0"
        }
    }

After installing, you need to require Composer's autoloader:
```php

require 'vendor/autoload.php';
```