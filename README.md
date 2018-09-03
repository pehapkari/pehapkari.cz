# Important Commands

Run web in browser:

```bash
projects/open-real-estate/bin/console server:run
```

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
