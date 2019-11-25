# Web of Czech PHP Community

[![Build Status](https://img.shields.io/travis/pehapkari/pehapkari.cz/master.svg?style=flat-square)](https://travis-ci.org/pehapkari/pehapkari.cz)

We're family of PHP developers from the Czech Republic, learning from each other on meetups and trainings.
We meet once a month in Prague, Brno and less often 4 other cities.

**This website is deployed to [pehapkari.cz](https://pehapkari.cz/).**

## Install

```bash
git clone git@github.com:pehapkari/pehapkari.cz.git

# install PHP dependencies
composer install

# copy `.env` as `.env.local` and complete variables.

# create database
bin/console doctrine:schema:create

# if you change entities later, update the database
bin/console doctrine:schema:update --dump-sql --force

# dump css and js from all bundles
bin/console assets:install --env=prod --no-debug

# install NPM dependencies
yarn install

# build assets
yarn encore dev --watch

# final step - run the website
php -S localhost:8000 -t public
```

Open [localhost:8000](http://localhost:8000) to see if it worked!

## Run via Docker

This is example for running the project locally:

1) Run `bin/run-from-docker.sh`
2) Enjoy :-)

**Project** is available on [localhost:8080](http://localhost:8080)
**DB Adminer** is available on [localhost:8081](http://localhost:8081) <small>(default credentials: server: mysql, user: root, password: root)</small>

*In some rare scenarios you might want to tweak `docker-compose.yml` file for your needs.*

## Deploy

- This repository is mirrored to [gitlab.com/pehapkari/pehapkari.cz](https://gitlab.com/pehapkari/pehapkari.cz/)
- [CI Pipeline](https://gitlab.com/pehapkari/pehapkari.cz/pipelines) is run on Gitlab CI
- When everything passes, it's deployed to our server in a Docker container

## Thank You

Our deploy from merge to production takes only 6 minutes thanks to [Jan Mikeš](https://janmikes.cz/). If you need CI-ready, Gitlab, Docker and DigitalOcean fully automated deploy, let him know.
