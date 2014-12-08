ALTER TABLE `item` MODIFY COLUMN `flag`  enum('root','item','link') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'item' AFTER `projectID`;

ALTER TABLE `informationarchitecture` ADD COLUMN `linkToInformationarchitectureID`  int NULL AFTER `flag`, ADD COLUMN `linkToItemID`  int NULL AFTER `linkToInformationarchitectureID`;