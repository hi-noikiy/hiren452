# Change Log
## 2.1.45
*(2020-11-03)*

#### Fixed
* fixed an issue with the missing test email send button at the Magento 2.4.x
* fixed an issue with the checkout processing
* fixed an issue with the event grid filtering

---


## 2.1.44
*(2020-09-07)*

#### Fixed
* fixed an issue with the checkout processing M2.4 https://3.basecamp.com/4292992/buckets/14310822/todos/2970332556

---


## 2.1.43
*(2020-07-29)*

#### Improvements
* Support of Magento 2.4

---


## 2.1.42
*(2020-06-18)*

#### Fixed
* fixed an issue with the emails delivery via 'Send' action button at the Mail Log (Queue)

---


## 2.1.41
*(2020-05-27)*

#### Fixed
* show warning message before sending test email that trigger 'Event' was not specified
* fixed an issue with the showing html text in the emails

---


## 2.1.40
*(2020-03-13)*

#### Fixed
* added Mageplaza Smtp compatibility

---


## 2.1.39
*(2020-03-04)*

#### Fixed
* fixed an issue with incorrect redirect urls of the cross-sell products

---


## 2.1.38
*(2020-02-26)*

#### Fixed
* fixed an issue with incorrect redirect urls of the cross-sell products https://3.basecamp.com/4292992/buckets/14310822/todos/2437751501

---


## 2.1.37
*(2020-02-18)*

#### Features
* added new template coupon variable 'Get coupon expiration date'

---


## 2.1.36
*(2020-02-10)*

#### Features
* added new template url variable 'Get URL used to create a new checkout cart for reorder'

---


## 2.1.35
*(2020-01-08)*

#### Improvements
* added functionality to manage emails subscriptions and unsubscriptions at Marketing > Follow Up Email > Unsubscription List
* added recipient email validation before sending

---


## 2.1.34
*(2020-01-08)*

#### Improvements
* added mass action for the events: 'Reset & Process'

---


## 2.1.33
*(2019-12-16)*

#### Improvements
* added trigger event "Change Group"

---


## 2.1.32
*(2019-12-05)*

#### Fixed
* show only in stock and enabled products at the template cross sell block

---


## 2.1.31
*(2019-12-03)*

#### Improvements
* added compatibility with the Mageplaza Smtp

#### Fixed
* fixed an issue with the active coupon status after his expiration date

---


## 2.1.30
*(2019-11-05)*

#### Fixed
* Issue with call to undefined method EmailMessage::setSubject()

---


## 2.1.29
*(2019-10-31)*

#### Fixed
* Compatibility with Magento 2.3.3

---
## 2.1.28
*(2019-08-14)*

#### Fixed
* EQP code refactoring

---


## 2.1.27
*(2019-07-30)*

#### Fixed
* Conflict with mageplaza smtp

---


## 2.1.26
*(2019-07-11)*

#### Fixed
* fixed an issue with the events processing

---


## 2.1.25
*(2019-06-26)*

#### Fixed
* fixed an issue with the incorrect displaying 'Scheduled At' emails date

---


## 2.1.24
*(2019-04-22)*

#### Fixed
* Compatibility with Ebizmarts Mandrill

---


## 2.1.23
*(2019-03-27)*

#### Fixed
* fixed an issue with the incorrect 'Sender From' name at the emails

---


## 2.1.22
*(2019-03-22)*

#### Fixed
* fixed an issue with missing Google Analytics parameters at the email urls

---

## 2.1.21
*(2019-03-18)*

#### Fixed
* Compatibility with M2.1
* fixed an issue with the sending test emails
* Issue deleting trigger' email chain from campaign

---

## 2.1.20
*(2019-02-21)*

#### Fixed
* Fix compilation error for Magento 2.1

#### Documentation
* Update path to Event settings

---

## 2.1.19
*(2019-02-11)*

#### Fixed
* Issue saving active to/from dates for trigger #116

---

## 2.1.18
*(2019-02-08)*

#### Fixed
* Fix error blocking Magento installation
* View email in browser shows wrong email
* Possible issue with sending emails. Error message like "The recipient address < ...> is not a valid RFC-5321"

---

## 2.1.17
*(2018-12-24)*

#### Fixed
* Error after navigation by link through email

---

## 2.1.16
*(2018-12-10)*

#### Fixed
* Trigger's rule edit section does not work

---

## 2.1.15
*(2018-11-30)*

#### Improvements
* support for M2.3

---

## 2.1.14
*(2018-10-22)*

#### Fixed
* Wrong emails report
* CSS issue

---

## 2.1.12
*(2018-10-03)*

#### Improvements
* Improved Reports

---


## 2.1.11
*(2018-09-26)*

#### Fixed
* Test emails continue to be sent automatically
* Error opening a campaign when email chain template has been removed
* DI compilation error

#### Documentation
* changes to getCustomerName callout

---

## 2.1.10
*(2018-09-06)*

#### Fixed
* Do not enqueue email if there is no recipient email address

---

## 2.1.9
*(2018-09-05)*

#### Fixed
* Mass send function does not work in Mail Log

#### Documentation
* Troubleshoot for translation of email templates

---

## 2.1.8
*(2018-08-30)*

#### Fixed
* Test email is not sent, when admin URL does not match front URL

---

## 2.1.7
*(2018-08-29)*

#### Fixed
* unsubscribe variable does not work

#### Documentation
* troubleshoot for coupon code and block with cross-sell products

---

## 2.1.6
*(2018-08-03)*

#### Fixed
* Campaign edit page is not loaded after update: emaildesigner keyword added 2 times

---

## 2.1.5
*(2018-07-31)*

#### Fixed
* Manage campaigns pages is not loaded on Magento 2.1.0 version

---

## 2.1.4
*(2018-07-13)*

#### Features
* Ability to disable Magento Newsletter's Success Email Template

#### Documentation
* How to disable the default Magento Newsletter's Success Email Template

---

## 2.1.3
*(2018-07-09)*

#### Improvements
* Refactoring

---

## 2.1.2
*(2018-07-09)*

#### Fixed
* fixed an issue with incorrect output of Facebook and Twitter Urls in emails

---

## 2.1.1
*(2018-07-03)*

#### Improvements
* Properly retrieve cross-sell products
* Ability to translate email template text via i18n CSV files
* Mute 'payment' error when rendering native Magento email templates and log them instead
* Use latest order information in test and preview emails

#### Fixed
* Properly create campaigns from template
* Email template editor loses focus on Safari
* Avoid error for test and preview emails during product image rendering
* Properly render native Magento email templates
* Error on 'setup:di:compile' on Magento versions prior to 2.2 (affects from 1.1.20)

---

## 2.1.0
*(2018-06-28)*

#### Features
* Ability to use native Magento email templates

#### Improvements
* Improve product image select algorithm in email templates

#### Fixed
* Product price change triggers the Abandoned Cart event of a cart which contains this product

---

## 2.0.4
*(2018-05-31)*

#### Fixed
* Invalid trigger link on Queue Preview page
* Cannot save trigger: properly handle resource permissions

---

## 2.0.3
*(2018-05-24)*

#### Fixed
* Error during running command 'setup:update' after module installation

---

## 2.0.2
*(2018-05-17)*

#### Fixed
* Error opening campaigns on Magento prior to 2.2 versions: 'Element modal is not expected'

---

## 2.0.1
*(2018-05-11)*

#### Fixed
* Error during update: use correct version of Email Designer module
* Error during compiling DI files

---

## 2.0.0
*(2018-05-11)*

#### Features
* New major update: UI, Campaigns, Statistic and other improvements

#### Documentation
* GDPR Compliance Tips

---

## 1.1.25
*(2018-04-27)*

#### Improvements
* Compatibility with latest email report module

#### Fixed
* Error previewing templates - negative offset in SQL
* Issue saving active to/from dates

---

## 1.1.24
*(2018-03-02)*

#### Fixed
* remove 'custom' area from the default email design

#### Documentation
* Update installation instruction

---

## 1.1.23
*(2018-02-27)*

#### Fixed
* Emails are sent to all store views instead of chosen only (affects since 1.1.19)

---

## 1.1.22
*(2018-02-16)*

#### Fixed
* Do not use same coupon code across different emails of the same trigger #60

#### Documentation
* correct command to update module
* product view event
* liquid is supported with Email Themes now
* Documentation for liquid syntax #45

---

## 1.1.21
*(2018-02-12)*

#### Improvements
* Move Email Theme editor to liquid syntax mirasvit/module-email-designer#4
* Liquid syntax allows to avoid blocking of templates rendering by server extension "ModSecurity"

#### Bugfixes
* Do not show coupon block in emails even if it disabled

---

## 1.1.20
*(2018-02-09)*

#### Features
* New condition 'Recipient does not have emails for triggers' #53
* Product View event #33
* Default trigger for Product View event #58
* Email template for Product View event #54

#### Bugfixes
* Cross sell products are not displayed in emails #55

#### Improvements
* Register only active events mirasvit/module-event#9

---

## 1.1.19
*(2018-01-30)*

#### Improvements
* Process events in realtime with message-queue #19

---

## 1.1.18
*(2018-01-26)*

#### Bugfixes
* Solve error during performing setup:di:compile command
* Fix error for restore checkout method - use correct alias for Quote model

---

## 1.1.17
*(2018-01-24)*

#### Improvements
* Create a new cart if the previous one has already been finished earlier #46

---

## 1.1.16
*(2018-01-18)*

#### Bugfixes
* test emails are not sent

#### Documentation
* Update for manual installation instruction

---

## 1.1.15
*(2018-01-16)*

#### Features
* new UI for working with variables in emails mirasvit/module-email-designer[#1](http://some.issue.tracker.com/1)
* Liquid template engine and menu with available variables

#### Bugfixes
* correctly emulate store to retrieve correct product URL [#27](http://some.issue.tracker.com/27)
* fixed an issue with the incorrect restore cart url redirect from email ([#21](http://some.issue.tracker.com/21))

#### Improvements
* add liquid variables
* Set queue status to 'Error' if it throws errors during sending

---

### 1.1.10
*(2017-12-11)*

#### Fixed
* Use product URLs with a correct store base URL
* Show global Follow Up Email settings for website and store view
* Use sender name and email from store scope when available
* Do not duplicate trigger on save
* Fixed an issue with the incorrect restore cart url redirect from email

---

### 1.1.9
*(2017-12-01)*

#### Fixed
* Validate only triggering events on the event check stage

---

### 1.1.8
*(2017-11-23)*

#### Improvements
* Rename column's title
* Remove old files

---

### 1.1.7
*(2017-11-03)*

#### Fixed
* Ignore test events

---

### 1.1.6
*(2017-11-01)*

#### Fixed
* Problem with trigger excluded weekdays setting

---

### 1.1.5
*(2017-11-01)*

#### Fixed
* Installation error
* Error opening trigger listing due to loading trigger rules

#### Documentation
* Update installation docs

---

### 1.1.4
*(2017-10-30)*

#### Fixed
* Use correct MySQL column type for updated/created at columns

---

### 1.1.3
*(2017-10-30)*

#### Fixed
* Compatibility with PHP > 7.0.0

---

### 1.1.2
*(2017-10-27)*

#### Fixed
* Properly migrate rules

---

### 1.1.1
*(2017-10-27)*

#### Fixed
* Disable console command
* Remove unused classes
* Update dates

---

### 1.1.0
*(2017-10-26)*

#### Improvements
* Add Sample Triggers
* Update Trigger's Rules On Event Changing
* Show Only Events Available For Follow Up Email
* Integrate With Module-event

#### Fixed
* Remove Unnecessary Files
* Correct Trigger Link In Queue View
* Ignore Shopping Carts That Have Associated Orders
* Filter Payment Methods Without Labels

---

### 1.0.57
*(2017-09-28)*

#### Fixed
* Fix error during compilation

---

### 1.0.56
*(2017-09-27)*

#### Improvements
* Compatibility with Magento 2.2

---

### 1.0.55
*(2017-09-05)*

#### Fixed
* Properly create Administrator triggers

---

### 1.0.54
*(2017-09-04)*

#### Documentation
* Improve documentation

---

### 1.0.53
*(2017-09-04)*

#### Fixed
* Compatibility with Magento 2.2.0rc

---

### 1.0.52
*(2017-09-01)*

#### Fixed
* Fix incorrect dependency error during compilation

---

### 1.0.51
*(2017-09-01)*

#### Improvements
* UI improvements

---

### 1.0.50
*(2017-08-31)*

#### Fixed
* Use correct source of events

---

### 1.0.49
*(2017-08-31)*

#### Documentation
* Delete old information

---

### 1.0.47
*(2017-08-30)*

#### Improvements
* Improve UI

---

### 1.0.46
*(2017-08-11)*

#### Documentation
* Mail Log

---

### 1.0.45
*(2017-08-10)*

#### Improvements
* Cancel emails whose email chain was removed from trigger
* Add identifier to cross sell block

#### Fixed
* Allow deselect the cancellation event
* correctly define email delay when sending email at specific time using 'at' option

#### Documentation
* Documentation for coupon code expiration date and displaying only first item

---

### 1.0.44
*(2017-07-19)*

#### Fixed
* YAML require

---

### 1.0.43
*(2017-07-14)*

#### Improvements
* Ability to delete email queues

---

### 1.0.42
*(2017-07-14)*

#### Bugfixes
* Escape colon character in yaml files

#### Improvements
* Performance (added indexes to db tables)

---

### 1.0.41
*(2017-06-29)*

#### Documentation
* Description of template methods and Mail Log section

---

### 1.0.40
*(2017-06-29)*

#### Features
* New condition 'Product Attribute Value Comparison'

#### Improvements
* Show more information about email queue cancellation event

---

### 1.0.39
*(2017-06-21)*

#### Bugfixes
* Compatibility with Ebizmarts Mandrill

---

### 1.0.38
*(2017-06-20)*

#### Bugfixes
* Problem with serializing email arguments
* Do not add 'test' subject for emails sent manually through email queue

#### Improvements
* Display cancellation event key

---

### 1.0.37
*(2017-06-13)*

#### Fixed
* Issue with queue

---

### 1.0.36
*(2017-06-12)*

#### Bugfixes
* Email is not sent to admin if multiple email addresses specified

---

### 1.0.35
*(2017-05-12)*

#### Features
* New conditions for order condition group

#### Bugfixes
* Error displaying cross sell products

---

### 1.0.34
*(2017-05-04)*

#### Bugfixes
* Properly place fragment part in generated URLs
* Fix issue with the product subselection condition

---

### 1.0.33
*(2017-05-03)*

#### Bugfixes
* Compatibility with the versions before introducing ability to send emails every X period (affects since 1.0.32)
* Add cross-sell html to base theme

---

### 1.0.32
*(2017-04-28)*

#### Features
* Ability to send emails every X days/weeks/months/years

#### Bugfixes
* Header is displayed like a field

#### Improvements
* Order event, use customer name from address if it's empty in order

---

### 1.0.31
*(2017-04-19)*

#### Bugfixes
* Show correct time values at the Mail Log message

#### Improvements
* Separate cron group for Follow Up Email extension

---

### 1.0.30
*(2017-04-12)*

#### Features
* New method 'getResumeUrl' to automatically login customers

---

### 1.0.29
*(2017-03-29)*

#### Features
* Ability to validate concrete number of products in cart/order
* New event 'Customer Review Approved'

---

### 1.0.28
*(2017-03-24)*

#### Bugfixes
* Fix error while restoring shopping cart

---

### 1.0.27
*(2017-03-20)*

#### Features
* Ability to validate events in mass action according trigger's rules

#### Bugfixes
* Correctly set numbers of affected records in response messages
* Fix issue with the condition "Shopping Cart products available for purchase"

---

### 1.0.26
*(2017-03-17)*

#### Bugfixes
* Trigger email chains reset after changing status of triggers in mass action

---

### 1.0.25
*(2017-03-16)*

#### Improvements
* Check order object before processing it

---

### 1.0.24
*(2017-03-07)*

#### Bugfixes
* Fix issue with 'Shipping Address' rules

---

### 1.0.23
*(2017-03-02)*

#### Bugfixes
* Use correct store ID for new product review event (affects all)

---

### 1.0.21
*(2017-02-20)*

#### Fixed
* Fixed an issue with abandoned cart trigger

---

### 1.0.20
*(2017-01-30)*

#### Bugfixes
* Correctly display email queue view page (affects all)

---

### 1.0.19
*(2017-01-25)*

#### Bugfixes
* Fixed problem when using SKU condition in products selection

---

### 1.0.18
*(2017-01-23)*

#### Fixed
* Fixed an issue with utm_ tags

---

### 1.0.17
*(2017-01-06)*

#### Fixed
* Fixed an issue with restoring cart

---

### 1.0.16
*(2017-01-05)*

#### Features
* Implemented ability to send follow up emails only to specified (administrator) email. Is useful for receive internal reminders (new review, order, customer etc)

#### Fixed
* Fixed an issue with abandoned cart event

---

### 1.0.15
*(2016-12-23)*

#### Improvements
* Triggers grid

---

### 1.0.14
*(2016-12-16)*

#### Bugfixes
* Fixed an issue with registering the event 'Order obtained new status' (affects all)
* Fixed an issue with Review Request template (error if product already removed)

#### Improvements
* Ability to use product attributes in rules

---

### 1.0.13
*(2016-09-14)*

#### Fixed
* Limit number of cart rules

---

### 1.0.12
*(2016-09-08)*

#### Fixed
* Compatibility issue
* Set attribute element as a text and add available options for region condition

---

### 1.0.11
*(2016-08-11)*

#### Improvements
* New rule condition 'Shopping cart products available for purchase'

---

### 1.0.10
*(2016-07-28)*

#### Fixed
* Fixed an issue with store base url

---

### 1.0.8
*(2016-06-24)*

#### Fixed
* Compatibility with Magento 2.1

---

### 1.0.7
*(2016-05-27)*

#### Improvements
* Added store filter to events grid

---

### 1.0.6
*(2016-05-20)*

#### Improvements
* Support of different mail transfer agents

#### Fixed
* Fixed issue with SalesRule naming (after update to 2.0.6)
* Fixed issue with multi-store emails
* Changed external links params (code to hash)
* Issues with rules
* Fixed an issue with empty Restre Cart url
* Fixed and issue with cross sells
* Fixed possible issue with non-secure url for ajax capture

---

### 1.0.4
*(2016-04-11)*

#### Fixed
* Fixed an issue with menu

---

### 1.0.3
*(2016-03-28)*

#### Improvements
* Added new tab to customer edit page with FUE emails
* Ability to setup coupon generation rules (length, prefix, suffix, dash every X chars)
* Check coupon type (fixed or auto generation)
* Improved clean history (logs) feature
* Improved current time (local/gmt) validation
* i18n

#### Fixed
* Fixed an issue with cross-sell products
* Fixed wrong link in menu
* Fixed an issue with wrong link to Settings

#### Documentation
* Updated installation steps

---

### 1.0.2
*(2016-02-18)*

#### Fixed
* Fixed an issue with cronjob (wrong path to class)
* Fixed an issue with parse error in crontab.xml

#### Improvements
* Added new column to trigger grid with general information
* Added ability to preview cross-sell products in template preview

#### Documentation
* Added base user manual

---

------
# Submodule mirasvit/module-email-designer
### 1.0.15
*(2017-10-30)*

#### Fixed
* Сompatibility with PHP > 7.0.0

---

### 1.0.14
*(2017-09-04)*

#### Fixed
* Fix for compatibility with Magento 2.2.0rc

---

### 1.0.13
*(2017-08-31)*

#### Improvements
* Create repository for templates
* Method 'getItemOptions' for displaying options selected for ordered item

---

### 1.0.12
*(2017-05-29)*

#### Improvements
* Methods to retrieve wishlist products
* Fallback mechanism for method 'getCustomerName()' in order context

---

### 1.0.11
*(2017-04-28)*

#### Improvements
* Fallback mechanism for method 'getCustomerName()' in order context

---

### 1.0.10
*(2017-03-16)*

#### Bugfixes
* Fix some variables do not exist until they explicitly called (affects all)

---

### 1.0.9
*(2017-01-27)*

#### Bugfixes
* Display value for method 'getCustomerEmail' in preview emails (affects all)

### 1.0.8
*(2017-01-26)*

#### Bugfixes
* Display coupon code in preview mode (affects all)
* Fixed an issue with image path

---

### 1.0.6 1.0.7
*(2016-09-08)*

#### Fixed
* Fixed an issue with image URL

---

### 1.0.5
*(2016-06-21)*

#### Fixed
* Fixed an issue with hard coded store id

---

### 1.0.3 1.0.4
*(2016-05-06)*

#### Fixed
* Fixed an issue with multi-store

---

### 1.0.2
*(2016-04-18)*

#### Fixed
* Fixed an issue during compilation (setup:di:compile-multi-tenant)
* Fixed an issue with fatal error when orders/carts are not exists

#### Improvements
* Ability to use wishlists in template
* i18n
* Showing php error, if template syntax not correct

---

------
# Submodule mirasvit/module-report

## 1.2.27
*(2017-12-07)*

#### Fixed
* filters by "Customers > Products" and "Abandoned Carts > Abandoned Products" columns

---

## 1.2.26
*(2017-12-06)*

#### Fixed
* filter by "Products" column

---

## 1.2.25
*(2017-12-05)*

#### Fixed
* Issue with active dimension column

---

## 1.2.24
*(2017-11-30)*

#### Fixed
* Issue with export in Magento 2.1.8

---

## 1.2.23
*(2017-11-27)*

#### Fixed
* Issue with "Total" value of non-numeric columns

---

## 1.2.22
*(2017-11-15)*

#### Fixed
* Issue with export to XML

---

## 1.2.21
*(2017-11-03)*

#### Fixed
* Properly replicate temporary tables
* An issue with builing relations
* Issue with finding way to join tables

---

## 1.2.20
*(2017-10-30)*

#### Fixed
* An issue with sales overview report when customer segments used

---

## 1.2.19
*(2017-10-30)*

#### Fixed
* Issue with export to CSV (Magento 2.1.9)

---

## 1.2.18
*(2017-10-26)*

#### Fixed
* Issue with long replication

---

## 1.2.17
*(2017-10-20)*

#### Fixed
* Fixed css bug
* Compare for leap year

---

## 1.2.16
*(2017-09-28)*

#### Fixed
* Compatibility with php 7.1.9

---

## 1.2.15
*(2017-09-26)*

#### Fixed
* M2.2

---

## 1.2.14
*(2017-09-18)*

#### Fixed
* Fix report email notification using 'Send Now' function

---

## 1.2.13
*(2017-08-09)*

#### Fixed
* Conflict with other reports extensions

---

## 1.2.12
*(2017-08-02)*

#### Improvements
* New Report Columns

---

## 1.2.11
*(2017-07-19)*

#### Fixed
* Display option labels instead of values for dashboard widgets

---

## 1.2.10
*(2017-07-12)*

#### Fixed
* Issue with Eav attributes

---

## 1.2.9
*(2017-07-11)*

#### Improvements
* New Charts

---

## 1.2.8
*(2017-06-21)*

#### Fixed
* Proper filter product details report by current product ID

## 1.2.7
*(2017-06-21)*

#### Improvements
* Refactoring

---

## 1.2.6
*(2017-06-01)*

---

## 1.2.5
*(2017-05-31)*

#### Improvements
* Added field to relation

---

## 1.2.4
*(2017-05-15)*

#### Fixed
* Issue with column ordering

---

## 1.2.3
*(2017-05-04)*

#### Bugfixes
* Fixed an issue with compound columns of type simple

#### Improvements
* Changed default multiselect control to ui-select
* Chart resizing

---

## 1.2.2
*(2017-03-21)*

#### Improvements
* Performance

#### Fixed
* Fixed an issue with join returing customers

---

## 1.2.1
*(2017-03-06)*

#### Improvements
* Disabled wrong filters for day/hour/month/quarter/week/year

#### Fixed
* Fixed an issue with table joining
* Fixed an issue with filters
* Issue with rounding numbers in chart

---

## 1.2.0
*(2017-02-27)*

#### Fixed
* Minor issues
* Fixed an issue with replication

---

## 1.1.14
*(2017-01-31)*

#### Fixed
* Dashboard

---

## 1.1.12
*(2017-01-25)*

#### Fixed
* Backward compatibility
* Fixed an issue with bookmarks

---

## 1.1.11
*(2017-01-20)*

#### Fixed
* fixed an issue with tz

---

## 1.1.9, 1.1.10
*(2017-01-13)*

#### Fixed
* Fixed an issue with timezones
* Fixed an issue with dates

## 1.1.7, 1.1.8

*(2016-12-15)*

#### Fixed
* Fixed an issue in toolbar
* Fixed an issue with date filter

---

## 1.1.6
*(2016-12-09)*

#### Improvements
* Compatibility with M2.2

---

## 1.1.5
*(2016-09-27)*

#### Fixed
* Fixed an issue with moment js

---

## 1.1.4
*(2016-09-13)*

#### Fixed
* Removed limit on export reports (was 1000 rows)

---

## 1.1.3
*(2016-09-05)*

#### Improvements
* Changed product type column type

---

## 1.1.2
*(2016-09-01)*

#### Improvements
* Added Product Type column

---

## 1.1.1
*(2016-08-15)*

#### Fixed
* Fixed an issue with exporting

---

## 1.1.0
*(2016-07-01)*

#### Fixed
* Rename report.xml to mreport.xsd (compatiblity with module-support)

---

## 1.0.4
*(2016-06-24)*

#### Fixed
* Compatibility with Magento 2.1

---

## 1.0.3
*(2016-05-31)*

#### Fixed
* Fixed an issue with currency symbol

---

## 1.0.2
*(2016-05-27)*

#### Fixed
* Add store filter

---

## 1.0.1
*(2016-05-25)*

#### Fixed
* Removed font-awesome

---

## 1.0.0
*(2016-05-19)*

#### Improvements
* Export
* Refactoring
* Table join logic

#### Fixed
* Fixed an issue with joining tables
* Chart - multi columns

------
# Submodule mirasvit/module-email-report
## 1.0.4
*(2017-10-30)*

#### Fixed
* compatibility with PHP > 7.0.0

---

## 1.0.3
*(2017-09-19)*

#### Fixed
* require correct version of module-report

---

## 1.0.2
*(2017-09-04)*

#### Fixed
* fix for compatibility with Magento 2.2.0rc

---

## 1.0.1
*(2017-09-01)*

#### Fixed
* Disable unfinished report provided by module

---

## 1.0.0
*(2017-08-30)*

#### Features
* integrate with Follow Up Email

## 0.0.0-alpha1
*(2016-04-18)*

#### Features
* Ability to manage email campaigns

---

------
# Submodule mirasvit/module-message-queue
## 1.0.2
*(2017-11-21)*

#### Fixed
* compatibility with Magento EE

---

## 1.0.1
*(2017-10-31)*

#### Fixed
* do not enqueue messages if 'queue' table is not created yet

---

## 1.0.0
*(2017-10-31)* 

#### Feature
* RabbitMQ and MySQL drivers

# Publish message

    /** @var \Mirasvit\Mq\Api\PublisherInterface $publisher */
    $publisher = $this->objectManager->create('Mirasvit\Mq\Api\PublisherInterface');

    $publisher->publish('mirasvit.event', [microtime(true)]);
    
# Listen

di.xml

    <type name="Mirasvit\Mq\Api\Repository\ConsumerRepositoryInterface">
        <arguments>
            <argument name="consumers" xsi:type="array">
                <item name="notificator" xsi:type="array">
                    <item name="queue" xsi:type="string">mirasvit.event</item>
                    <item name="callback" xsi:type="string">Mirasvit\Testing\Model\Processor::process</item>
                </item>
            </argument>
        </arguments>
    </type>

------
# Submodule mirasvit/module-event
## 1.1.10
*(2017-12-01)*

#### Fixed
* Properly retrieve attribute values
* correctly detect 'Product / QTY Reduced' event

---

## 1.1.9
*(2017-11-24)*

#### Fixed
* Missing customer_name parameter in the 'customer birthday' event

---

## 1.1.8
*(2017-11-23)*

#### Fixed
* Properly validate total count/qty of products in cart/order
* Set Customer: Group condition as multiselect
* Register 'order status change' event only when status really changed

---

## 1.1.7
*(2017-11-22)*

#### Fixed
* use customer email as unique key for newsletter events

---

## 1.1.6
*(2017-11-01)*

#### Improvements
* Move error event to module-notificator

---

## 1.1.5
*(2017-10-31)*

#### Fixed
* register method may return boolean false

---

## 1.1.4
*(2017-10-30)*

#### Fixed
* Properly load customer model

---

## 1.1.3
*(2017-10-30)*

#### Fixed
* error with review related events

---

## 1.1.2
*(2017-10-26)*

#### Improvements
* Customer condition 'Last Activity'
* Handle API errors
* Order condition 'Order Updated At Time (24H format)'
* move email capture function from follow up email to module event
* Add all follow up email conditions
* Save customer_name, customer_email values with event registration

#### Fixed
* Error event

---

## 1.1.1
*(2017-10-19)*

#### Fixed
* Bump version number

---

## 1.1.0
*(2017-10-19)*

#### Features
* Shipping Address Conditions
* Event 'Customer Birthday'
* Event 'Review Approved'
* Event 'New item added to wishlist'
* Event 'Wishlist shared'

#### Fixed
* rename attribute code

---

## 1.1.0-beta6
*(2017-10-12)*

#### Features
* Event 'Product QTY Decreased'
* new event 'New System Notification'
* Ability to add groups of conditions to events
* Product subselection conditions
* Event 'Admin Logged In'
* Event 'Failed Login Admin'
* Ability to add custom attributes and conditions to EventData

#### Improvements
* Last heartbeat schedule condition
* Add Store Event Data with store related conditions

#### Fixed
* add custom attributes only if they added

---

## 1.1.0-beta5
*(2017-09-27)*

#### Improvements
* compatibility with Magento 2.2

---

## 1.1.0-beta4
*(2017-09-27)*

#### Improvements
* expand all params specified in the getEventData method

---

## 1.1.0-beta3
*(2017-09-19)*

#### Fixed
* do not register events before module installation

---

## 1.1.0-beta2
*(2017-09-18)*

#### Fixed
* Order status event

---

## 1.1.0-beta1
*(2017-09-18)*

#### Improvements
* convert all events to plugins

---

## 1.0.1
*(2017-06-15)*

#### Fixed
* DI
* Pool

---
