SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `container`;
CREATE TABLE `container` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flag` enum('container') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'container',
  `projectID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `containermapitem`;
CREATE TABLE `containermapitem` (
  `containerID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL,
  UNIQUE KEY `containerID` (`containerID`,`itemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `informationarchitecture`;
CREATE TABLE `informationarchitecture` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `LFT` int(11) NOT NULL,
  `RGT` int(11) NOT NULL,
  `itemID` int(11) DEFAULT NULL,
  `containerID` int(11) DEFAULT NULL,
  `projectID` int(11) NOT NULL,
  `sectionID` int(11) NOT NULL,
  `flag` enum('container','item','root') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'item',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `flag` enum('root','item') COLLATE utf8_unicode_ci DEFAULT 'item',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UID` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `login` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registrationDatetime` datetime DEFAULT NULL,
  `ACL` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `index` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `route` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `page` VALUES ('1', 'content', '<p>\r\nVerantwortlich für die Inhalte dieser Seite ist:<br />\r\n</p>\r\n<p>\r\n<div class=\"btn-group\">\r\n  <a class=\"btn btn-primary\" href=\"http://www.cs.uni-paderborn.de/fachgebiete/fg-mci/personen/szwillus.html\">Prof. Dr. Gerd Szwillus</a>\r\n  <a class=\"btn btn-primary\" href=\"http://www.cs.uni-paderborn.de/fachgebiete/fg-mci.html\">FG MCI</a>\r\n  <a class=\"btn btn-primary\" href=\"https://www.cs.uni-paderborn.de/impressum.html\">Institut für Informatik</a>\r\n</div>\r\n</p>\r\n', '/impressum');
INSERT INTO `page` VALUES ('2', 'title', 'Impressum', '/impressum');
INSERT INTO `page` VALUES ('3', 'content', '<p>\r\nDie personenbezogenen Daten die vom Probanden während eines Experiments optional eingegeben werden können und die Experimentergebnisse werden bis auf die ID des Projekts unabhängig von einander gespeichert. Verknüpft werden Die Daten können nur vom Projektersteller und von den Verantwortlichen dieser Seite eingesehen werden. Die Daten werden von den Verantwortlichen dieser Seite nicht an Dritte weitergegeben.\r\n</p>', '/privacy');
INSERT INTO `page` VALUES ('4', 'title', 'Datenschutzerklärung', '/privacy');

DROP TABLE IF EXISTS `proband`;
CREATE TABLE `proband` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `forename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `surname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `startDatetime` datetime DEFAULT NULL,
  `endDatetime` datetime DEFAULT NULL,
  `createDatetime` datetime DEFAULT NULL,
  `flag` enum('public','protected','private') COLLATE utf8_unicode_ci DEFAULT 'protected',
  `welcomeText` text COLLATE utf8_unicode_ci,
  `instructionText` text COLLATE utf8_unicode_ci,
  `tributeText` text COLLATE utf8_unicode_ci,
  `offerButton` int(1) DEFAULT '1',
  `probandForm` int(1) DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `projectmapsection`;
CREATE TABLE `projectmapsection` (
  `projectID` int(11) NOT NULL,
  `sectionID` int(11) NOT NULL,
  UNIQUE KEY `projectID` (`projectID`,`sectionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `result`;
CREATE TABLE `result` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `projectID` int(11) NOT NULL,
  `startDatetime` datetime DEFAULT NULL,
  `endDatetime` datetime DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `resultdata`;
CREATE TABLE `resultdata` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `resultTaskID` int(11) DEFAULT NULL,
  `itemID` int(11) DEFAULT NULL,
  `informationarchitectureID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `resulttask`;
CREATE TABLE `resulttask` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `resultID` int(11) DEFAULT NULL,
  `taskID` int(11) DEFAULT NULL,
  `startDatetime` datetime DEFAULT NULL,
  `endDatetime` datetime DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `flag` enum('hit','skipped','miss','unknown') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `section`;
CREATE TABLE `section` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `shortcut` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `section` VALUES ('1', 'Header', 'Erzeugt eine Navigation am oberen Rand des Layouts in der die Linkelemente horizontal angeordnet werden.', 'h:');
INSERT INTO `section` VALUES ('2', 'Article', 'Erzeugt Linkelemente innerhalb des Fließtext der Layouts.', 'r:');
INSERT INTO `section` VALUES ('3', 'Aside', 'Erzeugt eine Navigation am linken Rand des Layouts in der Linkelemente vertikal angeordnet werden.', 's:');
INSERT INTO `section` VALUES ('4', 'Footer', 'Erzeugt eine Navigation am unteren Rand des Layouts mit fünf horizontale angeordnete Linklisten in der die Linkelemente vertikal angeordnet werden.', 'f:');

DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `index` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `flag` enum('view.variable') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `task` text COLLATE utf8_unicode_ci,
  `itemID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `loginID` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tokenRequestDatetime` datetime DEFAULT NULL,
  `tokenExpirationDatetime` datetime DEFAULT NULL,
  `status` enum('revoked','granted') COLLATE utf8_unicode_ci DEFAULT 'granted',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `forename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `surname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;