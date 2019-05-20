# Web of Czech PHP Community

We're family of PHP developers from the Czech Republic, learning from each other on meetups and trainings.
We meet once a month in Prague, Brno and less often 4 other cities.

**This website is deployed to [pehapkari.cz](https://pehapkari.cz/).**

## Install

```bash
git clone git@github.com:pehapkari/pehapkari.cz.git

# install PHP dependencies
composer install

# rename `.env.dist` to `.env` and complete variables.

# create database
bin/console doctrine:schema:create

# if you change entities later, update the database
bin/console doctrine:schema:update --dump-sql --force

# dump css and js from all bundles
bin/console assets:install --env=prod --no-debug

# final step - run the website
bin/console server:run
```

Open [localhost:8000](http://localhost:8000) to see if it worked!

## Run via Docker

This is example for running the project locally:

1) Run `bin/run-from-docker.sh`
2) Enjoy :-)

**Project** is available on [localhost:8080](http://localhost:8080)  
**DB Adminer** is available on [localhost:8081](http://localhost:8081) <small>(default credentials: server: mysql, user: root, password: root)</small>

*In some rare scenarios you might want to tweak `docker-compose.yml` file for your needs.*

## Thank You

Our deploy from merge to production takes only 6 minutes thanks to [Jan Mikeš](https://janmikes.cz/). If you need CI-ready, Gitlab, Docker and DigitalOcean fully automated deploy, let him know.
