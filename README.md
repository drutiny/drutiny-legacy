# Site audit

## Requirements

**Drush installed**

Drush is required to be installed locally and be available on your path. Drush 8 is recommended. Having a remote-host attribute in the drush alias file is required if you want to run any of the SSH checks.

**Composer**

Needed to install Symfony Console and related libraries.

```
composer install
```

## How to run

```
php site-audit audit:site govcms.casastg.govcms.gov.au --profile=govcms_saas -v
php site-audit audit:site iagwebsites.wfi.prod --profile=govcms_saas -v
```

## Bash alias

This could be helpful if you want to be able to run the command from anywhere

```
alias sa='php /path/to/site-audit audit:site'
```

Then you can run:

```
sa govcms.casastg.govcms.gov.au --profile=govcms_saas -v
```
