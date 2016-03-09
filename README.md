# Site audit

This is a generic Drupal 7 site auditing tool.

## Why another site audit tool?

This tool is different, in the sense that a site audit is comprised of a profile, and a profile can contain 1 or more checks, and those checks can have optional arguments supplied. This means that you can create a profile that is specific to your own internal guidelines, and not some generic report that may or may not be of any use to you.

Checks are simple classes, that at the moment can either be Drush based checks, or SSH based checks. This allows for easy extension to cover any needs.

## Requirements

**Drush installed**

Drush is required to be installed locally and be available on your path. Drush 8 is recommended. Having a remote-host attribute in the drush alias file is required if you want to run any of the SSH checks.

**Composer**

Needed to install Symfony Console and related libraries.

```
composer install
```

## How to run

Run using the `default` profile (replace [alias] with your drush alias):

```
php site-audit audit:site [alias]
```

Run a side audit using the `govcms_saas` profile:

```
php site-audit audit:site [alias] --profile=govcms_saas
```

Because this is Symfony console, you have some other familiar commands:

```
php site-audit help audit:site
```

In particular, if you use the `-v` argument, then you will see all the drush commands, and SSH commands printed to the screen.

## Bash alias

This could be helpful if you want to be able to run the command from anywhere

```
alias sa='php /path/to/site-audit audit:site'
```
