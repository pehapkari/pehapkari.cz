# Open Trainings

Open and free platform for organizing trainings. 

## Setup

We cover all you need to organize a training, with a trainer and attendees:

- [ ] create training
- [ ] create a new term for the training
- [ ] let people buy and attend a training
- [ ] automated invoicing
- [ ] automated emails to all parties
- [x] compute provision for the trainer and the organizer

## Instal

```bash
git clone ...
cd open-trainings
composer update
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
