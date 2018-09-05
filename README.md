# Important Commands

## 1. Install

You do this only once:

```
# create git repository
git init

# add link to repository - first https, than ssh to modify the code
git remote add origin git@github.com:TomasVotruba/open-project.git

# download project here
git pull

# turn code  
git checkout master

# install other packages - Symfony, Doctrine...
composer install
```

## 2. Run

Run web in browser:

```bash
projects/open-real-estate/bin/console server:run
```

Open in browser to see website:

[localhost:8000](http://localhost:8000)

<br>

Update database after changing entities:

```bash
projects/open-real-estate/bin/console doctrine:schema:update --dump-sql --force
```

<br>

@todo

Update main `composer.json` from project ones:

```bash
vendor/bin/monorepo-builder merge
vendor/bin/monorepo-builder validate
```
