# CHANGELOG

The following shortcuts will be used:
"+": new feature
"-": delete feature
"~": fixed bug

```
## 1.3
+ Add recapo.xml configuration file
+ Add library as submodul for Upload
~ Add folder app and folder web

## 1.2
+ back-end: display current version of recapo in the footer, added constant for the version within the index.php
+ back-end: added export of extended informationarchitecture to XML for ReCaLys which contains only the cards (every container will be replaced by its cards), so the export can not be imported again
+ front-end: supports cards beneath containers, so the export of the extended informationarchitecture does (gotta check other files if this feature is possible everywhere)
~ back-end: fixed the picture pathes (from dates to names)
~ imporved index.php, added "simple settings" section
~ cleanup of javascript bibs
~ fixed pathes: everything is below "/backend/*", e.g. "/help" moved to "/backend/help" so the experiments could have urls like recapo.de/help etc. only an url with backend is impossible. added simple javascript validation, but no php validation. however even if the url is "/backend" it has no effect to recapo, the experiment just cannot be opened
- back-end: deleted extended informationarchitecture to CSV, XML should be enough first

## 1.1
+ back-end: add version information within the footer
~ back-end: merged the link to the front-end, the edit button and the delete button into one button group in the experiment administration view
~ back-end: when creating a new experiment the schedulingStartDatetime is set properly
~ back-end: the sync between experiment and url has been optimized (didnt work perfectly, but better as before)
~ back-end: after creating a new experiment the dummy element in the IA gets the correct flag "root" instead of int(0)
+ back-end: im- and export supports the function id(ID:Name) and link(JumpToID:Name)

+ front-end: lightgrey background for the control panels inside the front-end
~ front-end: shorter text at the leaf card, removed breadcrumb there
+ front-end: add control button to go one level higher within the IA (=back button)
~ front-end: the breadcrumb shows the correct path within the IA
+ front-end: supports links and cycles within the IA

### MySQL database has to be updated
+ ALTER TABLE `item` MODIFY COLUMN `flag`  enum('root','item','link') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'item' AFTER `projectID`;
+ ALTER TABLE `informationarchitecture` ADD COLUMN `linkToInformationarchitectureID`  int NULL AFTER `flag`, ADD COLUMN `linkToItemID`  int NULL AFTER `linkToInformationarchitectureID`;


## 1.0 (2014-05-31)
initial version
```
