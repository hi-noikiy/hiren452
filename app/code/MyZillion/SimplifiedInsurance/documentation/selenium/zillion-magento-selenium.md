# Magento 2 Simplified Module Selenium Tests

## Getting started

The following sections document the considerations that must be taken to run the automatic tests cases implemented with Selenium IDE for the Zillion Module of Magento. The guide will cover the install of the IDE, the parameters settings to run the tests and some other considerations that could be useful in different scenarios.

### Installing Selenium IDE

Download [Selenium IDE](https://www.selenium.dev/selenium-ide/ "https://www.selenium.dev/selenium-ide/") for either Chrome or Firefox. It requires no additional setup other than installing the extension on the web browser.

- Google Chrome Extension:  [https://chrome.google.com/webstore/detail/selenium-ide/mooikfkahbdckldjjndioackbalphokd](https://chrome.google.com/webstore/detail/selenium-ide/mooikfkahbdckldjjndioackbalphokd "https://chrome.google.com/webstore/detail/selenium-ide/mooikfkahbdckldjjndioackbalphokd")
- Mozilla Firefox Extension:  [https://addons.mozilla.org/en-GB/firefox/addon/selenium-ide/](https://addons.mozilla.org/en-GB/firefox/addon/selenium-ide/ "https://addons.mozilla.org/en-GB/firefox/addon/selenium-ide/")

### Importing a Test Case Suite

1. Download the latest Selenium file containing the test cases from [here](https://tracker.serfe.com/file_download.php?file_id=88433&type=bug "https://tracker.serfe.com/file_download.php?file_id=88433&type=bug").
2. With the extension installed and opened, a main menu will appear. On it, select the option 'Open an existing project' and import the required  _.side_  file containing the test cases that will be run.
3. Once the file is imported, the  **test cases**  will appear on the left section of the window. At the right section, the  **steps**  of each case will appear, including the target and the value of the command. At the bottom panel, the  **logger messages**  will be shown during the tests' running. An image of the Selenium IDE window and its sections is included below.
   ![selenium-window](https://i.ibb.co/PY0sXLh/Screenshot-from-2021-01-08-14-19-23.png)

### Running Tests

- There are two ways of running tests in Selenium IDE. The first is  **running all tests**, that will sequentially run each test contained in the suite. The second, is  **running each tests individually**, selecting one of them and running it. These actions can be done with the first two 'Play' buttons in the right panel of the window.
  ![selenium running buttons](https://i.ibb.co/sjBkTGM/Screenshot-from-2021-01-08-14-37-50.png)
- Another consideration to run tests with Selenium is the **Test Execution Speed**. This command allows to change dynamically the time lapse between the execution of each step of the test. This can be useful when the task doesn't need to be visually controlled in all of the steps, or when the page has sites or components that take longer to be loaded than others. It's important to remember that this speed is adjusted DYNAMICALLY during the execution of the test. This allows to change the speed in some particular parts and then set it back in others.
  ![selenium time button](https://i.ibb.co/RH0zmjm/Screenshot-from-2021-01-08-15-46-33.png%22%20alt=%22Screenshot-from-2021-01-08-15-46-33)

## Zillion Magento Tests

Next, the implemented test cases for the module are described. It's relevant to mention that ALL the cases were developed only for the  **Staging instance of the Zillion Magento module**. Any future change in this instance will probably require the update of some of the cases implemented. Besides, the cases contemplate only the behavior of the module in the Magento environment. The effects of this behaviors  **are not tested in any other platforms**  like Zillion CustomerCenter, but may be useful to check that the results from the testing tasks are the desired in each other related platform.

### Prerequisites to Run the Cases

1. Having access and be **logged in with an admin account** for the staging site.
2. If you're testing in a different environment than Staging, register a test account with the following data:
   * **Email:** example@selenium.com
   * **Password:** testing2021!

### Describing the Cases*

1. [ ] **Checking module credentials.**  Consist on accessing the Magento Admin Backend and testing different values for the auth credentials used in the module. The alert messages thrown by the module are tested too.
2. [ ] **Checking out items with the Zillion module disabled.**  Consist on accessing the Magento Admin Backend, disabling the app and performing the checkout of an item using an eligible zip code. The flow of the Frontend app is tested and the registration of the transaction in the Admin Backend is validated too. The tests cases include performing a checkout with a registered user account and a guest one.
3. [ ] **Checking out items with the Zillion module enabled and offering a quote offer insurance for an eligible zip code.**  Consist on accessing the Magento Admin Backend, enabling the app, setting the offer type parameter to QUOTE and performing the checkout of an item using an eligible zip code. The flow of the Frontend app is tested and the registration of the transaction in the Admin Backend is validated too, considering if the customer accepts the offer or not. The tests cases include performing a checkout with a registered user account and a guest one.
4. [ ] **Checking out items with the Zillion module enabled and offering a binder offer insurance for an eligible zip code.**  Consist on accessing the Magento Admin Backend, enabling the app, setting the offer type parameter to BINDER and performing the checkout of an item using an eligible zip code. The flow of the Frontend app is tested and the registration of the transaction in the Admin Backend is validated too, considering if the customer accepts the offer or not. The tests cases include performing a checkout with a registered user account and a guest one.
5. [ ] **Checking out items with the Zillion module enabled for an ineligible zip code.**  Consist on accessing the Magento Admin Backend, enabling the app and performing the checkout of an item using an ineligible zip code. The flow of the Frontend app is tested and the registration of the transaction in the Admin Backend is validated too. The tests cases include performing a checkout with a registered user account and a guest one.
6. [ ] **Shipping orders.**  Consist on accessing the Magento Admin Backend and performing the shipping of a Pending order. The flow of the Frontend app is not tested, but the registration of the transaction in the Admin Backend is validated.

_*It is important to mention that each case has a more detailed description of their tasks in each one of their steps. Also, the tests considers the existence of the **example@selenium.com** testing account. If the account is not registered in your testing environment, read the prerequisites before running the tests._

### Important Notes

- First of all, before running the tests is recommended to open the staging site and input the credentials, including the ones to access the page and the ones to access the module.  **The user must be logged in on the Admin Backend before the running the tests**. Not being logged may cause the fail of the automatic tests in the firsts steps of the case.
- For the cases affecting the Frontend Shop,  **a special testing account was created**. This account has two addresses registered, one using an eligible zip code and other with an ineligible code. The account was made to avoid the extra steps of register additional info of a customer, and it doesn't affect the flow of the shop or the module. Also, using two addresses ease the selection of a eligible/ineligible zip code, according the test case.  _No previous logging with this account is needed to run the tests_.
- When a test is running, pay attention to the loading time of the pages and the components of the site.  **If the pages are slowly loading, is recommended to reduce the Test Execution Speed by a half**.  _A fast execution of the tests cases on a slow loading page may cause some troubles on the automatic running_, for example, trying to click elements that were not rendered yet.
- Finally, is recommended  **NOT INTERACT with the page during the execution of the tests**. Selenium tries to operate with the real components and data of the page, and any kind of event like clicking or focusing an element that is not part of the test case may cause the failure of it.

### Checking the Results

- Once the tests were run, the results of each step of them can be visualized on the logger messages. Actually, it's not possible to export these results to any other file, but each message of the logger is useful to understand what's really happening on the test. Besides, each step description helps to make the command more understandable for the user running the case.
- It can be very intuitive but it is important to mention that the cases and steps successfully run are indicated in green, and the failed ones, in red. There are also yellow messages, but they indicate warning cases to attend on the run.  **A key part of the automatic testing is checking that the final results have a real value for the desired results**, and the messages of the logger is the mechanism that Selenium offers to communicate to the tester what is happening on the page.

<hr>
Copyright (c) 2021 Zillion, released under the New BSD License.
