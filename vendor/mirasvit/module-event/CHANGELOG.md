# Change Log
## 1.2.41
*(2021-05-11)*

#### Fixed
* Issue with product saving

---


## 1.2.40
*(2021-05-07)*

#### Fixed
* Event product qty reduced

---


## 1.2.39
*(2021-04-19)*

#### Improvements
* Small improvement in event observer

---


## 1.2.38
*(2021-03-09)*

#### Fixed
* Fixed the issue with the error during mass approvement of reviews

---


## 1.2.37
*(2021-01-29)*

#### Fixed
* fixed the issues with running integration tests

---


## 1.2.36
*(2020-09-07)*

#### Fixed
* fixed an issue with the checkout processing M2.4

---


## 1.2.35
*(2020-07-29)*

#### Improvements
* Support of Magento 2.4

---


## 1.2.34
*(2020-07-17)*

#### Fixed
* fixed and issue with the sending 'Product View' and 'Review' test emails

---


## 1.2.33
*(2020-03-11)*

#### Improvements
* added event "Mirasvit Helpdesk / New ticket"added event "Mirasvit Helpdesk / New ticket"

---


## 1.2.32
*(2020-02-10)*

#### Fixed
* fixed an issue with the setting up audience date filters

---


## 1.2.31
*(2020-01-02)*

#### Fixed
* issue with creating customer in admin panel

---


## 1.2.30
*(2019-12-20)*

#### Fixed
* fixed an issue with the 'Fallback to JQueryUI Compat activated'

---


## 1.2.29
*(2019-12-16)*

#### Improvements
* added new event "Change Group"

---


## 1.2.28
*(2019-12-12)*

#### Improvements
* Email capture logic

---


## 1.2.27
*(2019-09-14)*

#### Fixed
* EQP code refactoring

---

## 1.2.26
*(2019-03-29)*

#### Fixed
* Product attribute conditions does not work properly
* Conditions Combination rule does not have child conditions

---

## 1.2.25
*(2019-03-27)*

#### Features
* Product Out of stock event for Magento 2.3 version

---

## 1.2.24
*(2019-03-20)*

#### Fixed
* Errors sending test notifications

---

## 1.2.22
*(2019-01-18)*

#### Fixed
* Error sending test emails (negative limit)

---

## 1.2.21
*(2019-01-04)*

#### Improvements
* Amasty Order Status

---

## 1.2.20
*(2018-12-24)*

#### Fixed
* Error during submitting a review

---

## 1.2.19
*(2018-12-10)*

#### Fixed
* Order status condition does not work in rules

---

## 1.2.18
*(2018-12-07)*

#### Improvements
* Clear event log from admin

---

## 1.2.17
*(2018-11-29)*

#### Fixed
* Require Mirasvit core module

---

## 1.2.16
*(2018-11-29)*

#### Improvements
* support for M2.3

#### Fixed
* Issue with filtration by category ids in cart

---


## 1.2.15
*(2018-08-14)*

#### Fixed
* fixed an issue with the Abandoned Cart incorrect updating "Created At" date ([#18](../../issues/18))

---


## 1.2.14
*(2018-08-08)*

#### Fixed
* fixed an issue with the Fatal error: "Allowed memory size exhausted" during the test email sending

---


## 1.2.13
*(2018-07-13)*

#### Fixed
* fixed an issue with the payment methods rule conditions

---


## 1.2.12
*(2018-07-11)*

#### Fixed
* Condition 'Customer: Number of Orders' fails with error

---

## 1.2.11
*(2018-07-09)*

#### Improvements
* Refactoring

---

## 1.2.10
*(2018-07-03)*

#### Improvements
* Use latest order information in test and preview emails

---

## 1.2.9
*(2018-06-25)*

#### Fixed
* Product price change triggers the Abandoned Cart event of a cart which contains this product mirasvit/module-email[#96](../../issues/96)

---

## 1.2.8
*(2018-05-04)*

#### Fixed
* Suppress exceptions while capturing user data

---

## 1.2.7
*(2018-05-04)*

#### Features
* GDPR compliance: Ability to disable user data capturing

---

## 1.2.6
*(2018-04-23)*

#### Improvements
* Display hint message when event is not chosen

---

## 1.2.5
*(2018-03-09)*

#### Fixed
* Error while executing command setup:di:compile

---

## 1.2.4
*(2018-02-21)*

#### Fixed
* Error on creating customer account when Customer Login/Logout event is active

---


## 1.2.3
*(2018-02-20)*

#### Fixed
* Fix error during generation di.xml files (affects since 1.2.2)

---

## 1.2.2
*(2018-02-19)*

#### Fixed
* Helpdesk Event: register only new message, ignore updated

---

## 1.2.1
*(2018-02-14)*

#### Fixed
* Issue with app state

---

## 1.2.0
*(2018-02-09)*

#### Features
* New condition 'Product is one of top selling products' #11
* Condition 'Product is one of most recently added products' #10
* Product View event mirasvit/module-email#33
* Product View event

#### Improvements
* Register only active events #9

---

## 1.1.18
*(2018-02-05)*

#### Bugfixes
* Do not ignore validation of custom events

---

## 1.1.17
*(2018-01-30)*

#### Bugfixes
* Fix error 'Area code is not set' during execuing command 'bin/magento setup:update' #8

---

## 1.1.16
*(2018-01-29)*

#### Bugfixes
* Fix warning 'Invalid argument supplied for foreach' (since v. 1.1.15)

---

## 1.1.15
*(2018-01-29)*

#### Features
* API for observable events #6

---

## 1.1.14
*(2018-01-18)*

#### Bugfixes
* Product QTY decrease event is not triggered when an order placed mirasvit/module-notificator[#13](http://some.issue.tracker.com/13)
* Fix error filtering event grid by column 'Info'

---

## 1.1.13
*(2018-01-12)*

#### Bugfixes
* Customer Birthday event is not properly registered

---

## 1.1.12
*(2018-01-09)*

#### Fixed
* issue with params

---

### 1.1.10
*(2017-12-01)*

#### Fixed
* Properly retrieve attribute values
* Correctly detect 'Product / QTY Reduced' event

---

### 1.1.9
*(2017-11-24)*

#### Fixed
* Missing customer_name parameter in the 'customer birthday' event

---

### 1.1.8
*(2017-11-23)*

#### Fixed
* Properly validate total count/qty of products in cart/order
* Set Customer: Group condition as multiselect
* Register 'order status change' event only when status really changed

---

### 1.1.7
*(2017-11-22)*

#### Fixed
* Use customer email as unique key for newsletter events

---

### 1.1.6
*(2017-11-01)*

#### Improvements
* Move error event to module-notificator

---

### 1.1.5
*(2017-10-31)*

#### Fixed
* Register method may return boolean false

---

### 1.1.4
*(2017-10-30)*

#### Fixed
* Properly load customer model

---

### 1.1.3
*(2017-10-30)*

#### Fixed
* Error with review related events

---

### 1.1.2
*(2017-10-26)*

#### Improvements
* Customer condition 'Last Activity'
* Handle API errors
* Order condition 'Order Updated At Time (24H format)'
* Move email capture function from follow up email to module event
* Add all follow up email conditions
* Save customer_name, customer_email values with event registration

#### Fixed
* Error event

---

### 1.1.1
*(2017-10-19)*

#### Fixed
* Various fixes

---

### 1.1.0
*(2017-10-19)*

#### Features
* Shipping Address Conditions
* Event 'Customer Birthday'
* Event 'Review Approved'
* Event 'New item added to wishlist'
* Event 'Wishlist shared'

#### Fixed
* Rename attribute code

---

### 1.0.1
*(2017-06-15)*

#### Fixed
* DI
* Pool

---
