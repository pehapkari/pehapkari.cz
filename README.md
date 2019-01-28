# Open Trainings

Open and free platform for organizing trainings. 

## Setup

We cover all you need to organize a training, with a trainer and attendees:

## Instal

```bash
git clone ...
composer install
```

Then rename `.env.dist` to `.env` and complete variables:

```bash
# create database
bin/console doctrine:schema:create 

# dump css and js from all bundles
bin/console assets:install --env=prod --no-debug

# install adminer
composer adminer-install
```

## Run

```bash
bin/console server:run
```

Clear cache after any config change - e.g. security

```bash
php bin/console cache:clear
```

<br>

Update database after changing entities:

```bash
bin/console doctrine:schema:update --dump-sql --force
```

## Run via docker

This is example for running open-training project locally:

1) Run `bin/run-from-docker.sh`
2) Enjoy :-) project is available on [localhost:8000](http://localhost:8000)

*In some rare scenarios you might want to tweak `docker-compose.yml` file for your needs.*
