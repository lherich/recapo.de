# recapo.de
A webapplication for usability testing. Recapo offers an abstracted and partial formated test environment for reverse card sorting tests. These tests are also know as:
* Tree tresting
* Card based classification evaluation
* Inverse card sorting
* Reverse card lookup

### System requirements

* web server with php support
* PHP >= 5.0
* MySQL server >= 5.5.0 for results in seconds
* MySQL server >= 5.6.4 for results in mikroseconds
                  recapo will use fractional seconds, which are available since version 5.6.4, see [mysql.com](http://dev.mysql.com/doc/refman/5.6/en/fractional-seconds.html)

### Installation

Please be aware that there is no documentation for the installation. The files may be self-explanatory. See [./install/*](install/) and check ./web/index.php.

### ToDo
* Refactor ./app/ and ./web/ and add documentation for the content of both folders.
* Add Composer, Load libraries via Composer, Upgrade libraries.
* Add [phpdotenv](https://github.com/vlucas/phpdotenv).
* Add a dependencies for JavaScript libraries.
* Add a docker-compose.yml for local development.
* Add tests and CI support.

### Changelog
Please see [CHANGELOG.md](CHANGELOG.md).

### Author
Recapo was created in 2014 by Lucas Herich <info@recapo.de>.

### License
Recapo is released under the MIT public license.
Please see [LICENSE](LICENSE).
