-- phpMyAdmin SQL Dump
-- version 2.11.9
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2012 年 08 月 08 日 14:16
-- 服务器版本: 5.1.26
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `ucenter`
--

-- --------------------------------------------------------

--
-- 表的结构 `uc_admins`
--

CREATE TABLE IF NOT EXISTS `uc_admins` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(15) NOT NULL DEFAULT '',
  `allowadminsetting` tinyint(1) NOT NULL DEFAULT '0',
  `allowadminapp` tinyint(1) NOT NULL DEFAULT '0',
  `allowadminuser` tinyint(1) NOT NULL DEFAULT '0',
  `allowadminbadword` tinyint(1) NOT NULL DEFAULT '0',
  `allowadmintag` tinyint(1) NOT NULL DEFAULT '0',
  `allowadminpm` tinyint(1) NOT NULL DEFAULT '0',
  `allowadmincredits` tinyint(1) NOT NULL DEFAULT '0',
  `allowadmindomain` tinyint(1) NOT NULL DEFAULT '0',
  `allowadmindb` tinyint(1) NOT NULL DEFAULT '0',
  `allowadminnote` tinyint(1) NOT NULL DEFAULT '0',
  `allowadmincache` tinyint(1) NOT NULL DEFAULT '0',
  `allowadminlog` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `uc_admins`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_applications`
--

CREATE TABLE IF NOT EXISTS `uc_applications` (
  `appid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(16) NOT NULL DEFAULT '',
  `name` char(20) NOT NULL DEFAULT '',
  `url` char(255) NOT NULL DEFAULT '',
  `authkey` char(255) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL DEFAULT '',
  `viewprourl` char(255) NOT NULL,
  `apifilename` char(30) NOT NULL DEFAULT 'uc.php',
  `charset` char(8) NOT NULL DEFAULT '',
  `dbcharset` char(8) NOT NULL DEFAULT '',
  `synlogin` tinyint(1) NOT NULL DEFAULT '0',
  `recvnote` tinyint(1) DEFAULT '0',
  `extra` mediumtext NOT NULL,
  `tagtemplates` mediumtext NOT NULL,
  PRIMARY KEY (`appid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 导出表中的数据 `uc_applications`
--

INSERT INTO `uc_applications` (`appid`, `type`, `name`, `url`, `authkey`, `ip`, `viewprourl`, `apifilename`, `charset`, `dbcharset`, `synlogin`, `recvnote`, `extra`, `tagtemplates`) VALUES
(1, 'ECSHOP', '易多商城', 'http://localhost/edo/cp', '5ff6Sk7YZvMkvokeliz6mqI8V1W7SGDVYCGv40sD7j8', '127.0.0.1', '', 'uc.php', '', '', 1, 1, 'a:1:{s:7:"apppath";s:0:"";}', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="template"><![CDATA[]]></item>\r\n</root>'),
(2, 'OTHER', '易多社区', 'http://localhost/edo', '5e45BIAUSwWXIKJHWLg5CEv61L922/NbAkGav+1HDkM', '', '', 'uc.php', '', '', 1, 1, 'a:1:{s:7:"apppath";s:0:"";}', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="template"><![CDATA[]]></item>\r\n</root>');

-- --------------------------------------------------------

--
-- 表的结构 `uc_badwords`
--

CREATE TABLE IF NOT EXISTS `uc_badwords` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `admin` varchar(15) NOT NULL DEFAULT '',
  `find` varchar(255) NOT NULL DEFAULT '',
  `replacement` varchar(255) NOT NULL DEFAULT '',
  `findpattern` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `find` (`find`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `uc_badwords`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_domains`
--

CREATE TABLE IF NOT EXISTS `uc_domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` char(40) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `uc_domains`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_failedlogins`
--

CREATE TABLE IF NOT EXISTS `uc_failedlogins` (
  `ip` char(15) NOT NULL DEFAULT '',
  `count` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastupdate` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_failedlogins`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_feeds`
--

CREATE TABLE IF NOT EXISTS `uc_feeds` (
  `feedid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `appid` varchar(30) NOT NULL DEFAULT '',
  `icon` varchar(30) NOT NULL DEFAULT '',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(15) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `hash_template` varchar(32) NOT NULL DEFAULT '',
  `hash_data` varchar(32) NOT NULL DEFAULT '',
  `title_template` text NOT NULL,
  `title_data` text NOT NULL,
  `body_template` text NOT NULL,
  `body_data` text NOT NULL,
  `body_general` text NOT NULL,
  `image_1` varchar(255) NOT NULL DEFAULT '',
  `image_1_link` varchar(255) NOT NULL DEFAULT '',
  `image_2` varchar(255) NOT NULL DEFAULT '',
  `image_2_link` varchar(255) NOT NULL DEFAULT '',
  `image_3` varchar(255) NOT NULL DEFAULT '',
  `image_3_link` varchar(255) NOT NULL DEFAULT '',
  `image_4` varchar(255) NOT NULL DEFAULT '',
  `image_4_link` varchar(255) NOT NULL DEFAULT '',
  `target_ids` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`feedid`),
  KEY `uid` (`uid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `uc_feeds`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_friends`
--

CREATE TABLE IF NOT EXISTS `uc_friends` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `friendid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `direction` tinyint(1) NOT NULL DEFAULT '0',
  `version` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delstatus` tinyint(1) NOT NULL DEFAULT '0',
  `comment` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`version`),
  KEY `uid` (`uid`),
  KEY `friendid` (`friendid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `uc_friends`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_mailqueue`
--

CREATE TABLE IF NOT EXISTS `uc_mailqueue` (
  `mailid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `touid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `tomail` varchar(32) NOT NULL,
  `frommail` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `charset` varchar(15) NOT NULL,
  `htmlon` tinyint(1) NOT NULL DEFAULT '0',
  `level` tinyint(1) NOT NULL DEFAULT '1',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `failures` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `appid` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailid`),
  KEY `appid` (`appid`),
  KEY `level` (`level`,`failures`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `uc_mailqueue`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_memberfields`
--

CREATE TABLE IF NOT EXISTS `uc_memberfields` (
  `uid` mediumint(8) unsigned NOT NULL,
  `blacklist` text NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_memberfields`
--

INSERT INTO `uc_memberfields` (`uid`, `blacklist`) VALUES
(1, ''),
(2, ''),
(16, ''),
(17, ''),
(18, ''),
(19, ''),
(20, ''),
(21, ''),
(22, ''),
(23, ''),
(24, ''),
(25, ''),
(26, ''),
(27, ''),
(28, ''),
(29, ''),
(30, ''),
(31, ''),
(32, ''),
(33, ''),
(34, ''),
(35, ''),
(36, ''),
(37, ''),
(38, '');

-- --------------------------------------------------------

--
-- 表的结构 `uc_members`
--

CREATE TABLE IF NOT EXISTS `uc_members` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(15) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `email` char(32) NOT NULL DEFAULT '',
  `myid` char(30) NOT NULL DEFAULT '',
  `myidkey` char(16) NOT NULL DEFAULT '',
  `regip` char(15) NOT NULL DEFAULT '',
  `regdate` int(10) unsigned NOT NULL DEFAULT '0',
  `lastloginip` int(10) NOT NULL DEFAULT '0',
  `lastlogintime` int(10) unsigned NOT NULL DEFAULT '0',
  `salt` char(6) NOT NULL,
  `secques` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;


-- --------------------------------------------------------

--
-- 表的结构 `uc_mergemembers`
--

CREATE TABLE IF NOT EXISTS `uc_mergemembers` (
  `appid` smallint(6) unsigned NOT NULL,
  `username` char(15) NOT NULL,
  PRIMARY KEY (`appid`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_mergemembers`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_newpm`
--

CREATE TABLE IF NOT EXISTS `uc_newpm` (
  `uid` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_newpm`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_notelist`
--

CREATE TABLE IF NOT EXISTS `uc_notelist` (
  `noteid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `operation` char(32) NOT NULL,
  `closed` tinyint(4) NOT NULL DEFAULT '0',
  `totalnum` smallint(6) unsigned NOT NULL DEFAULT '0',
  `succeednum` smallint(6) unsigned NOT NULL DEFAULT '0',
  `getdata` mediumtext NOT NULL,
  `postdata` mediumtext NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `pri` tinyint(3) NOT NULL DEFAULT '0',
  `app1` tinyint(4) NOT NULL,
  `app2` tinyint(4) NOT NULL,
  PRIMARY KEY (`noteid`),
  KEY `closed` (`closed`,`pri`,`noteid`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- 导出表中的数据 `uc_notelist`
--

INSERT INTO `uc_notelist` (`noteid`, `operation`, `closed`, `totalnum`, `succeednum`, `getdata`, `postdata`, `dateline`, `pri`, `app1`, `app2`) VALUES
(1, 'updateapps', 1, 0, 0, '', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="1">\r\n <item id="appid"><![CDATA[1]]></item>\r\n <item id="type"><![CDATA[ECSHOP]]></item>\r\n <item id="name"><![CDATA[易多商城]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo/cp/]]></item>\r\n <item id="ip"><![CDATA[]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="UC_API"><![CDATA[http://localhost/edo/ucenter]]></item>\r\n</root>', 0, 0, 0, 0),
(2, 'updateapps', 1, 5, 0, '', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="1">\r\n <item id="appid"><![CDATA[1]]></item>\r\n <item id="type"><![CDATA[ECSHOP]]></item>\r\n <item id="name"><![CDATA[易多商城]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo/cp/]]></item>\r\n <item id="ip"><![CDATA[127.0.01]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="UC_API"><![CDATA[http://localhost/edo/ucenter]]></item>\r\n</root>', 1344312684, 0, -5, 0),
(3, 'updateapps', 1, 5, 0, '', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="1">\r\n <item id="appid"><![CDATA[1]]></item>\r\n <item id="type"><![CDATA[ECSHOP]]></item>\r\n <item id="name"><![CDATA[易多商城]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo/cp]]></item>\r\n <item id="ip"><![CDATA[127.0.01]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="UC_API"><![CDATA[http://localhost/edo/ucenter]]></item>\r\n</root>', 1344312816, 0, -5, 0),
(4, 'updateapps', 1, 1, 1, '', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="1">\r\n <item id="appid"><![CDATA[1]]></item>\r\n <item id="type"><![CDATA[ECSHOP]]></item>\r\n <item id="name"><![CDATA[易多商城]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo/cp]]></item>\r\n <item id="ip"><![CDATA[127.0.0.1]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="UC_API"><![CDATA[http://localhost/edo/ucenter]]></item>\r\n</root>', 1344320161, 0, 1, 0),
(5, 'updateapps', 1, 1, 1, '', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="1">\r\n <item id="appid"><![CDATA[1]]></item>\r\n <item id="type"><![CDATA[ECSHOP]]></item>\r\n <item id="name"><![CDATA[易多商城]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo/cp]]></item>\r\n <item id="ip"><![CDATA[127.0.0.1]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="2">\r\n <item id="appid"><![CDATA[2]]></item>\r\n <item id="type"><![CDATA[UCHOME]]></item>\r\n <item id="name"><![CDATA[易多社区]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo]]></item>\r\n <item id="ip"><![CDATA[]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="UC_API"><![CDATA[http://localhost/edo/ucenter]]></item>\r\n</root>', 1344320431, 0, 1, 0),
(6, 'updateapps', 1, 6, 1, '', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="1">\r\n <item id="appid"><![CDATA[1]]></item>\r\n <item id="type"><![CDATA[ECSHOP]]></item>\r\n <item id="name"><![CDATA[易多商城]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo/cp]]></item>\r\n <item id="ip"><![CDATA[127.0.0.1]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="2">\r\n <item id="appid"><![CDATA[2]]></item>\r\n <item id="type"><![CDATA[OTHER]]></item>\r\n <item id="name"><![CDATA[易多社区]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo]]></item>\r\n <item id="ip"><![CDATA[]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="UC_API"><![CDATA[http://localhost/edo/ucenter]]></item>\r\n</root>', 1344322681, 0, 1, -5),
(7, 'updateapps', 1, 2, 2, '', '<?xml version="1.0" encoding="ISO-8859-1"?><root> <item_1> <appid><![CDATA[1]]></appid> <type><![CDATA[ECSHOP]]></type> <name><![CDATA[易多商城]]></name> <url><![CDATA[http://localhost/edo/cp]]></url> <ip><![CDATA[127.0.0.1]]></ip> <charset><![CDATA[]]></charset> <synlogin><![CDATA[1]]></synlogin> <extra><![CDATA[a:1:{s:7:"apppath";s:0:"";}]]></extra> </item_1> <item_2> <appid><![CDATA[2]]></appid> <type><![CDATA[OTHER]]></type> <name><![CDATA[易多社区]]></name> <url><![CDATA[http://localhost/edo]]></url> <ip><![CDATA[]]></ip> <charset><![CDATA[]]></charset> <synlogin><![CDATA[1]]></synlogin> <extra><![CDATA[a:1:{s:7:"apppath";s:0:"";}]]></extra> </item_2> <item_3> <appid><![CDATA[3]]></appid> <type><![CDATA[ECSHOP]]></type> <name><![CDATA[ECSHOP]]></name> <url><![CDATA[http://localhost/edo/cp/]]></url> <ip><![CDATA[]]></ip> <charset><![CDATA[utf-8]]></charset> <synlogin><![CDATA[1]]></synlogin> <extra><![CDATA[]]></extra> </item_3> <UC_API><![CDATA[http://localhost/edo/ucenter]]></UC_API></root>', 1344342422, 0, 1, 1),
(8, 'updateapps', 1, 2, 2, '', '<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<root>\r\n <item id="1">\r\n <item id="appid"><![CDATA[1]]></item>\r\n <item id="type"><![CDATA[ECSHOP]]></item>\r\n <item id="name"><![CDATA[易多商城]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo/cp]]></item>\r\n <item id="ip"><![CDATA[127.0.0.1]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="2">\r\n <item id="appid"><![CDATA[2]]></item>\r\n <item id="type"><![CDATA[OTHER]]></item>\r\n <item id="name"><![CDATA[易多社区]]></item>\r\n <item id="url"><![CDATA[http://localhost/edo]]></item>\r\n <item id="ip"><![CDATA[]]></item>\r\n <item id="viewprourl"><![CDATA[]]></item>\r\n <item id="apifilename"><![CDATA[uc.php]]></item>\r\n <item id="charset"><![CDATA[]]></item>\r\n <item id="synlogin"><![CDATA[1]]></item>\r\n <item id="extra">\r\n <item id="apppath"><![CDATA[]]></item>\r\n </item>\r\n <item id="recvnote"><![CDATA[1]]></item>\r\n </item>\r\n <item id="UC_API"><![CDATA[http://localhost/edo/ucenter]]></item>\r\n</root>', 1344348456, 0, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `uc_pms`
--

CREATE TABLE IF NOT EXISTS `uc_pms` (
  `pmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `msgfrom` varchar(15) NOT NULL DEFAULT '',
  `msgfromid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `msgtoid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `folder` enum('inbox','outbox') NOT NULL DEFAULT 'inbox',
  `new` tinyint(1) NOT NULL DEFAULT '0',
  `subject` varchar(75) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `related` int(10) unsigned NOT NULL DEFAULT '0',
  `fromappid` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pmid`),
  KEY `msgtoid` (`msgtoid`,`folder`,`dateline`),
  KEY `msgfromid` (`msgfromid`,`folder`,`dateline`),
  KEY `related` (`related`),
  KEY `getnum` (`msgtoid`,`folder`,`delstatus`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `uc_pms`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_protectedmembers`
--

CREATE TABLE IF NOT EXISTS `uc_protectedmembers` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` char(15) NOT NULL DEFAULT '',
  `appid` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `admin` char(15) NOT NULL DEFAULT '0',
  UNIQUE KEY `username` (`username`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_protectedmembers`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_settings`
--

CREATE TABLE IF NOT EXISTS `uc_settings` (
  `k` varchar(32) NOT NULL DEFAULT '',
  `v` text NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_settings`
--

INSERT INTO `uc_settings` (`k`, `v`) VALUES
('accessemail', ''),
('censoremail', ''),
('censorusername', ''),
('dateformat', 'y-n-j'),
('doublee', '1'),
('nextnotetime', '0'),
('timeoffset', '28800'),
('pmlimit1day', '100'),
('pmfloodctrl', '15'),
('pmcenter', '1'),
('sendpmseccode', '1'),
('pmsendregdays', '0'),
('maildefault', 'username@21cn.com'),
('mailsend', '1'),
('mailserver', 'smtp.21cn.com'),
('mailport', '25'),
('mailauth', '1'),
('mailfrom', 'UCenter <username@21cn.com>'),
('mailauth_username', 'username@21cn.com'),
('mailauth_password', 'password'),
('maildelimiter', '0'),
('mailusername', '1'),
('mailsilent', '1'),
('version', '1.5.0');

-- --------------------------------------------------------

--
-- 表的结构 `uc_sqlcache`
--

CREATE TABLE IF NOT EXISTS `uc_sqlcache` (
  `sqlid` char(6) NOT NULL DEFAULT '',
  `data` char(100) NOT NULL,
  `expiry` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sqlid`),
  KEY `expiry` (`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_sqlcache`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_tags`
--

CREATE TABLE IF NOT EXISTS `uc_tags` (
  `tagname` char(20) NOT NULL,
  `appid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `data` mediumtext,
  `expiration` int(10) unsigned NOT NULL,
  KEY `tagname` (`tagname`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_tags`
--


-- --------------------------------------------------------

--
-- 表的结构 `uc_vars`
--

CREATE TABLE IF NOT EXISTS `uc_vars` (
  `name` char(32) NOT NULL DEFAULT '',
  `value` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `uc_vars`
--

