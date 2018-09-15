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


Install assets

```bash
php projects/open-real-estate/bin/console assets:install --env=prod --no-debug
```

Install adminer

```
composer adminer-install
```


## 2. Database setup

1. Xampp - create databse
...

## 3. Run

Run web in browser:

```bash
php projects/open-real-estate/bin/console server:run
```

Open in browser to see website:

[localhost:8000](http://localhost:8000)

<br>

Update database after changing entities:

```bash
php projects/open-real-estate/bin/console doctrine:schema:update --dump-sql --force
```
