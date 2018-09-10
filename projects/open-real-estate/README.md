# Open Real Estate

## Instal

```bash
git clone ...
cd open-trainings
composer update
```

Configure `php.ini`: 

```ini
upload_max_filesize = 20MB # at least
max_execution_time = 120
```

Then rename `.env.dist` to `.env` and complete variables:

```bash
# create database
bin/console doctrine:schema:create

# dump css and js from all bundles
bin/console assets:install --env=prod --no-debug

composer adminer-install
```

## Run

```bash
bin/console server:run
```

