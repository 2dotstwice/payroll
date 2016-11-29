## Payroll application

# phpdocker
Using phpdocker config generated, `docker-compose.yml` and `Vagrantfile` moved
to project root and config edited to match path.

Run `docker-compose up` to start docker containers, `docker-compose up -d` to
run in background.

`./phpcli` will run any php inside a docker container linked with the
mysql-container, and startup the other containers if they are not running
already.

Webapp will be accessable at http://localhost:8080, this port can be edited
in `docker-compose.yml`.

**Vagrant** makes the app accesable at http://payroll.dev/

`./vendor/bin/phing` to run all tests.
