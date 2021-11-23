# url-shortener

### Repository
Project Repository (GitLab): https://github.com/alexanderkatkov/url-shortener

### URLS
#### Docs
http://localhost/api/docs
#### URLs Create, Get, Delete
http://localhost/api/urls
#### Example of shortcode (for https://www.google.com):
http://localhost/5f890849

### Local Installation
Project uses Docker for local environment and should be compatible with
any popular Linux distribution.
Current project is not tested on MacOS, but usually this config works smoothly in most of the cases.
Also you should use `Maker` for better experience.

All build and prepare operations are done with `make install` command.
It builds PHP image, and starts local server.

You can start and stop server with corresponding commands:
`make up` and `make stop`.

Dummy data can be generated with `make app-fixtures-load` command.

### Code Quality
To ensure basic code quality we're using these tools:
1. `phpstan/phpstan`
2. `squizlabs/php_codesniffer`
3. `php-parallel-lint/php-parallel-lint`

Before pushing code to repository local checks can be run with
`make app-lint` command.

### Testing
PhpUnit tests are started with `make app-test` command.
Also, PhpUnit is used to run basic tests on API endpoints.
There is possibility to create DB fixtures for tests, extending `App\Tests\AliceFixtureDependentTestCase`.

These tests are not sufficient though and require too much effort to create for a long run.
Should be replaced with full endpoints coverage using [Behat](https://docs.behat.org/en/latest/) in the future.
