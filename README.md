# Recommendation #

[![Build Status](https://travis-ci.org/yosefda/recommendation.svg?branch=master)](https://travis-ci.org/yosefda/recommendation)

Movie recommendations.

#### Requirements ###

__PHP 7.1.*__ must be installed.


#### To work on the project ####

```
# clone the repo
git clone git@github.com:yosefda/recommendation.git .
 
# download dependencies
composer install
 
# run phpunit
vendor/bin/phpunit
```

#### To dist the application ####

```
# run make_dist script
sh make_dist.sh
 
# application is disted in dist/Recommendation.phar
# running the application e.g.
php dist/Recommendation.phar animation 12:00
```




