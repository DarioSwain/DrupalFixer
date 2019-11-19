# DrupalFixer
Checks and fixes Drupal deprecated code.

**[IMPORTANT] This project is an experiment to figure out how is complex to implement automatically migration 
of deprecated Drupal7/8 code blocks which are going to be removed in Drupal 9.**

Manual checks of the results are required.

## Contributing

Please [follow this guide](/docs/CONTRIBUTION.md) to understand how this project is working and how you can contribute into it.

## Requirements

* PHP >=7.1

## Installation

### Composer

To get the latest master version of this project you can install it via composer:

```
php myapp/composer.phar create-project dario_swain/drupal-fixer -s dev
cd drupal-fixer
vendor/bin/rector -V
```

After this you should get Rector version in console like:
```
Rector v0.5.16
```

### Build From Source

- Clone or fork and clone this repository.
- Run ```composer install```
- That's it you can use Rector via ```vendor/bin/rector -V```

## Usage

```
php ../../vendor/bin/rector -c ../../configs/drupal-9-deprecations.yaml process -a vendor/autoload.php modules/contrib/simple_amp/src/ --dry-run

```
  
## License

[GPL v2](LICENSE)

## Issues

Submit issues and feature requests here: https://github.com/DarioSwain/DrupalFixer/issues.

### Known Issues

---
