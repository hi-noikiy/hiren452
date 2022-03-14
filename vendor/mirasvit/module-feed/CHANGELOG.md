# Change Log
## 1.1.32
*(2021-05-07)*

#### Improvements
* Implemented LockManager interface
* Added the ability to use additional images in the filter rule conditions

#### Fixed
* Fixed an issue with duplicated cron export feed generation

---


## 1.1.31
*(2021-04-12)*

#### Fixed
* Fixed an issue with the incorrect filtering 'Amount of Children in stock' via cron generation
* Fixed an issue with the missing Category Mapping autocomplete

---


## 1.1.30
*(2021-01-28)*

#### Improvements
* Added ability to enable or disable fast filtering mode at the feed 'Additional' tab
* Added Trovaprezzi template

---



## 1.1.29
*(2020-12-30)*

#### Fixed
* fixed an issue with incorrect output of the filter condition 'IS'
* fixed an issue with the encoding characters at the feed preview
* fixed an issue with the inability to set empty field option at the filter condition

---


## 1.1.28
*(2020-12-03)*

#### Improvenets
* Added abbility to disable FB Metadata (tab "Additional")

---

## 1.1.27
*(2020-11-18)*

#### Improvements
* speed up the time of the feed export with the Category Mappings
* added Multi Source Inventory stocks filter conditions

---


## 1.1.26
*(2020-11-13)*

#### Fixed
* Incorrect class name for notification about installation of zf1/zend-reflection library

---

## 1.1.25
*(2020-11-09)*

#### Improvements
* module compatibility with the php 7.4
* added an error message if Zend Reflection library wasn't installed

#### Fixed
* fixed an issue with the incorrect filter Delete message

---


## 1.1.24
*(2020-10-21)*

#### Fixed
* invalid ID attribute

#### Improvements
* added new attribute 'Item group ID'

---


## 1.1.23
*(2020-09-28)*

#### Fixed
* fixed an issue with the incorrect filtering by categories

---


## 1.1.22
*(2020-09-11)*

#### Improvements
* added metadata to the Facebook feeds https://3.basecamp.com/4292992/buckets/14246772/todolists/3012280408

---


## 1.1.21
*(2020-09-10)*

#### Fixed
* fixed an issue with the filtering incorrect amount of products https://3.basecamp.com/4292992/buckets/14246772/todolists/3004659314

---


## 1.1.20
*(2020-09-08)*

#### Improvements
* Filter interface and applying logic

---


## 1.1.19
*(2020-09-05)*

#### Fixed
* Minor fixes

---


## 1.1.18
*(2020-07-29)*

#### Improvements
* Support of Magento 2.4

---


## 1.1.17
*(2020-07-03)*

#### Fixed
* fixed an issue with the filtering non-default attributes https://3.basecamp.com/4292992/buckets/14246772/todolists/2779115043

---


## 1.1.16
*(2020-06-12)*

#### Improvements
* added ability to export salable quantities of Multi Source Inventory stocks https://3.basecamp.com/4292992/buckets/14246772/todolists/2754713911

#### Fixed
* fixed an issue with the using filter conditions https://3.basecamp.com/4292992/buckets/14246772/todolists/2744638418

---


## 1.1.15
*(2020-05-15)*

#### Fixed
* fixed an issue with the incorrect exporting of the "Product Type" attribute values at the csv and txt feeds https://3.basecamp.com/4292992/buckets/14246772/todos/2657392179
* fixed an issue with the mirasvit sorting product collection during feed generation https://3.basecamp.com/4292992/buckets/14246772/todolists/2677137469 

---


## 1.1.14
*(2020-05-12)*

#### Fixed
* fixed errors during the filtration step https://3.basecamp.com/4292992/buckets/14246772/todos/2661509248

---

## 1.1.13
*(2020-04-29)*

#### Improvements
* Improved filtration time

---


## 1.1.12
*(2020-04-14)*

#### Fixed
* fixed an issue with the exporting inactive special prices

---


## 1.1.11
*(2020-03-23)*

#### Fixed
* fixed an issue with the incorrect select options of the rule condition 'manage stock' https://3.basecamp.com/4292992/buckets/14246772/todos/2516709888

---


## 1.1.10
*(2020-03-04)*

#### Fixed
* Issue when input for cron enabling/disabling don't show current cron status for feed
* Price format

---


## 1.1.9
*(2020-02-19)*

#### Fixed
* fixed an issue with the error during new category mapping creation https://3.basecamp.com/4292992/buckets/14246772/todos/2423577857

---


## 1.1.8
*(2020-02-04)*

#### Features
* added cron info to the 'Scheduled Task' feed tab

---


## 1.1.7
*(2019-12-27)*

#### Features
* added new product attribute 'Product Url with custom options'

---


## 1.1.6
*(2019-12-18)*

#### Features
* added new multi source inventory filters 'manage stock' and 'qty'

#### Fixed
* icon class in the button text
* incorrect selection field for the filter condition 'manage stock'

---


## 1.1.5
*(2019-11-22)*

#### Fixed
* Rename route "feed" to "mst_feed" (for prevent possible conflicts)
* Undefined offset on Magento EE
* chmod not permitted in Helper/Io.php

---


## 1.1.3
*(2019-10-29)*

#### Fixed
* Installation issues on Magento 2.2.x

---


## 1.1.2
*(2019-10-25)*

#### Fixed
* Undefined offset 0 on Magento EE

---

## 1.1.1
*(2019-10-25)*

#### Fixed
* Magento Marketplace tests

---


## 1.1.0
*(2019-10-11)*

#### Improvements
* added new filter conditions 'Created At' and 'Updated At'

---


## 1.0.110
*(2019-09-17)*

#### Fixed
* fixed an issue with AM/PM format at the cron schedule list
* fixed an issue with mass exporting templates at the grid
* updated to eqp requirements

---


## 1.0.109
*(2019-08-22)*

#### Fixed
* Updated templates
* Added notification about missing feed Filename before generation

---


## 1.0.108
*(2019-08-12)*

#### Improvements
* Added new 10 templates
* Minor templates updates

---


## 1.0.107
*(2019-08-08)*

#### Improvements
* Coding Standart Refactoring

#### Fixed
* minor template issue

---


## 1.0.106
*(2019-06-05)*

#### Improvements
* added new template 'Amazon Seller Central (example)'
* added an ability to duplicate Dynamic Attributes at the 'Action' column

---


## 1.0.105
*(2019-05-28)*

#### Improvements
* added an ability to export Multi Source Inventory sources (Magento > 2.3)

---


## 1.0.104
*(2019-05-22)*

#### Fixed
* fixed an issue with the tracking orders at the Feed Reports
* fixed an issue with incorrect exporting 'Reviews Count' attribute

---


## 1.0.103
*(2019-04-10)*

#### Fixed
* fixed an issue with the incorrect filtering rule condition 'Status(Parent Product)'
* fixed an error at the feed Preview page: 'Exception: for tag was never closed'

---


## 1.0.102
*(2019-03-28)*

#### Improvements
* added HMTL Entity Decode modifier

---


## 1.0.101
*(2019-03-19)*

#### Fixed
* Issue with filtration by Multi Select attribute

---


## 1.0.100
*(2019-02-22)*

#### Fixed
* fixed an issue with the incorrect store view export via cron schedule generation

---


## 1.0.99
*(2019-02-07)*

#### Fixed
* fixed an issue with the incorrect enclosure exporting in the csv and txt feeds

#### Improvements
* added new attribute 'Gallery image collection'

---


## 1.0.98
*(2019-02-04)*

#### Fixed
* Changed column length for dynamic category

---


## 1.0.97
*(2019-01-10)*

#### Fixed
* fixed an issue with the generation feeds by cron after new year

---


## 1.0.96
*(2019-01-09)*

#### Fixed
* fixed an issue with the incorrect output of the 'Is in Stock' attribute

---


## 1.0.95
*(2018-12-28)*

#### Improvements
* Ability to filter products without category
* added new modifiers

---


## 1.0.94
*(2018-12-20)*

#### Fixed
* fixed an issue with incorrect output after applying "Append" modifier to the numbers
* fixed an issue with the empty output instead of zero value at the Dynamic Attribute
---


## 1.0.93
*(2018-12-14)*

#### Improvements
* added 'Final Price with Tax' product attribute for export
* updated Google Shopping Review template

#### Fixed
* fixed an issue with the creating new feed using Empty Template

---


## 1.0.92
*(2018-12-11)*

#### Fixed
* fixed an issue with the incorrect work of Math Modifiers: Addition, Substraction, Multiplication, Division
* added new text Modifiers

---


## 1.0.91
*(2018-11-30)*

#### Improvements
* M2.3 support

---


## 1.0.90
*(2018-11-29)*

#### Fixed
* fix of the problem with the data serialization for magento versions < 2.3
---


## 1.0.89
*(2018-11-28)*

#### Fixed
* support of magento 2.3

---


## 1.0.88
*(2018-11-23)*

#### Fixed
* export full path for small image pattern

---

## 1.0.87
*(2018-11-20)*

#### Improvements
* added "Product ID" filter condition
* added "Qty of children in stock products" attribute for export 

---


## 1.0.86
*(2018-11-19)*

#### Fixed
* getLevel() issue

---


## 1.0.85
*(2018-11-15)*

#### Improvements
* Added new templates: "Facebook Dynamic Ads", "Domodi", "Marktplaats"
* Improved "Google Shopping Review" template according to the new XML Schema requirements
* Updated existing templates

---


## 1.0.84
*(2018-11-09)*

#### Improvements
* added 'Stock Status' product attribute

#### Fixed
* fixed an issue with the incorrect categories export according to their position

---


## 1.0.83
*(2018-10-25)*

#### Fixed
* fixed an issue with the incorrect timezone export date at the 'time' pattern
* fixed an issue with the exporting empty feed if any filter conditions are applied

---


## 1.0.82
*(2018-10-02)*

#### Fixed
* Possible issue with sorting

---


## 1.0.81
*(2018-09-11)*

#### Improvements
* Performance

---


## 1.0.80
*(2018-09-06)*

#### Improvements
* Added memory/interation time to CLI command

---



## 1.0.79
*(2018-09-05)*

#### Fixed
* Feed emails are not working
* When using filters out of stock products excluded from feed
* Issue with truncate filter (multibyte strings)
* Fixed error "A technical problem with the server created an error. Try again to continue what you were doing. If the problem persists, try again later."

#### Improvements
* Created separate option group for Category Mappings
---


## 1.0.77
*(2018-08-09)*

#### Improvements
* improved the "Is Salable" filter condition to exclude children products according to their parent salable status

#### Fixed
* fixed an issue with the incorrect export of the category path by levels

---


## 1.0.76
*(2018-07-24)*

#### Fixed
* fixed an issue with the incorrect Review Summary Rating export
* fixed an issue with the SQL errors during Magento installation

---


## 1.0.74
*(2018-07-23)*

#### Fixed
* fixed the Eval function Error via using the Dynamic Variables

---


## 1.0.73
*(2018-07-17)*

#### Fixed
* added compatibility of Notification Email classes with the Magento 2.1.x versions

---


## 1.0.72
*(2018-07-16)*

#### Fixed
* fixed an issue with the incorrect feeds delivery and export via CLI commands
* fixed an issue with the sending notification emails

---


## 1.0.71
*(2018-07-10)*

#### Fixed
* Pie chart is not displayed: set default column for chart

---

## 1.0.70
*(2018-06-28)*


#### Improvements
* added 'Final Price' filter condition
* improved 'Is Salable' filter condition

#### Fixed
* fixed issues with processing image and image sizes filter conditions
* fixed an issue with the incorrect and missing enclosures in the txt and csv feeds 
---

## 1.0.69
*(2018-06-04)*

#### Fixed
* fixed an issue with the incorrect feed schedule time execution by cron

---


## 1.0.68
*(2018-03-19)*

#### Fixed
* fixed an issue with incorrect filtering of "Stock Availability" condition ([#37](../../issues/37))

---


## 1.0.67
*(2018-03-12)*

#### Fixed
* fixed an issue with incorrect filtering products by category ids
* Reports are not visible in 'developer' mode

---


## 1.0.66
*(2018-02-26)*

#### Fixed
* Report is not displayed (affects since 1.0.65)

---


## 1.0.65
*(2018-02-23)*

#### Improvements
* compatibility with latest version of Mirasvit module Reports

#### Fixed
* fixed an issue with the showing store categories on the Category Mapping page

---



### 1.0.64
*(2018-02-15)*

#### Improvements
* Added filter condition to export products by amount of in stock children

---

## 1.0.63
*(2018-02-02)*

#### Bugfixes
* fixed an issue with the files locking on Windows #23

---

### 1.0.62
*(2017-12-15)*

#### Fixed
* Issue with yaml parsing library

---

### 1.0.61
*(2017-12-07)*

#### Fixed
* Fixed issue with deleting dynamic variables from the mass action grid

---

### 1.0.60
*(2017-11-22)*

#### Feature
* Feed generation report

#### Improvements
* Display number of generated items in the feed

---

### 1.0.59
*(2017-11-17)*

#### Fixed
* Issue with price export by cron

---

### 1.0.58
*(2017-10-12)*

#### Fixed
* Fixed an issue with the exploding array instead of string

---

### 1.0.57
*(2017-10-12)*

#### Fixed
* Fixed an issue related to incorrect export of parent product values in Magento Enterprise edition
* Fixed an issue with the exploding array instead of string

---

### 1.0.56
*(2017-09-28)*

#### Improvements
* Compatibility with Magento 2.2

---

### 1.0.55
*(2017-06-27)*

#### Fixed
* Dynamic attribute conditions do not work properly when admin user has custom permissions
* Solve 'Duplicate entry' error occurred on a filtration step

#### Improvements
* Modifier to remove all non-utf8 characters
* Ability to limit gallery collection

---

### 1.0.54
*(2017-06-13)*

#### Fixed
* Issue with image resolver

---

### 1.0.53
*(2017-05-05)*

#### Fixed
* Product.price to product.finalPrice for google templates
* Fixed a feed generation error via CLI - "Area code is already set"
* Fixed feed generation error via command Command Line Interface - "Area code is already set"

### Features
* Ability to use Product attributes in the Google Analytics tabs

---

### 1.0.52
*(2017-03-30)*

#### Improvements
* Changed clicks logging mechanism to more stable

#### Fixed
* CI for import/export

---

### 1.0.51
*(2017-03-24)*

#### Features
* Import/export Feed entities

#### Documentation
* updated documentation

---

### 1.0.50
*(2017-03-21)*

#### Fixed
* Possible issue with filtration

---

### 1.0.49
*(2017-02-27)*

#### Fixed
* Fixed an issue with patterns preview

---

### 1.0.48
*(2017-02-27)*

#### Improvements
* Changed report version to 1.2.*

#### Fixed
* Fixed an issue with dynamic attributes

---

### 1.0.47
*(2017-02-20)*

#### Improvements
* Ability to export swatches values

---

### 1.0.46
*(2017-02-14)*

#### Improvements
* Added dedicated cron group for feed generation process

#### Fixed
* Fixed an issue with Plain filter

---

### 1.0.45
*(2017-02-01)*

#### Fixed
* Fixed an issue with special char "|" in filters

---

### 1.0.44
*(2017-01-20)*

#### Improvements
* Changed logic of exporting configurable product attributes. If configurable product return empty value, module select values for child products

---

### 1.0.43
*(2017-01-19)*

#### Fixed
* Fixed an issue with thumbnail images

---

### 1.0.42
*(2017-01-11)*

#### Fixed
* Fixed an issue with json

---

### 1.0.41
*(2017-01-10)*

#### Improvements
* Ability to export all attributes (CSV header XALL)
* Added json filter {{ product.gallery | json }}

---

### 1.0.40
*(2017-01-09)*

#### Fixed
* Fixed an issue with tax rate preview

---

### 1.0.39
*(2017-01-06)*

#### Improvements
* Implemented lock mechanism for prevent parallel feed generation (CLI)

---

### 1.0.38
*(2017-01-06)*

#### Fixed
* Fixed an issue with category mapping (edit page)

---

### 1.0.37
*(2017-01-06)*

#### Fixed
* Fixed an issue with mapping

---

### 1.0.36
*(2017-01-03)*

#### Fixed
* Fixed an issue with nested category mapping

---

### 1.0.35
*(2017-01-03)*

#### Fixed
* Added symfony/yaml to depends

#### Improvements
* Ability to place category taxonomy files to pub/media/feed/mapping

---

### 1.0.33
*(2016-12-21)*

#### Fixed
* Fixed an issue with filtration by category (simple products that not visible in catalog)

---

### 1.0.32
*(2016-12-15)*

#### Improvements
* Improved performance for csv feed edit page

---

### 1.0.31
*(2016-12-14)*

#### Fixed
* Fixed an issue with current date

---

### 1.0.30
*(2016-12-09)*

#### Improvements
* Compatibility with M2.2

---

### 1.0.29
*(2016-12-06)*

#### Fixed
* Fixed an issue with pattern output for dynamic attributes

---

### 1.0.28
*(2016-12-05)*

#### Fixed
* Fixed an issue with parent selector in dynamic attributes

---

### 1.0.27
*(2016-11-11)*

#### Fixed
* Fixed an issue with filtration by Yes/No attributes

---

### 1.0.26
*(2016-11-04)*

#### Fixed
* Fixed an issue with Status filter

---

### 1.0.25
*(2016-11-02)*

#### Fixed
* Changed crontab
* Fixed an issue with feed delivery (SFTP)

---

### 1.0.24
*(2016-10-21)*

#### Fixed
* Fixed an issue with compilation

---

### 1.0.23
*(2016-10-20)*

#### Fixed
* Fixed an issue with deleting dynamic attribute
* Fixed an issue with filter by stock quantity

---

### 1.0.21
*(2016-10-12)*

#### Improvements
* Use the same font-awesome.min.css for all extensions

---

### 1.0.20
*(2016-10-12)*

#### Fixed
* Fixed an issue with dynamic attributes

---

### 1.0.19
*(2016-10-07)*

#### Fixed
* Fixed an issue with images url
* Fixed an issue with wrong product url (multi-store configuration)
* Select product category depend on current store

---

### 1.0.18
*(2016-09-06)*

#### Improvements
* Export category with maximum level that related with product

#### Fixed
* Fixed an issue with category mapping

---

### 1.0.17
*(2016-09-05)*

#### Fixed
* Fixed possible issue with generation process

---

### 1.0.16
*(2016-08-23)*

#### Fixed
* Fixed an issue with delivery button

---

### 1.0.15
*(2016-08-23)*

#### Fixed
* Fixed a possible issue with filtration

---

### 1.0.14
*(2016-07-18)*

#### Fixed
* Fixed an issue with reports
* CI

---

### 1.0.13
*(2016-06-24)*

#### Fixed
* Compatibility with Magento 2.1
* Fixed an issue with validation dynamic attribute rules

---

### 1.0.12
*(2016-06-17)*

#### Fixed
* Fixed an issue with dynamic attribute values

---

### 1.0.11
*(2016-05-25)*

#### Fixed
* Fixed an issue with empty attribute value, if attribute contains numbers

---

### 1.0.10
*(2016-05-25)*

#### Features
* Implemented autocomplete for category mapping

#### Improvements
* Ability use dynamic variables in {% for %} cycle

#### Fixed
* Fixed an issue with dynamic attribute conditions (multi-select)

---

### 1.0.9
*(2016-04-11)*

#### Fixed
* Fixed an issue with menu
* ACL for dynamic variables

---

### 1.0.8
*(2016-04-07)*

#### Improvements
* Integration tests for Dynamic Variables
* Dynamic Variables
* Offset attribute for "for" cycle

#### Fixed
* Remove Root Category from categoryCollection method
* Fixed an issue with capture tag

#### Documentation
* Dynamic Variables

---

### 1.0.7
*(2016-04-01)*

#### Fixed
* Fixed an issue with cross-browsing ajax requests

---

### 1.0.6
*(2016-04-01)*

#### Fixed
* Fixed an issue with feed generation (not all products for feed with >100K products)
* Styles

#### Improvements
* Capture tag
* Ability to defined product ids for feed preview (stored in cookies)
* Dynamic Attributes
* Reports
* Added ability to export Related/CrossSell/UpSell products

### 1.0.5
*(2016-03-14)*

#### Improvements
* Added random param to export/progress url for prevent request caching
* Added ability to export Related/CrossSell/UpSell products
* Added 2 new filters: mediaSecure, mediaUnsecure
* Export all images with direct link (without cdn if defined)
* Updated "Save" button for Templates, Filters and Category Mapping
* Added tax rate resolver "{{ product.tax_rate }}"
* Clean feed history by cron (leave history for last 3 days)

#### Fixed
* Updated "Google Shopping" template
* Fixed an issue with cron job scheduling

---

### 1.0.4
*(2016-02-17)*

#### Improvements
* Improved feed generation process by cron, plus added integration tests for cron job
* Improved feed history (CLI and manual export process)
* Added new filters inclTax, exclTax

#### Fixed
* Fixed an issue with broken link to Category Mapping in top menu
* Fixed an issue with gallery images (for cycle)
* CouplingBetweenObjects
* Fixed an issue with rounding prices when apply filter inclTax, exclTax
* Fixed an issue with trimming chars in {for} cycle
* Fixed an issue with removing liquid filters durign change expression (xml)
* Fixed an issue with mysql error at feed preview on empty catalog
* Fixed an issue with exclude tax calculations
* Fixed an issue with wrong js mapping (reports.js)

---

### 1.0.3
*(2016-02-08)*

#### Improvements
* Added new filters for urls: "secure" and "unsercure"
* Added prices including tax

---

### 1.0.2
*(2016-02-07)*

#### Improvements
* Improved xml highlighting
* Split product resolve to few files depends on product type
* Added date filter
* Added ability to select associated products {{ product.associatedProducts }}

#### Fixed
* Integration tests
* Fixed an issue with select/multiselect attribute values
* Fixed an issue related with wrong loop length in liquid cycles
* Fixed an issue with "No elements to pop"
* Minor issue

------
