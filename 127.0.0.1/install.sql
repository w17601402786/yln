DROP TABLE IF EXISTS `yln_blog`;
CREATE TABLE `yln_blog` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `qq` varchar(255) DEFAULT NULL,
  `info` text,
  `time` datetime DEFAULT NULL,
  `status` int(2) DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `yln_chat_data`;
CREATE TABLE `yln_chat_data` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `data` text,
  `name` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `qq` varchar(255) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `yln_comment`;
CREATE TABLE `yln_comment` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `qq` varchar(255) DEFAULT NULL,
  `info` text,
  `time` datetime DEFAULT NULL,
  `blog_Id` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `yln_index`;
CREATE TABLE `yln_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `copy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
INSERT INTO `yln_index` VALUES (1,'忆流年小站','1',144,454,'无名小卒');
DROP TABLE IF EXISTS `yln_user`;
CREATE TABLE `yln_user` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `qq` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `birth` date DEFAULT NULL,
  `reminder` int(11) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `regtime` datetime DEFAULT NULL,
  `lasttime` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `safecode` varchar(255) DEFAULT NULL,
  `en_searched` int(1) DEFAULT '1',
  `sex` varchar(9) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;