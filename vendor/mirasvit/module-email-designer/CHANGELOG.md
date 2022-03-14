# Change Log
## 1.2.0
*(2021-01-04)*

#### Fixed
* Fixed the issue with error in the email template related to the product ID

---


## 1.1.45
*(2020-10-13)*

#### Improvements
* Small spelling fixes
---

## 1.1.44
*(2020-08-20)*

#### Fixed
* Minor fixes

---


## 1.1.43
*(2020-07-22)*

#### Improvements
* added new cart variables 'Item Quantity' and 'Amount of products in the cart' https://3.basecamp.com/4292992/buckets/14310822/todolists/2768063790

---


## 1.1.42
*(2020-04-09)*

#### Fixed
* fixed an issue with the incorrect product and image urls https://3.basecamp.com/4292992/buckets/14310822/todos/2486980223

---


## 1.1.41
*(2020-03-26)*

#### Improvements
* added new functionality to send in email only the first enabled product from the quote/order https://3.basecamp.com/4292992/buckets/14310822/todos/2525779268

---


## 1.1.40
*(2020-03-03)*

#### Fixed
* fixed an issue with the error 'Class \Magento\Wishlist\Model\Wishlist|bool does not exist' at the templates

---
## 1.1.39
*(2020-03-02)*

#### Improvements
* added new template variable "Get customer phone number"

---

## 1.1.38
*(2020-02-26)*

#### Fixed
* fixed an issue with the incorrect product redirect urls

---

## 1.1.37
*(2020-01-02)*

#### Improvements
* added ability to duplicate templates and themes

---

## 1.1.36
*(2019-12-19)*

#### Fixed
* fixed an error with the fread length at the email theme
* fixed an issue with the 'Fallback to JQueryUI Compat activated'

---

## 1.1.35
*(2019-12-04)*

#### Fixed
* fixed an error at emty theme or template "Length parameter must be greater than 0"

---

## 1.1.34
*(2019-11-06)*

#### Fixed
* Minor changes

---

## 1.1.33
*(2019-10-29)*

#### Fixed
* Issue with responsive preview

---

## 1.1.32
*(2019-08-14)*

#### Fixed
* fixed an issue 'Environment emulation nesting is not allowed'
* EQP code refactoring

---

## 1.1.31
*(2019-06-26)*

#### Fixed
* fixed an issue with incorrect output Store variables by store view

#### Improvements
* added new Store variables

---

## 1.1.30
*(2019-06-10)*

#### Improvements
* added 'order id' and 'shipping address' template variables

---

## 1.1.29
*(2019-02-18)*

#### Fixed
* fixed an issue with the template 'Insert Variable' pop-up on Magento 2.3
* store_email variable does not return store-specific email address

---

## 1.1.28
*(2018-12-20)*

#### Fixed
* Issue with amp (&) in urls

---

## 1.1.27
*(2018-11-28)*

#### Fixed
* support of magento 2.3

---

## 1.1.26
*(2018-10-22)*

#### Fixed
* Email made of curly braces }}}, when inline translation active

---

## 1.1.25
*(2018-09-21)*

#### Fixed
* price_html method for retrieving ready for displaying item's price

---

## 1.1.24
*(2018-09-19)*

#### Improvements
* getCustomerName method now accepts second parameter used as a fallback for customer name

#### Fixed
* Compatibility issue with Magetrend_Email

---

## 1.1.23
*(2018-07-11)*

#### Improvements
* Variable for getting customer first name

#### Fixed
* Email theme editor loses focus on Safari

---

## 1.1.22
*(2018-07-09)*

#### Fixed
* Fix errors for marketplace

---

## 1.1.21
*(2018-07-05)*

#### Improvements
* Ability to translate email template text via i18n CSV files
* Handle native Magento templates in first order by Magento template engine
* Mute 'payment' error when rendering native Magento email templates and log them instead

#### Fixed
* Email template editor loses focus on Safari
* Avoid error for test and preview emails during product image rendering
* Properly render native Magento email templates
* Error on 'setup:di:compile' on Magento versions prior to 2.2 (affects from 1.1.20)

---

## 1.1.20
*(2018-06-28)*

#### Features
* Ability to use Magento native email templates

---

## 1.1.19
*(2018-06-22)*

#### Improvements
* Improve product image select algorithm

#### Fixed
* Theme

---

## 1.1.18
*(2018-06-21)*

#### Improvements
* New PHP variable getTemplate

---

## 1.1.17
*(2018-06-12)*

#### Improvements
* Show message on theme and template pages if Liquid library is not installed

---

## 1.1.16
*(2018-05-31)*

#### Fixed
* Adjust width for theme/template editor fields
* Correctly display theme/template preview

---

## 1.1.15
*(2018-05-24)*

#### Fixed
* Error during running command setup:update after module installation

---

## 1.1.14
*(2018-05-11)*

#### Improvements
* Do not allow to remove default templates

---

## 1.1.13
*(2018-04-27)*

#### Improvements
* Do not show the 'Area not defined' message
* Variable for customer first name

#### Fixed
* Error previewing templates - negative offset in SQL

---

## 1.1.12
*(2018-04-13)*

#### Fixed
* fixed an issue with editing and deleting theme/template from the grid

---

## 1.1.11
*(2018-04-11)*

#### Fixed
* fixed a problem with Email Theme preview

---

## 1.1.10
*(2018-04-11)*

#### Fixed
* fixed an issue with the incorrect loading of the theme preview

---

## 1.1.9
*(2018-04-03)*

#### Fixed
* Problem with Email Template/Theme previews

---

## 1.1.8
*(2018-03-29)*

#### Fixed
* Theme and Template preview pages don't work correctly when enabled cache

---

## 1.1.7
*(2018-03-13)*

#### Fixed
* Fixed an issue with showing images at the emails #8

---

## 1.1.6
*(2018-02-20)*

#### Fixed
* Fix error during creating a new theme (affects since 1.1.5)

---

## 1.1.5
*(2018-02-15)*

#### Bugfixes
* Items not displayed in emails with liquid syntax #5

---

## 1.1.4
*(2018-02-12)*

#### Improvements
* Move Email Theme editor to liquid syntax #4

---

## 1.1.3
*(2018-02-02)*

#### Bugfixes
* Wrong field width for newly created email templates #2
* Email Templates are not displayed when JS bundling enabled mirasvit/module-email#51

---

## 1.1.2
*(2018-01-29)*

#### Improvements
* Additional filters for Liquid variables

---

## 1.1.1
*(2018-01-29)*

#### Bugfixes
* Fix error 'environment emulation nesting is not allowed' mirasvit/module-email#44

---

## 1.1.0
*(2018-01-16)*

#### Features
* new UI for working with Variables in emails [#1](http://some.issue.tracker.com/1)

#### Improvements
* Move template form to UI

---

