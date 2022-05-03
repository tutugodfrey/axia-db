# Axia-DB

Repository for the Axia-DB application.

## Dependencies

### Production

This is the preferred approach for installing dependencies in production:

```shell
composer install --prefer-dist --optimize-autoloader
```

### Development

This is the preferred approach for installing dependencies during development:

```shell
composer update --prefer-source
```

#### PHP packages

This app uses Composer to manage PHP dependencies.

To add new dependencies, run:

```shell
composer require user/repo:version
git add composer.*
```

## Development environment

To help developers get started quickly, a PuPHPet VM is included with this repository.

```shell
vagrant up
```

To initialise the database, place a copy of `axia.dump` into the root of this repository,
then execute the following commands:

```shell
vagrant ssh
sudo -u postgres -H createuser -DLRS axia
sudo -u postgres -H psql -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";'
sudo -u postgres -H pg_restore -C -d postgres /vagrant/axia.dump
```
