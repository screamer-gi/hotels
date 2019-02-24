# hotels
Hotels test task

##Installation

- Run composer: `composer install`
- Create database `hotels` with username `hotels` and password `hotels`
- Load dump from `docs/dump.sql` or run migration: `vendor/bin/phinx migrate`
- Add test data: `vendor/bin/phinx seed:run`
- Add to `/etc/hosts` row `127.0.0.1 hotels.local`
- Open `http://hotels.local` in the browser 

### docker

- Add to `/etc/hosts` row `127.0.0.1 hotels.local`
- run docker compose commands:
  - `docker-compose up -d`
  - `docker-compose exec php composer install` 
  - `docker-compose exec php phinx migrate` 
  - `docker-compose exec php phinx seed:run` 
- Open `http://hotels.local` in the browser 
