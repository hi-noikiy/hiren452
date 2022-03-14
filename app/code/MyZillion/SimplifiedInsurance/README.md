# MyZillion - Magento 2 Simplified Plugin

[![N|Solid](https://www.serfe.com/serfe_logo_email.jpg)](https://www.serfe.com/en/)

MyZillion - Simplified Plugin is module for Magento 2, which allows the user to select if they want to add a jewelry insurance in the billing step of checkout process.

# New Features!

  - The customer does not pay for the insurance, so there’s no need to add the insurance to the cart.
  - The insurance does not need to be invalidated after changes to the order. No status tracking.
  - The insurance offer price is not shown to the customer



### Installation

MyZillion - Simplified Insurance Plugin is compatible with:

 - Magento 2.2.11
 - Magento 2.3.5

- Update composer.json file:

#### with SSH key or deploy key:
```
{
    "type": "vcs",
    "url": "git@github.com:myzillion/Zillion-Magento-Extension.git"
},
{
    "type": "vcs",
    "url": "git@github.com:myzillion/zillion-insurance-api-wrapper-php.git"
}
```

- How to add an SSH deploy key: https://docs.github.com/v3/guides/managing-deploy-keys/
- How to generate an SSH key: https://docs.github.com/articles/generating-an-ssh-key

#### With user/password:
```
{
    "type": "vcs",
    "url": "https://github.com/myzillion/Zillion-Magento-Extension.git"
},
{
    "type": "vcs",
    "url": "https://github.com/myzillion/zillion-insurance-api-wrapper-php.git"
}
```

- Require module
```sh
$ composer require myzillion/module-simplified-insurance
```

- Enable module and deploy Magento

```sh
$ bin/magento module:enable MyZillion_SimplifiedInsurance
$ bin/magento module:status MyZillion_SimplifiedInsurance
$ bin/magento setup:upgrade
$ bin/magento setup:di:compile
$ bin/magento cache:enable
$ bin/magento cache:flush

```


### Configuration

Module configuration is available at

Stores > Settings > Configuration > My Zillion > API

#### Options

- **Enabled**: Enable or disable the module
- **API Key**: API key provided by zillion. This is the 'Basic username:password' string encrypted in base 64.
- **Test Mode**:  Enable or disable the test mode.
- **Zillion Product Type Attribute**: Allows to map an existing store attribute to be used as Product Zillion Type in the API requests
