/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50616
Source Host           : localhost:3306
Source Database       : bachelorarbeit

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2014-08-20 16:54:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for section
-- ----------------------------
DROP TABLE IF EXISTS `section`;
CREATE TABLE `section` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `shortcut` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of section
-- ----------------------------
INSERT INTO `section` VALUES ('1', 'Header', 'Erzeugt eine Navigation am oberen Rand des Layouts in der die Linkelemente horizontal angeordnet werden.', 'h:');
INSERT INTO `section` VALUES ('2', 'Article', 'Erzeugt Linkelemente innerhalb des Fließtext der Layouts.', 'r:');
INSERT INTO `section` VALUES ('3', 'Aside', 'Erzeugt eine Navigation am linken Rand des Layouts in der Linkelemente vertikal angeordnet werden.', 's:');
INSERT INTO `section` VALUES ('4', 'Footer', 'Erzeugt eine Navigation am unteren Rand des Layouts mit fünf horizontale angeordnete Linklisten in der die Linkelemente vertikal angeordnet werden.', 'f:');
