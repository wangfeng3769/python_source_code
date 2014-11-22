 -- phpMyAdmin SQL Dump
-- version 2.8.2.2-dev
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2012 年 09 月 22 日 16:23
-- 服务器版本: 5.5.14
-- PHP 版本: 5.2.17
-- 
-- 数据库: `edo1`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_ad`
-- 

CREATE TABLE `edo_ad` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `place` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:中部 1:头部 2:左侧 3:右侧 4:底部',
  `content` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_closable` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  `mtime` int(11) DEFAULT NULL,
  `display_order` smallint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_ad`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_addons`
-- 

CREATE TABLE `edo_addons` (
  `addonId` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `pluginName` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `info` tinytext,
  `version` varchar(50) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `lastupdate` varchar(255) DEFAULT '',
  `site` varchar(255) DEFAULT NULL,
  `tsVersion` varchar(11) DEFAULT '2.5',
  PRIMARY KEY (`addonId`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_admin_log`
-- 

CREATE TABLE `edo_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(11) NOT NULL COMMENT '操作人UID',
  `type` tinyint(4) NOT NULL,
  `data` text NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_app`
-- 

CREATE TABLE `edo_app` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) NOT NULL,
  `app_alias` varchar(255) NOT NULL,
  `description` text,
  `version` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:关闭 1:默认 2:可选',
  `category` varchar(255) DEFAULT NULL,
  `release_date` varchar(255) DEFAULT NULL,
  `last_update_date` varchar(255) DEFAULT NULL,
  `host_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：本地应用 1：远程应用',
  `app_entry` varchar(255) DEFAULT NULL,
  `icon_url` varchar(255) DEFAULT NULL,
  `large_icon_url` varchar(255) DEFAULT NULL,
  `admin_entry` varchar(255) DEFAULT NULL,
  `statistics_entry` varchar(255) DEFAULT NULL,
  `homepage_url` varchar(255) DEFAULT NULL,
  `sidebar_title` varchar(255) DEFAULT NULL,
  `sidebar_entry` varchar(255) DEFAULT NULL,
  `sidebar_icon_url` varchar(255) DEFAULT NULL,
  `sidebar_support_submenu` tinyint(1) NOT NULL DEFAULT '0',
  `sidebar_is_submenu_active` tinyint(1) NOT NULL DEFAULT '0',
  `author_name` varchar(255) DEFAULT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `author_homepage_url` varchar(255) DEFAULT NULL,
  `contributor_name` text,
  `display_order` smallint(5) NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_app`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_area`
-- 

CREATE TABLE `edo_area` (
  `area_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`area_id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=3235 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3235 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_attach`
-- 

CREATE TABLE `edo_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attach_type` varchar(50) NOT NULL DEFAULT 'attach',
  `userId` int(11) unsigned DEFAULT NULL,
  `uploadTime` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL,
  `private` tinyint(1) DEFAULT '0',
  `isDel` tinyint(1) DEFAULT '0',
  `savepath` varchar(255) DEFAULT NULL,
  `savename` varchar(255) DEFAULT NULL,
  `savedomain` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_attach`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_car_follow`
-- 

CREATE TABLE `edo_car_follow` (
  `user_id` bigint(20) NOT NULL,
  `car_id` bigint(20) NOT NULL,
  PRIMARY KEY (`user_id`,`car_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- 导出表中的数据 `edo_car_follow`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_category`
-- 

CREATE TABLE `edo_category` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cleft` int(11) NOT NULL,
  `cright` int(11) NOT NULL,
  `corder` int(5) NOT NULL DEFAULT '0',
  `namespace` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_category`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_comment`
-- 

CREATE TABLE `edo_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(15) DEFAULT NULL,
  `appid` int(11) DEFAULT NULL,
  `appuid` int(11) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `comment` text,
  `cTime` int(12) DEFAULT NULL,
  `toId` int(11) NOT NULL DEFAULT '0',
  `status` int(1) DEFAULT '0',
  `quietly` tinyint(1) NOT NULL DEFAULT '0',
  `to_uid` int(11) NOT NULL DEFAULT '0',
  `data` text,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `appid` (`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_comment`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_account_log`
-- 

CREATE TABLE `edo_cp_account_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `user_money` decimal(10,2) NOT NULL,
  `frozen_money` decimal(10,2) NOT NULL,
  `rank_points` mediumint(9) NOT NULL,
  `pay_points` mediumint(9) NOT NULL,
  `change_time` int(10) unsigned NOT NULL,
  `change_desc` varchar(255) NOT NULL,
  `change_type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_account_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_ad`
-- 

CREATE TABLE `edo_cp_ad` (
  `ad_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `media_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ad_name` varchar(60) NOT NULL DEFAULT '',
  `ad_link` varchar(255) NOT NULL DEFAULT '',
  `ad_code` text NOT NULL,
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) NOT NULL DEFAULT '0',
  `link_man` varchar(60) NOT NULL DEFAULT '',
  `link_email` varchar(60) NOT NULL DEFAULT '',
  `link_phone` varchar(60) NOT NULL DEFAULT '',
  `click_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ad_id`),
  KEY `position_id` (`position_id`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_ad`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_ad_custom`
-- 

CREATE TABLE `edo_cp_ad_custom` (
  `ad_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ad_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ad_name` varchar(60) DEFAULT NULL,
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `content` mediumtext,
  `url` varchar(255) DEFAULT NULL,
  `ad_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_ad_custom`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_ad_position`
-- 

CREATE TABLE `edo_cp_ad_position` (
  `position_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `position_name` varchar(60) NOT NULL DEFAULT '',
  `ad_width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ad_height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `position_desc` varchar(255) NOT NULL DEFAULT '',
  `position_style` text NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_ad_position`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_admin_action`
-- 

CREATE TABLE `edo_cp_admin_action` (
  `action_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `action_code` varchar(20) NOT NULL DEFAULT '',
  `relevance` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`action_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_admin_log`
-- 

CREATE TABLE `edo_cp_admin_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `log_info` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_id`),
  KEY `log_time` (`log_time`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_admin_message`
-- 

CREATE TABLE `edo_cp_admin_message` (
  `message_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `receiver_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sent_time` int(11) unsigned NOT NULL DEFAULT '0',
  `read_time` int(11) unsigned NOT NULL DEFAULT '0',
  `readed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`,`receiver_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_admin_message`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_admin_user`
-- 

CREATE TABLE `edo_cp_admin_user` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `action_list` text NOT NULL,
  `nav_list` text NOT NULL,
  `lang_type` varchar(50) NOT NULL DEFAULT '',
  `agency_id` smallint(5) unsigned NOT NULL,
  `suppliers_id` smallint(5) unsigned DEFAULT '0',
  `todolist` longtext,
  `role_id` smallint(5) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_adsense`
-- 

CREATE TABLE `edo_cp_adsense` (
  `from_ad` smallint(5) NOT NULL DEFAULT '0',
  `referer` varchar(255) NOT NULL DEFAULT '',
  `clicks` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `from_ad` (`from_ad`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_adsense`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_affiliate_log`
-- 

CREATE TABLE `edo_cp_affiliate_log` (
  `log_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) NOT NULL,
  `time` int(10) NOT NULL,
  `user_id` mediumint(8) NOT NULL,
  `user_name` varchar(60) DEFAULT NULL,
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `point` int(10) NOT NULL DEFAULT '0',
  `separate_type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_affiliate_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_agency`
-- 

CREATE TABLE `edo_cp_agency` (
  `agency_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `agency_name` varchar(255) NOT NULL,
  `agency_desc` text NOT NULL,
  PRIMARY KEY (`agency_id`),
  KEY `agency_name` (`agency_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_agency`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_approve_log`
-- 

CREATE TABLE `edo_cp_approve_log` (
  `id` int(13) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `id_approve` int(13) NOT NULL COMMENT '关联审核状态表id',
  `result` int(3) NOT NULL COMMENT '审核结果',
  `reason` varchar(255) NOT NULL COMMENT '结果原因',
  `approve_time` int(15) NOT NULL COMMENT '审核时间',
  `admin_id` int(13) NOT NULL COMMENT '管理员id',
  `admin_name` varchar(255) NOT NULL COMMENT '管理员名',
  `sort_order` int(13) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_area_region`
-- 

CREATE TABLE `edo_cp_area_region` (
  `shipping_area_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `region_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shipping_area_id`,`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_area_region`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_article`
-- 

CREATE TABLE `edo_cp_article` (
  `article_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` smallint(5) NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `author` varchar(30) NOT NULL DEFAULT '',
  `author_email` varchar(60) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `article_type` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `is_open` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `file_url` varchar(255) NOT NULL DEFAULT '',
  `open_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_article_cat`
-- 

CREATE TABLE `edo_cp_article_cat` (
  `cat_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL DEFAULT '',
  `cat_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `cat_desc` varchar(255) NOT NULL DEFAULT '',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `show_in_nav` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`),
  KEY `cat_type` (`cat_type`),
  KEY `sort_order` (`sort_order`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_attribute`
-- 

CREATE TABLE `edo_cp_attribute` (
  `attr_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attr_name` varchar(60) NOT NULL DEFAULT '',
  `attr_input_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `attr_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `attr_values` text NOT NULL,
  `attr_index` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_linked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `attr_group` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_attribute`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_auction_log`
-- 

CREATE TABLE `edo_cp_auction_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `act_id` mediumint(8) unsigned NOT NULL,
  `bid_user` mediumint(8) unsigned NOT NULL,
  `bid_price` decimal(10,2) unsigned NOT NULL,
  `bid_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `act_id` (`act_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_auction_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_auto_manage`
-- 

CREATE TABLE `edo_cp_auto_manage` (
  `item_id` mediumint(8) NOT NULL,
  `type` varchar(10) NOT NULL,
  `starttime` int(10) NOT NULL,
  `endtime` int(10) NOT NULL,
  PRIMARY KEY (`item_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_auto_manage`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_back_goods`
-- 

CREATE TABLE `edo_cp_back_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `back_id` mediumint(8) unsigned DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_sn` varchar(60) DEFAULT NULL,
  `goods_name` varchar(120) DEFAULT NULL,
  `brand_name` varchar(60) DEFAULT NULL,
  `goods_sn` varchar(60) DEFAULT NULL,
  `is_real` tinyint(1) unsigned DEFAULT '0',
  `send_number` smallint(5) unsigned DEFAULT '0',
  `goods_attr` text,
  PRIMARY KEY (`rec_id`),
  KEY `back_id` (`back_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_back_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_back_order`
-- 

CREATE TABLE `edo_cp_back_order` (
  `back_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_sn` varchar(20) NOT NULL,
  `order_sn` varchar(20) NOT NULL,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(50) DEFAULT NULL,
  `add_time` int(10) unsigned DEFAULT '0',
  `shipping_id` tinyint(3) unsigned DEFAULT '0',
  `shipping_name` varchar(120) DEFAULT NULL,
  `user_id` mediumint(8) unsigned DEFAULT '0',
  `action_user` varchar(30) DEFAULT NULL,
  `consignee` varchar(60) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `country` smallint(5) unsigned DEFAULT '0',
  `province` smallint(5) unsigned DEFAULT '0',
  `city` smallint(5) unsigned DEFAULT '0',
  `district` smallint(5) unsigned DEFAULT '0',
  `sign_building` varchar(120) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `zipcode` varchar(60) DEFAULT NULL,
  `tel` varchar(60) DEFAULT NULL,
  `mobile` varchar(60) DEFAULT NULL,
  `best_time` varchar(120) DEFAULT NULL,
  `postscript` varchar(255) DEFAULT NULL,
  `how_oos` varchar(120) DEFAULT NULL,
  `insure_fee` decimal(10,2) unsigned DEFAULT '0.00',
  `shipping_fee` decimal(10,2) unsigned DEFAULT '0.00',
  `update_time` int(10) unsigned DEFAULT '0',
  `suppliers_id` smallint(5) DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `return_time` int(10) unsigned DEFAULT '0',
  `agency_id` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`back_id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_back_order`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_bonus_type`
-- 

CREATE TABLE `edo_cp_bonus_type` (
  `type_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(60) NOT NULL DEFAULT '',
  `type_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `send_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `max_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `send_start_date` int(11) NOT NULL DEFAULT '0',
  `send_end_date` int(11) NOT NULL DEFAULT '0',
  `use_start_date` int(11) NOT NULL DEFAULT '0',
  `use_end_date` int(11) NOT NULL DEFAULT '0',
  `min_goods_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `icon` varchar(255) DEFAULT NULL COMMENT '红包图标',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_bonus_type`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_booking_goods`
-- 

CREATE TABLE `edo_cp_booking_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `email` varchar(60) NOT NULL DEFAULT '',
  `link_man` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_desc` varchar(255) NOT NULL DEFAULT '',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `booking_time` int(10) unsigned NOT NULL DEFAULT '0',
  `is_dispose` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dispose_user` varchar(30) NOT NULL DEFAULT '',
  `dispose_time` int(10) unsigned NOT NULL DEFAULT '0',
  `dispose_note` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`rec_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_booking_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_brand`
-- 

CREATE TABLE `edo_cp_brand` (
  `brand_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(60) NOT NULL DEFAULT '',
  `brand_logo` varchar(80) NOT NULL DEFAULT '',
  `brand_desc` text NOT NULL,
  `site_url` varchar(255) NOT NULL DEFAULT '',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`brand_id`),
  KEY `is_show` (`is_show`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_brand`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car`
-- 

CREATE TABLE `edo_cp_car` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL COMMENT '昵称',
  `number` varchar(300) NOT NULL COMMENT '车牌号',
  `model` varchar(300) NOT NULL COMMENT '型号',
  `gearbox` varchar(300) NOT NULL COMMENT '变速箱类型',
  `color` varchar(100) NOT NULL COMMENT '颜色',
  `group` int(13) NOT NULL COMMENT '所属群组id',
  `station` int(13) NOT NULL COMMENT '站点id',
  `equipment` varchar(4) NOT NULL COMMENT '设备信息',
  `sort_order` int(13) NOT NULL,
  `status` int(1) DEFAULT '1' COMMENT '1:锁门;2:开门;3:行驶中;4:禁用',
  `icon` varchar(128) NULL COMMENT '车辆图标',
  `mile_factor` int(11) NOT NULL COMMENT '里程因子',
  `brand` VARCHAR( 32 ) NOT NULL COMMENT  '车辆品牌',
  `delete_stat` int(1) NOT NULL DEFAULT '0' COMMENT '下线标志(0:正常,1:下线)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AUTO_INCREMENT=10001 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car_cmd_record`
-- 

CREATE TABLE `edo_cp_car_cmd_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` bigint(20) NOT NULL,
  `send_time` datetime NOT NULL,
  `wait_port` int(6) NOT NULL,
  `packet_number` smallint(6) NOT NULL,
  `addon` varchar(512) COLLATE utf8_bin NOT NULL,
  `state` smallint(6) NOT NULL COMMENT '0：没有下发;1:已下发;2:接收成功;3:接收失败',
  `need_resend` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

-- 
-- 导出表中的数据 `edo_cp_car_cmd_record`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car_group`
-- 

CREATE TABLE `edo_cp_car_group` (
  `car_group_id` int(13) NOT NULL AUTO_INCREMENT COMMENT '车群组id',
  `car_group_name` varchar(255) NOT NULL COMMENT '车群组名称',
  `car_group_desc` varchar(255) NOT NULL COMMENT '车群组描述',
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`car_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car_order`
-- 

CREATE TABLE `edo_cp_car_order` (
  `order_id` int(13) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_no` varchar(40) NOT NULL COMMENT '订单号',
  `order_stat` int(1) NOT NULL COMMENT '订单状态：0:未支付、1:已预定、2:执行中、3:已完成、4:已取消、5:支付超时、6:还车超时、8:已锁门、9:已结算',
  `is_shift_stat` int(1) NOT NULL COMMENT '转移状态',
  `car_id` int(11) NOT NULL COMMENT '订单车辆id',
  `user_id` int(11) NOT NULL COMMENT '会员id',
  `add_time` int(11) NOT NULL COMMENT '下单时间',
  `order_start_time` int(11) NOT NULL COMMENT '订车开始时间',
  `order_end_time` int(11) NOT NULL COMMENT '订车结束时间',
  `real_start_time` int(11) NOT NULL COMMENT '实际开始时间',
  `real_end_time` int(11) NOT NULL COMMENT '实际结束时间',
  `start_mileage` varchar(32) NOT NULL COMMENT '开始里程',
  `end_mileage` varchar(32) NOT NULL COMMENT '结束里程',
  `mile` int NOT NULL COMMENT '实际行驶里程，单位：米',
  `order_org_cost` int(11) NOT NULL COMMENT '车辆使用费原价',
  `order_real_cost` int(11) NOT NULL COMMENT '车辆使用费折后价',
  `extra_cost` int(11) NOT NULL COMMENT '附加费',
  `medal_id` bigint(20) DEFAULT NULL COMMENT '使用的奖章ID',
  `old_order_id` int(11) NOT NULL COMMENT '修改订单时使用',
  `violate_org_rent` int(11) NOT NULL COMMENT '原始违章押金金额',
  `violate_real_rent` int(11) NOT NULL COMMENT '折后违章押金金额',
  `modify_times` int(11) NOT NULL COMMENT '订单修改次数',
  `payment_trans_num` varchar(64) NOT NULL COMMENT '订单使用费支付时产生的transaction id',
  `modify_fee` int(11) NOT NULL COMMENT '修改订单的修订费',
  `cancel_fee` int(11) NOT NULL COMMENT '取消订单费',
  `use_car_password` varchar(8) NOT NULL,
  `modify_stat` smallint NOT NULL,
  PRIMARY KEY (`order_id`)
);

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car_price_base`
-- 

CREATE TABLE `edo_cp_car_price_base` (
  `id` int(13) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `hour_price` float NOT NULL COMMENT '小时租金',
  `hour_km` float NOT NULL COMMENT '小时租金含公里数',
  `top_price` float NOT NULL COMMENT '24小时封顶价',
  `top_km` float NOT NULL COMMENT '24小时封顶价含公里数',
  `price_km` float NOT NULL COMMENT '每公里价格',
  `price_time_out` float NOT NULL COMMENT '超时价格',
  `car_id` int(13) NOT NULL COMMENT '汽车id',
  `type` int(3) NOT NULL COMMENT '0:工作日,1节假日',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

-- 
-- 导出表中的数据 `edo_cp_car_price_base`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car_price_detail`
-- 

CREATE TABLE `edo_cp_car_price_detail` (
  `id` int(13) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `car_id` int(13) NOT NULL COMMENT '汽车id',
  `time_from` int(2) NOT NULL COMMENT '开始时间',
  `time_end` int(2) NOT NULL COMMENT '结束时间',
  `price` int(13) unsigned NOT NULL COMMENT '价格',
  `contain_km` int(13) unsigned NOT NULL COMMENT '含公里数',
  `type` int(1) unsigned NOT NULL COMMENT '0:工作日,1:节假日',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=latin1 AUTO_INCREMENT=124 ;

-- 
-- 导出表中的数据 `edo_cp_car_price_detail`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car_price_top`
-- 

CREATE TABLE `edo_cp_car_price_top` (
  `id` int(13) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `car_id` int(10) unsigned NOT NULL COMMENT '汽车id',
  `date_from` date NOT NULL COMMENT '生效日期',
  `date_end` date NOT NULL COMMENT '失效日期',
  `time_from` int(3) unsigned NOT NULL COMMENT '起始时间',
  `time_end` int(3) unsigned NOT NULL COMMENT '终止时间',
  `period_top` int(13) unsigned NOT NULL COMMENT '时段封顶价',
  `contain_km` int(13) unsigned NOT NULL COMMENT '含公里数',
  `type` int(1) unsigned NOT NULL COMMENT '0:工作日,1:节假日',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=181 DEFAULT CHARSET=latin1 AUTO_INCREMENT=181 ;

-- 
-- 导出表中的数据 `edo_cp_car_price_top`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car_tracer`
-- 

CREATE TABLE `edo_cp_car_tracer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `car_id` bigint(20) NOT NULL,
  `sys_state` smallint(6) NOT NULL,
  `soft_version` smallint(6) NOT NULL,
  `packet_number` smallint(6) NOT NULL,
  `ctrl_state` smallint(6) NOT NULL,
  `oil` varchar(32) COLLATE utf8_bin NOT NULL,
  `distance_count` int(11) NOT NULL,
  `latitude` varchar(32) COLLATE utf8_bin NOT NULL,
  `longitude` varchar(32) COLLATE utf8_bin NOT NULL,
  `addon` varchar(256) COLLATE utf8_bin NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_car_tracer`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_car_violate`
-- 

CREATE TABLE `edo_cp_car_violate` (
  `id` int(13) NOT NULL AUTO_INCREMENT COMMENT'自增id',
  `car_id` int(13) NOT NULL COMMENT '汽车id',
  `violate_time` int(13) NOT NULL COMMENT '违章时间',
  `violate_location` varchar(127) NOT NULL COMMENT '违章地点',
  `process_stat` int(1) NOT NULL COMMENT '0=新录入,1=正在代办,2=已自行处理,3=已代办处理,4=已结算',
  `violate_type` varchar(255) NOT NULL COMMENT '违章类型',
  `violate_cost` int(13) NOT NULL COMMENT '违章金额',
  `violate_point` int(13) NOT NULL COMMENT '扣分',
  `agency_cost` int(13) NOT NULL COMMENT '代办费用',
  `user_id` int(13) NOT NULL COMMENT '违章会员',
  `is_agency` int(1) NOT NULL COMMENT '是否代办',
  `order_id` int(13) NOT NULL COMMENT '订单ID',
  `record_time` int(11) NOT NULL COMMENT '录入记录的时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_car_violate`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_card`
-- 

CREATE TABLE `edo_cp_card` (
  `card_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `card_name` varchar(120) NOT NULL DEFAULT '',
  `card_img` varchar(255) NOT NULL DEFAULT '',
  `card_fee` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `free_money` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `card_desc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`card_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=10001 ;

-- 
-- 导出表中的数据 `edo_cp_card`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_cart`
-- 

CREATE TABLE `edo_cp_cart` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `session_id` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_attr` text NOT NULL,
  `is_real` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `rec_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_gift` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `can_handsel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `goods_attr_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`rec_id`),
  KEY `session_id` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_cart`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_cat_recommend`
-- 

CREATE TABLE `edo_cp_cat_recommend` (
  `cat_id` smallint(5) NOT NULL,
  `recommend_type` tinyint(1) NOT NULL,
  PRIMARY KEY (`cat_id`,`recommend_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_cat_recommend`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_category`
-- 

CREATE TABLE `edo_cp_category` (
  `cat_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(90) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `cat_desc` varchar(255) NOT NULL DEFAULT '',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '50',
  `template_file` varchar(50) NOT NULL DEFAULT '',
  `measure_unit` varchar(15) NOT NULL DEFAULT '',
  `show_in_nav` tinyint(1) NOT NULL DEFAULT '0',
  `style` varchar(150) NOT NULL,
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `grade` tinyint(4) NOT NULL DEFAULT '0',
  `filter_attr` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_collect_goods`
-- 

CREATE TABLE `edo_cp_collect_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0',
  `is_attention` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rec_id`),
  KEY `user_id` (`user_id`),
  KEY `goods_id` (`goods_id`),
  KEY `is_attention` (`is_attention`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_collect_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_comment`
-- 

CREATE TABLE `edo_cp_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `id_value` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `email` varchar(60) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `comment_rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `parent_id` (`parent_id`),
  KEY `id_value` (`id_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_comment`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_crons`
-- 

CREATE TABLE `edo_cp_crons` (
  `cron_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `cron_code` varchar(20) NOT NULL,
  `cron_name` varchar(120) NOT NULL,
  `cron_desc` text,
  `cron_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cron_config` text NOT NULL,
  `thistime` int(10) NOT NULL DEFAULT '0',
  `nextime` int(10) NOT NULL,
  `day` tinyint(2) NOT NULL,
  `week` varchar(1) NOT NULL,
  `hour` varchar(2) NOT NULL,
  `minute` varchar(255) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  `run_once` tinyint(1) NOT NULL DEFAULT '0',
  `allow_ip` varchar(100) NOT NULL DEFAULT '',
  `alow_files` varchar(255) NOT NULL,
  PRIMARY KEY (`cron_id`),
  KEY `nextime` (`nextime`),
  KEY `enable` (`enable`),
  KEY `cron_code` (`cron_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_crons`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_delivery_goods`
-- 

CREATE TABLE `edo_cp_delivery_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned DEFAULT '0',
  `product_sn` varchar(60) DEFAULT NULL,
  `goods_name` varchar(120) DEFAULT NULL,
  `brand_name` varchar(60) DEFAULT NULL,
  `goods_sn` varchar(60) DEFAULT NULL,
  `is_real` tinyint(1) unsigned DEFAULT '0',
  `extension_code` varchar(30) DEFAULT NULL,
  `parent_id` mediumint(8) unsigned DEFAULT '0',
  `send_number` smallint(5) unsigned DEFAULT '0',
  `goods_attr` text,
  PRIMARY KEY (`rec_id`),
  KEY `delivery_id` (`delivery_id`,`goods_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_delivery_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_delivery_order`
-- 

CREATE TABLE `edo_cp_delivery_order` (
  `delivery_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_sn` varchar(20) NOT NULL,
  `order_sn` varchar(20) NOT NULL,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(50) DEFAULT NULL,
  `add_time` int(10) unsigned DEFAULT '0',
  `shipping_id` tinyint(3) unsigned DEFAULT '0',
  `shipping_name` varchar(120) DEFAULT NULL,
  `user_id` mediumint(8) unsigned DEFAULT '0',
  `action_user` varchar(30) DEFAULT NULL,
  `consignee` varchar(60) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `country` smallint(5) unsigned DEFAULT '0',
  `province` smallint(5) unsigned DEFAULT '0',
  `city` smallint(5) unsigned DEFAULT '0',
  `district` smallint(5) unsigned DEFAULT '0',
  `sign_building` varchar(120) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `zipcode` varchar(60) DEFAULT NULL,
  `tel` varchar(60) DEFAULT NULL,
  `mobile` varchar(60) DEFAULT NULL,
  `best_time` varchar(120) DEFAULT NULL,
  `postscript` varchar(255) DEFAULT NULL,
  `how_oos` varchar(120) DEFAULT NULL,
  `insure_fee` decimal(10,2) unsigned DEFAULT '0.00',
  `shipping_fee` decimal(10,2) unsigned DEFAULT '0.00',
  `update_time` int(10) unsigned DEFAULT '0',
  `suppliers_id` smallint(5) DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `agency_id` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`delivery_id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_delivery_order`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_deposit`
-- 

CREATE TABLE `edo_cp_deposit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `station_id` int(11) NOT NULL COMMENT '站点ID',
  `group_id` int(11) NOT NULL COMMENT '会员组ID',
  `user_type_id` int(1) NOT NULL COMMENT '会员类别ID',
  `deposit` varchar(11) NOT NULL COMMENT '使用押金 调整百分比（%）',
  `type` int(1) NOT NULL COMMENT '1:使用押金;2:违章押金',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='押金策略表' AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `edo_cp_deposit`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_deposit_violate`
-- 

CREATE TABLE `edo_cp_deposit_violate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_id` int(11) unsigned NOT NULL COMMENT '城市ID',
  `group_id` int(11) NOT NULL COMMENT '会员组ID',
  `user_type_id` int(1) NOT NULL COMMENT '会员类别ID',
  `adjust_money` varchar(11) NOT NULL COMMENT '使用押金 金额 增加,或减少',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='押金策略表' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_deposit_violate_city`
-- 

CREATE TABLE `edo_cp_deposit_violate_city` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `city_id` int(11) unsigned NOT NULL COMMENT '城市id',
  `deposit` varchar(40) NOT NULL COMMENT '默认押金',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='默认城市违章押金表' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_deposit_violate_group`
-- 

CREATE TABLE `edo_cp_deposit_violate_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `group_id` int(13) NOT NULL COMMENT '群组id',
  `discount` varchar(40) NOT NULL COMMENT '折扣',
  `adjust_money` varchar(40) NOT NULL COMMENT '调整金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='默认群组违章押金表' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_deposit_violate_user_type`
-- 

CREATE TABLE `edo_cp_deposit_violate_user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_type_id` int(13) NOT NULL COMMENT '会员类别id',
  `discount` varchar(40) NOT NULL COMMENT '折扣',
  `adjust_money` varchar(40) NOT NULL COMMENT '调整金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='默认会员违章押金表' AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_email_list`
-- 

CREATE TABLE `edo_cp_email_list` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL,
  `stat` tinyint(1) NOT NULL DEFAULT '0',
  `hash` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_email_sendlist`
-- 

CREATE TABLE `edo_cp_email_sendlist` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `template_id` mediumint(8) NOT NULL,
  `email_content` text NOT NULL,
  `error` tinyint(1) NOT NULL DEFAULT '0',
  `pri` tinyint(10) NOT NULL,
  `last_send` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_email_sendlist`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_equipment`
-- 

CREATE TABLE `edo_cp_equipment` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `version` varchar(50) NOT NULL COMMENT '版本',
  `serial` varchar(50) NOT NULL COMMENT '序列号',
  `sim` varchar(50) NOT NULL COMMENT 'SIM卡号',
  `model` varchar(30) NOT NULL COMMENT '型号',
  `oil_card` varchar(255) NOT NULL COMMENT '油卡卡号',
  `status` int(1) NOT NULL COMMENT '状态',
  `sort_order` int(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_error_log`
-- 

CREATE TABLE `edo_cp_error_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `info` varchar(255) NOT NULL,
  `file` varchar(100) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_error_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_exchange_goods`
-- 

CREATE TABLE `edo_cp_exchange_goods` (
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `exchange_integral` int(10) unsigned NOT NULL DEFAULT '0',
  `is_exchange` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_exchange_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_favourable_activity`
-- 

CREATE TABLE `edo_cp_favourable_activity` (
  `act_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `act_name` varchar(255) NOT NULL,
  `start_time` int(10) unsigned NOT NULL,
  `end_time` int(10) unsigned NOT NULL,
  `user_rank` varchar(255) NOT NULL,
  `act_range` tinyint(3) unsigned NOT NULL,
  `act_range_ext` varchar(255) NOT NULL,
  `min_amount` decimal(10,2) unsigned NOT NULL,
  `max_amount` decimal(10,2) unsigned NOT NULL,
  `act_type` tinyint(3) unsigned NOT NULL,
  `act_type_ext` decimal(10,2) unsigned NOT NULL,
  `gift` text NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '50',
  PRIMARY KEY (`act_id`),
  KEY `act_name` (`act_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_favourable_activity`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_feedback`
-- 

CREATE TABLE `edo_cp_feedback` (
  `msg_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `user_email` varchar(60) NOT NULL DEFAULT '',
  `msg_title` varchar(200) NOT NULL DEFAULT '',
  `msg_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_content` text NOT NULL,
  `msg_time` int(10) unsigned NOT NULL DEFAULT '0',
  `message_img` varchar(255) NOT NULL DEFAULT '0',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0',
  `msg_area` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`msg_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_feedback`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_friend_link`
-- 

CREATE TABLE `edo_cp_friend_link` (
  `link_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_logo` varchar(255) NOT NULL DEFAULT '',
  `show_order` tinyint(3) unsigned NOT NULL DEFAULT '50',
  PRIMARY KEY (`link_id`),
  KEY `show_order` (`show_order`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_goods`
-- 

CREATE TABLE `edo_cp_goods` (
  `goods_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_name_style` varchar(60) NOT NULL DEFAULT '+',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `provider_name` varchar(100) NOT NULL DEFAULT '',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_weight` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `shop_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `promote_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `promote_start_date` int(11) unsigned NOT NULL DEFAULT '0',
  `promote_end_date` int(11) unsigned NOT NULL DEFAULT '0',
  `warn_number` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `goods_brief` varchar(255) NOT NULL DEFAULT '',
  `goods_desc` text NOT NULL,
  `goods_thumb` varchar(255) NOT NULL DEFAULT '',
  `goods_img` varchar(255) NOT NULL DEFAULT '',
  `original_img` varchar(255) NOT NULL DEFAULT '',
  `is_real` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `is_on_sale` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_alone_sale` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `integral` int(10) unsigned NOT NULL DEFAULT '0',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` smallint(4) unsigned NOT NULL DEFAULT '100',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_best` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_new` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_promote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `bonus_type_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `last_update` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `seller_note` varchar(255) NOT NULL DEFAULT '',
  `give_integral` int(11) NOT NULL DEFAULT '-1',
  `rank_integral` int(11) NOT NULL DEFAULT '-1',
  `suppliers_id` smallint(5) unsigned DEFAULT NULL,
  `is_check` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`goods_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `cat_id` (`cat_id`),
  KEY `last_update` (`last_update`),
  KEY `brand_id` (`brand_id`),
  KEY `goods_weight` (`goods_weight`),
  KEY `promote_end_date` (`promote_end_date`),
  KEY `promote_start_date` (`promote_start_date`),
  KEY `goods_number` (`goods_number`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_goods_activity`
-- 

CREATE TABLE `edo_cp_goods_activity` (
  `act_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `act_name` varchar(255) NOT NULL,
  `act_desc` text NOT NULL,
  `act_type` tinyint(3) unsigned NOT NULL,
  `goods_id` mediumint(8) unsigned NOT NULL,
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_name` varchar(255) NOT NULL,
  `start_time` int(10) unsigned NOT NULL,
  `end_time` int(10) unsigned NOT NULL,
  `is_finished` tinyint(3) unsigned NOT NULL,
  `ext_info` text NOT NULL,
  PRIMARY KEY (`act_id`),
  KEY `act_name` (`act_name`,`act_type`,`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_goods_activity`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_goods_article`
-- 

CREATE TABLE `edo_cp_goods_article` (
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `article_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_id`,`article_id`,`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_goods_article`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_goods_attr`
-- 

CREATE TABLE `edo_cp_goods_attr` (
  `goods_attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `attr_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attr_value` text NOT NULL,
  `attr_price` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`goods_attr_id`),
  KEY `goods_id` (`goods_id`),
  KEY `attr_id` (`attr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_goods_attr`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_goods_cat`
-- 

CREATE TABLE `edo_cp_goods_cat` (
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cat_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_id`,`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_goods_cat`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_goods_gallery`
-- 

CREATE TABLE `edo_cp_goods_gallery` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `img_url` varchar(255) NOT NULL DEFAULT '',
  `img_desc` varchar(255) NOT NULL DEFAULT '',
  `thumb_url` varchar(255) NOT NULL DEFAULT '',
  `img_original` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`img_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_goods_gallery`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_goods_type`
-- 

CREATE TABLE `edo_cp_goods_type` (
  `cat_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(60) NOT NULL DEFAULT '',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `attr_group` varchar(255) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_goods_type`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_group_goods`
-- 

CREATE TABLE `edo_cp_group_goods` (
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`parent_id`,`goods_id`,`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_group_goods`
-- 


-- --------------------------------------------------------

--
-- 表的结构 `edo_cp_invoice_log`
--

CREATE TABLE `edo_cp_invoice_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
  `order_id` int(11) unsigned NOT NULL COMMENT '订单id',
  `admin_id` int(11) unsigned NOT NULL COMMENT '管理员id',
  `invoice_address` varchar(255) NOT NULL COMMENT '发票寄送地址',
  `invoice_title` varchar(255) NOT NULL COMMENT '发票抬头',
  `is_processed` int(1) NOT NULL COMMENT '是否已经处理,开票',
  `processed_time` int(11) DEFAULT NULL COMMENT '开票日期',
  `zip` int(11) unsigned DEFAULT NULL COMMENT '邮编',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='发票记录表' AUTO_INCREMENT=16 ;

-- 
-- 导出表中的数据 `edo_cp_invoice_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_keywords`
-- 

CREATE TABLE `edo_cp_keywords` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `searchengine` varchar(20) NOT NULL DEFAULT '',
  `keyword` varchar(90) NOT NULL DEFAULT '',
  `count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`,`searchengine`,`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_keywords`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_link_goods`
-- 

CREATE TABLE `edo_cp_link_goods` (
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `link_goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_double` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_id`,`link_goods_id`,`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_link_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_mail_templates`
-- 

CREATE TABLE `edo_cp_mail_templates` (
  `template_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `template_code` varchar(30) NOT NULL DEFAULT '',
  `is_html` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `template_subject` varchar(200) NOT NULL DEFAULT '',
  `template_content` text NOT NULL,
  `last_modify` int(10) unsigned NOT NULL DEFAULT '0',
  `last_send` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `template_code` (`template_code`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_member_price`
-- 

CREATE TABLE `edo_cp_member_price` (
  `price_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_rank` tinyint(3) NOT NULL DEFAULT '0',
  `user_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`price_id`),
  KEY `goods_id` (`goods_id`,`user_rank`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_member_price`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_mo_log`
-- 

CREATE TABLE `edo_cp_mo_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sender` varchar(32) COLLATE utf8_bin NOT NULL,
  `msg` varchar(512) COLLATE utf8_bin NOT NULL,
  `time` datetime NOT NULL,
  `service_code` varchar(16) COLLATE utf8_bin NOT NULL,
  `msg_id` varchar(256) COLLATE utf8_bin NOT NULL,
  `receiver` varchar(32) COLLATE utf8_bin NOT NULL,
  `esm_class` smallint(6) NOT NULL,
  `product_id` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `edo_cp_mo_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_mt_log`
-- 

CREATE TABLE `edo_cp_mt_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `receiver` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `time` datetime NOT NULL,
  `msg` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `msg_id` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` smallint(6) NOT NULL COMMENT '1:下发成功；2:下发失败；3:接收成功；4:接收失败',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_mt_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_nav`
-- 

CREATE TABLE `edo_cp_nav` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `ctype` varchar(10) DEFAULT NULL,
  `cid` smallint(5) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `ifshow` tinyint(1) NOT NULL,
  `vieworder` tinyint(1) NOT NULL,
  `opennew` tinyint(1) NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `ifshow` (`ifshow`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_nav`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_oil_card`
-- 

CREATE TABLE `edo_cp_oil_card` (
  `id` int(30) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `card_id` varchar(255) NOT NULL COMMENT '加油卡号',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `provider` varchar(255) NOT NULL COMMENT '供应商',
  `monitor_status` int(1) NOT NULL COMMENT '是 否监控',
  `using_status` int(1) NOT NULL COMMENT '使用状态',
  `sort_order` int(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=904 DEFAULT CHARSET=utf8 AUTO_INCREMENT=904 ;

-- 
-- 导出表中的数据 `edo_cp_oil_card`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_order_action`
-- 

CREATE TABLE `edo_cp_order_action` (
  `action_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `action_user` varchar(30) NOT NULL DEFAULT '',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `action_place` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `action_note` varchar(255) NOT NULL DEFAULT '',
  `log_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`action_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_order_action`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_order_goods`
-- 

CREATE TABLE `edo_cp_order_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_sn` varchar(60) NOT NULL DEFAULT '',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '1',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_attr` text NOT NULL,
  `send_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_real` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_gift` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_attr_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`rec_id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_order_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_order_info`
-- 

CREATE TABLE `edo_cp_order_info` (
  `order_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `consignee` varchar(60) NOT NULL DEFAULT '',
  `country` smallint(5) unsigned NOT NULL DEFAULT '0',
  `province` smallint(5) unsigned NOT NULL DEFAULT '0',
  `city` smallint(5) unsigned NOT NULL DEFAULT '0',
  `district` smallint(5) unsigned NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `best_time` varchar(120) NOT NULL DEFAULT '',
  `sign_building` varchar(120) NOT NULL DEFAULT '',
  `postscript` varchar(255) NOT NULL DEFAULT '',
  `shipping_id` tinyint(3) NOT NULL DEFAULT '0',
  `shipping_name` varchar(120) NOT NULL DEFAULT '',
  `pay_id` tinyint(3) NOT NULL DEFAULT '0',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `how_oos` varchar(120) NOT NULL DEFAULT '',
  `how_surplus` varchar(120) NOT NULL DEFAULT '',
  `pack_name` varchar(120) NOT NULL DEFAULT '',
  `card_name` varchar(120) NOT NULL DEFAULT '',
  `card_message` varchar(255) NOT NULL DEFAULT '',
  `inv_payee` varchar(120) NOT NULL DEFAULT '',
  `inv_content` varchar(120) NOT NULL DEFAULT '',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `insure_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pack_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `card_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `money_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `surplus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `integral` int(10) unsigned NOT NULL DEFAULT '0',
  `integral_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `from_ad` smallint(5) NOT NULL DEFAULT '0',
  `referer` varchar(255) NOT NULL DEFAULT '',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0',
  `shipping_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pack_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `card_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bonus_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(255) NOT NULL DEFAULT '',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `extension_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `to_buyer` varchar(255) NOT NULL DEFAULT '',
  `pay_note` varchar(255) NOT NULL DEFAULT '',
  `agency_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `inv_type` varchar(60) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_separate` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `order_status` (`order_status`),
  KEY `shipping_status` (`shipping_status`),
  KEY `pay_status` (`pay_status`),
  KEY `shipping_id` (`shipping_id`),
  KEY `pay_id` (`pay_id`),
  KEY `extension_code` (`extension_code`,`extension_id`),
  KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_order_info`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_pack`
-- 

CREATE TABLE `edo_cp_pack` (
  `pack_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `pack_name` varchar(120) NOT NULL DEFAULT '',
  `pack_img` varchar(255) NOT NULL DEFAULT '',
  `pack_fee` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `free_money` smallint(5) unsigned NOT NULL DEFAULT '0',
  `pack_desc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pack_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_pack`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_package_goods`
-- 

CREATE TABLE `edo_cp_package_goods` (
  `package_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '1',
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`package_id`,`goods_id`,`admin_id`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_package_goods`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_pay_log`
-- 

CREATE TABLE `edo_cp_pay_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_amount` decimal(10,2) unsigned NOT NULL,
  `order_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_paid` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_pay_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_payment`
-- 

CREATE TABLE `edo_cp_payment` (
  `pay_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `pay_code` varchar(20) NOT NULL DEFAULT '',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `pay_fee` varchar(10) NOT NULL DEFAULT '0',
  `pay_desc` text NOT NULL,
  `pay_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_config` text NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_cod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_online` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pay_id`),
  UNIQUE KEY `pay_code` (`pay_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_payment`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_plugins`
-- 

CREATE TABLE `edo_cp_plugins` (
  `code` varchar(30) NOT NULL DEFAULT '',
  `version` varchar(10) NOT NULL DEFAULT '',
  `library` varchar(255) NOT NULL DEFAULT '',
  `assign` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `install_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_plugins`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_products`
-- 

CREATE TABLE `edo_cp_products` (
  `product_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_attr` varchar(50) DEFAULT NULL,
  `product_sn` varchar(60) DEFAULT NULL,
  `product_number` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_products`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_reg_extend_info`
-- 

CREATE TABLE `edo_cp_reg_extend_info` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `reg_field_id` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_reg_extend_info`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_reg_fields`
-- 

CREATE TABLE `edo_cp_reg_fields` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `reg_field_name` varchar(60) NOT NULL,
  `dis_order` tinyint(3) unsigned NOT NULL DEFAULT '100',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_need` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_region`
-- 

CREATE TABLE `edo_cp_region` (
  `region_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `region_name` varchar(120) NOT NULL DEFAULT '',
  `region_type` tinyint(1) NOT NULL DEFAULT '2',
  `agency_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`region_id`),
  KEY `parent_id` (`parent_id`),
  KEY `region_type` (`region_type`),
  KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3409 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3409 ;



-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_role`
-- 

CREATE TABLE `edo_cp_role` (
  `role_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(60) NOT NULL DEFAULT '',
  `action_list` text NOT NULL,
  `role_describe` text,
  PRIMARY KEY (`role_id`),
  KEY `user_name` (`role_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_role`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_searchengine`
-- 

CREATE TABLE `edo_cp_searchengine` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `searchengine` varchar(20) NOT NULL DEFAULT '',
  `count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`,`searchengine`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_searchengine`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_sessions`
-- 

CREATE TABLE `edo_cp_sessions` (
  `sesskey` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `adminid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL,
  `user_rank` tinyint(3) NOT NULL,
  `discount` decimal(3,2) NOT NULL,
  `email` varchar(60) NOT NULL,
  `data` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`sesskey`),
  KEY `expiry` (`expiry`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_sessions`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_sessions_data`
-- 

CREATE TABLE `edo_cp_sessions_data` (
  `sesskey` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `data` longtext NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `expiry` (`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_sessions_data`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_shipping`
-- 

CREATE TABLE `edo_cp_shipping` (
  `shipping_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_code` varchar(20) NOT NULL DEFAULT '',
  `shipping_name` varchar(120) NOT NULL DEFAULT '',
  `shipping_desc` varchar(255) NOT NULL DEFAULT '',
  `insure` varchar(10) NOT NULL DEFAULT '0',
  `support_cod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_print` text NOT NULL,
  `print_bg` varchar(255) DEFAULT NULL,
  `config_lable` text,
  `print_model` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`shipping_id`),
  KEY `shipping_code` (`shipping_code`,`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_shipping`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_shipping_area`
-- 

CREATE TABLE `edo_cp_shipping_area` (
  `shipping_area_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_area_name` varchar(150) NOT NULL DEFAULT '',
  `shipping_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `configure` text NOT NULL,
  PRIMARY KEY (`shipping_area_id`),
  KEY `shipping_id` (`shipping_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_shipping_area`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_shop_config`
-- 

CREATE TABLE `edo_cp_shop_config` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `code` varchar(30) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `store_range` varchar(255) NOT NULL DEFAULT '',
  `store_dir` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=904 DEFAULT CHARSET=utf8 AUTO_INCREMENT=904 ;




-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_sim_card`
-- 

CREATE TABLE `edo_cp_sim_card` (
  `id` int(30) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `phone_number` varchar(30) NOT NULL COMMENT '电话号码',
  `provider` varchar(255) NOT NULL COMMENT '供应商',
  `status` int(4) NOT NULL COMMENT '状态',
  `service_password` varchar(255) NOT NULL COMMENT '服务密码',
  `login_password` varchar(255) NOT NULL COMMENT '供应商网络登录密码',
  `phone_location` varchar(255) NOT NULL COMMENT '归属地',
  `sort_order` int(13) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `edo_cp_sim_card`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_snatch_log`
-- 

CREATE TABLE `edo_cp_snatch_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `snatch_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `bid_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bid_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `snatch_id` (`snatch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_snatch_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_station`
-- 

CREATE TABLE `edo_cp_station` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `radius` int(50) NOT NULL,
  `city` int(10) NOT NULL,
  `is_show` int(1) NOT NULL,
  `sort_order` int(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_stats`
-- 

CREATE TABLE `edo_cp_stats` (
  `access_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `visit_times` smallint(5) unsigned NOT NULL DEFAULT '1',
  `browser` varchar(60) NOT NULL DEFAULT '',
  `system` varchar(20) NOT NULL DEFAULT '',
  `language` varchar(20) NOT NULL DEFAULT '',
  `area` varchar(30) NOT NULL DEFAULT '',
  `referer_domain` varchar(100) NOT NULL DEFAULT '',
  `referer_path` varchar(200) NOT NULL DEFAULT '',
  `access_url` varchar(255) NOT NULL DEFAULT '',
  KEY `access_time` (`access_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_suppliers`
-- 

CREATE TABLE `edo_cp_suppliers` (
  `suppliers_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `suppliers_name` varchar(255) DEFAULT NULL,
  `suppliers_desc` mediumtext,
  `is_check` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`suppliers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_suppliers`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_tag`
-- 

CREATE TABLE `edo_cp_tag` (
  `tag_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `tag_words` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag_id`),
  KEY `user_id` (`user_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_tag`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_template`
-- 

CREATE TABLE `edo_cp_template` (
  `filename` varchar(30) NOT NULL DEFAULT '',
  `region` varchar(40) NOT NULL DEFAULT '',
  `library` varchar(40) NOT NULL DEFAULT '',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `number` tinyint(1) unsigned NOT NULL DEFAULT '5',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `theme` varchar(60) NOT NULL DEFAULT '',
  `remarks` varchar(30) NOT NULL DEFAULT '',
  KEY `filename` (`filename`,`region`),
  KEY `theme` (`theme`),
  KEY `remarks` (`remarks`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_topic`
-- 

CREATE TABLE `edo_cp_topic` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '''''',
  `intro` text NOT NULL,
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(10) NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `template` varchar(255) NOT NULL DEFAULT '''''',
  `css` text NOT NULL,
  `topic_img` varchar(255) DEFAULT NULL,
  `title_pic` varchar(255) DEFAULT NULL,
  `base_style` char(6) DEFAULT NULL,
  `htmls` mediumtext,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_topic`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_user_account`
-- 

CREATE TABLE `edo_cp_user_account` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `admin_user` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `add_time` int(10) NOT NULL DEFAULT '0',
  `paid_time` int(10) NOT NULL DEFAULT '0',
  `admin_note` varchar(255) NOT NULL,
  `user_note` varchar(255) NOT NULL,
  `process_type` tinyint(1) NOT NULL DEFAULT '0',
  `payment` varchar(90) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_paid` (`is_paid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_user_account`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_user_address`
-- 

CREATE TABLE `edo_cp_user_address` (
  `address_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `address_name` varchar(50) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `consignee` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `country` smallint(5) NOT NULL DEFAULT '0',
  `province` smallint(5) NOT NULL DEFAULT '0',
  `city` smallint(5) NOT NULL DEFAULT '0',
  `district` smallint(5) NOT NULL DEFAULT '0',
  `address` varchar(120) NOT NULL DEFAULT '',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `sign_building` varchar(120) NOT NULL DEFAULT '',
  `best_time` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`address_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_user_address`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_user_bonus`
-- 

CREATE TABLE `edo_cp_user_bonus` (
  `bonus_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_type_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bonus_sn` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `used_time` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `emailed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bonus_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_user_bonus`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_user_feed`
-- 

CREATE TABLE `edo_cp_user_feed` (
  `feed_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `value_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `feed_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_feed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`feed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_user_feed`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_user_rank`
-- 

CREATE TABLE `edo_cp_user_rank` (
  `rank_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(30) NOT NULL DEFAULT '',
  `min_points` int(10) unsigned NOT NULL DEFAULT '0',
  `max_points` int(10) unsigned NOT NULL DEFAULT '0',
  `discount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `show_price` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `special_rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rank_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_users`
-- 

CREATE TABLE `edo_cp_users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `question` varchar(255) NOT NULL DEFAULT '',
  `answer` varchar(255) NOT NULL DEFAULT '',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `user_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `frozen_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_points` int(10) unsigned NOT NULL DEFAULT '0',
  `rank_points` int(10) unsigned NOT NULL DEFAULT '0',
  `address_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  `last_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `visit_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user_rank` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_special` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `salt` varchar(10) NOT NULL DEFAULT '0',
  `parent_id` mediumint(9) NOT NULL DEFAULT '0',
  `flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alias` varchar(60) NOT NULL,
  `msn` varchar(60) NOT NULL,
  `qq` varchar(20) NOT NULL,
  `office_phone` varchar(20) NOT NULL,
  `home_phone` varchar(20) NOT NULL,
  `mobile_phone` varchar(20) NOT NULL,
  `is_validated` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `credit_line` decimal(10,2) unsigned NOT NULL,
  `passwd_question` varchar(50) DEFAULT NULL,
  `passwd_answer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `email` (`email`),
  KEY `parent_id` (`parent_id`),
  KEY `flag` (`flag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_users`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_vacation`
-- 

CREATE TABLE `edo_cp_vacation` (
  `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `holiday` date NOT NULL COMMENT '休息日',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=216 DEFAULT CHARSET=utf8 AUTO_INCREMENT=216 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_vip_card`
-- 

CREATE TABLE `edo_cp_vip_card` (
  `id` int(13) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `serial` varchar(255) NOT NULL COMMENT '序列号',
  `group_number` varchar(255) NOT NULL COMMENT '用户编号',
  `card_number` varchar(255) NOT NULL COMMENT '卡号',
  `card_type` int(13) NOT NULL COMMENT '卡类型',
  `using_status` int(13) NOT NULL COMMENT '卡状态',
  `allot_status` int(13) NOT NULL COMMENT '分配情况',
  `sort_order` int(13) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

-- 
-- 导出表中的数据 `edo_cp_vip_card`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_vip_card_issue`
-- 

CREATE TABLE `edo_cp_vip_card_issue` (
  `id` int(13) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `buy_time` int(13) NOT NULL COMMENT '购买时间',
  `user_id` int(13) NOT NULL COMMENT '会员id',
  `phone` int(13) unsigned NOT NULL COMMENT '手机号码',
  `vip_card_id` int(13) NOT NULL COMMENT '会员卡id',
  `issue_status` int(3) NOT NULL COMMENT '发送状态,0=未发送,1=已发送',
  `post_man` varchar(255) NOT NULL COMMENT '发卡员',
  `issue_time` int(13) NOT NULL COMMENT '发送时间戳',
  `location` varchar(255) NOT NULL COMMENT '城市',
  `delivery_address` varchar(255) NOT NULL COMMENT '邮寄地址',
  `sort_order` int(13) NOT NULL COMMENT 'sort_order',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_virtual_card`
-- 

CREATE TABLE `edo_cp_virtual_card` (
  `card_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `card_sn` varchar(60) NOT NULL DEFAULT '',
  `card_password` varchar(60) NOT NULL DEFAULT '',
  `add_date` int(11) NOT NULL DEFAULT '0',
  `end_date` int(11) NOT NULL DEFAULT '0',
  `is_saled` tinyint(1) NOT NULL DEFAULT '0',
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `crc32` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`card_id`),
  KEY `goods_id` (`goods_id`),
  KEY `car_sn` (`card_sn`),
  KEY `is_saled` (`is_saled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_virtual_card`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_volume_price`
-- 

CREATE TABLE `edo_cp_volume_price` (
  `price_type` tinyint(1) unsigned NOT NULL,
  `goods_id` mediumint(8) unsigned NOT NULL,
  `volume_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `volume_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`price_type`,`goods_id`,`volume_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_cp_volume_price`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_vote`
-- 

CREATE TABLE `edo_cp_vote` (
  `vote_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `vote_name` varchar(250) NOT NULL DEFAULT '',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0',
  `can_multi` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vote_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_vote`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_vote_log`
-- 

CREATE TABLE `edo_cp_vote_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `vote_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `vote_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `vote_id` (`vote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_vote_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_vote_option`
-- 

CREATE TABLE `edo_cp_vote_option` (
  `option_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `vote_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `option_name` varchar(250) NOT NULL DEFAULT '',
  `option_count` int(8) unsigned NOT NULL DEFAULT '0',
  `option_order` tinyint(3) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`option_id`),
  KEY `vote_id` (`vote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_vote_option`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_wholesale`
-- 

CREATE TABLE `edo_cp_wholesale` (
  `act_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL,
  `goods_name` varchar(255) NOT NULL,
  `rank_ids` varchar(255) NOT NULL,
  `prices` text NOT NULL,
  `enabled` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`act_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_cp_wholesale`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_credit_setting`
-- 

CREATE TABLE `edo_credit_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'user',
  `info` text NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `experience` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 AUTO_INCREMENT=96 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_credit_type`
-- 

CREATE TABLE `edo_credit_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_credit_user`
-- 

CREATE TABLE `edo_credit_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `experience` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_denounce`
-- 

CREATE TABLE `edo_denounce` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `from` varchar(255) NOT NULL COMMENT '目前存入各个应用的名称，比如blog,weibo，说明举报的是不同应用下的内容',
  `aid` int(11) NOT NULL COMMENT '记录内容表的主键ID',
  `state` tinyint(4) NOT NULL COMMENT '记录状态，0，默认，表示刚举报；1，表示已删除；2，表示已经通过可以正常显示；',
  `uid` int(11) NOT NULL COMMENT '记录举报人的UID',
  `fuid` int(11) NOT NULL COMMENT '记录被举报人UID',
  `reason` text NOT NULL COMMENT '举报理由',
  `content` varchar(255) NOT NULL,
  `ctime` int(11) NOT NULL COMMENT '记录举报的时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_denounce`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_document`
-- 

CREATE TABLE `edo_document` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text,
  `author_id` int(11) DEFAULT NULL,
  `last_editor_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_on_footer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在页脚显示',
  `ctime` int(11) DEFAULT NULL,
  `mtime` int(11) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`document_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_expression`
-- 

CREATE TABLE `edo_expression` (
  `expression_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'miniblog',
  `emotion` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`expression_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_feed`
-- 

CREATE TABLE `edo_feed` (
  `feed_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `data` text NOT NULL,
  `ctime` int(11) NOT NULL,
  `type` varchar(120) NOT NULL,
  PRIMARY KEY (`feed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_feed`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_friend`
-- 

CREATE TABLE `edo_friend` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `friend_uid` int(11) NOT NULL,
  `friend_uname` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `message` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`friend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_friend`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_friend_group`
-- 

CREATE TABLE `edo_friend_group` (
  `friend_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`friend_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_friend_group`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_friend_group_link`
-- 

CREATE TABLE `edo_friend_group_link` (
  `uid` int(11) NOT NULL,
  `friend_uid` int(11) NOT NULL,
  `friend_uname` varchar(255) NOT NULL,
  `friend_group_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_friend_group_link`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_invite_record`
-- 

CREATE TABLE `edo_invite_record` (
  `invite_record_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `fid` int(11) unsigned NOT NULL,
  `ctime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`invite_record_id`),
  UNIQUE KEY `uid` (`uid`,`fid`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_invite_record`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_invitecode`
-- 

CREATE TABLE `edo_invitecode` (
  `invite_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `code` varchar(120) NOT NULL,
  `is_used` tinyint(1) NOT NULL,
  `type` char(40) NOT NULL,
  `is_received` tinyint(1) NOT NULL,
  PRIMARY KEY (`invite_code_id`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_invitecode`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_login`
-- 

CREATE TABLE `edo_login` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type_uid` varchar(255) NOT NULL DEFAULT '',
  `type` char(80) NOT NULL,
  `oauth_token` varchar(150) DEFAULT NULL,
  `oauth_token_secret` varchar(150) DEFAULT NULL,
  `is_sync` tinyint(1) NOT NULL,
  PRIMARY KEY (`login_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_login_record`
-- 

CREATE TABLE `edo_login_record` (
  `login_record_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`login_record_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_medal`
-- 

CREATE TABLE `edo_medal` (
  `medal_id` int(11) NOT NULL AUTO_INCREMENT,
  `path_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `data` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` smallint(4) NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`medal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_message`
-- 

CREATE TABLE `edo_message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_uid` int(11) NOT NULL,
  `to_uid` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `source_message_id` int(255) NOT NULL DEFAULT '0',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `is_lastest` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_message`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_message_content`
-- 

CREATE TABLE `edo_message_content` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(11) unsigned NOT NULL,
  `from_uid` int(11) unsigned NOT NULL,
  `content` text,
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `mtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `list_id` (`list_id`,`is_del`,`mtime`),
  KEY `list_id_2` (`list_id`,`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_message_content`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_message_list`
-- 

CREATE TABLE `edo_message_list` (
  `list_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_uid` int(11) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `member_num` smallint(5) unsigned NOT NULL DEFAULT '0',
  `min_max` varchar(17) DEFAULT NULL,
  `mtime` int(11) unsigned NOT NULL,
  `last_message` text NOT NULL,
  PRIMARY KEY (`list_id`),
  KEY `type` (`type`),
  KEY `min_max` (`min_max`),
  KEY `from_uid` (`from_uid`,`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_message_list`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_message_member`
-- 

CREATE TABLE `edo_message_member` (
  `list_id` int(11) unsigned NOT NULL,
  `member_uid` int(11) unsigned NOT NULL,
  `new` smallint(8) unsigned NOT NULL DEFAULT '0',
  `message_num` int(10) unsigned NOT NULL DEFAULT '1',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0',
  `list_ctime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`list_id`,`member_uid`),
  KEY `new` (`new`),
  KEY `ctime` (`ctime`),
  KEY `list_ctime` (`list_ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_message_member`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_myop_friendlog`
-- 

CREATE TABLE `edo_myop_friendlog` (
  `uid` int(11) NOT NULL,
  `fuid` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `dateline` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_myop_friendlog`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_myop_myapp`
-- 

CREATE TABLE `edo_myop_myapp` (
  `appid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `appname` varchar(60) NOT NULL DEFAULT '',
  `narrow` tinyint(1) NOT NULL DEFAULT '0',
  `flag` tinyint(1) NOT NULL DEFAULT '0',
  `version` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `displaymethod` tinyint(1) NOT NULL DEFAULT '0',
  `displayorder` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`appid`),
  KEY `flag` (`flag`,`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_myop_myapp`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_myop_myinvite`
-- 

CREATE TABLE `edo_myop_myinvite` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `typename` varchar(100) NOT NULL DEFAULT '',
  `appid` mediumint(8) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `fromuid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `touid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `myml` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `hash` int(10) unsigned NOT NULL DEFAULT '0',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`),
  KEY `uid` (`touid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_myop_myinvite`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_myop_userapp`
-- 

CREATE TABLE `edo_myop_userapp` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `appid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `appname` varchar(60) NOT NULL DEFAULT '',
  `privacy` tinyint(1) NOT NULL DEFAULT '0',
  `allowsidenav` tinyint(1) NOT NULL DEFAULT '0',
  `allowfeed` tinyint(1) NOT NULL DEFAULT '0',
  `allowprofilelink` tinyint(1) NOT NULL DEFAULT '0',
  `narrow` tinyint(1) NOT NULL DEFAULT '0',
  `menuorder` smallint(6) NOT NULL DEFAULT '0',
  `displayorder` smallint(6) NOT NULL DEFAULT '0',
  `ext` text,
  KEY `uid` (`uid`,`appid`),
  KEY `menuorder` (`uid`,`menuorder`),
  KEY `displayorder` (`uid`,`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_myop_userapp`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_myop_userappfield`
-- 

CREATE TABLE `edo_myop_userappfield` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `appid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `profilelink` text NOT NULL,
  `myml` text NOT NULL,
  KEY `uid` (`uid`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_myop_userappfield`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_myop_userlog`
-- 

CREATE TABLE `edo_myop_userlog` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `dateline` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_node`
-- 

CREATE TABLE `edo_node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) NOT NULL,
  `app_alias` varchar(255) DEFAULT NULL,
  `mod_name` varchar(255) NOT NULL,
  `mod_alias` varchar(255) DEFAULT NULL,
  `act_name` varchar(255) NOT NULL,
  `act_alias` varchar(255) DEFAULT NULL,
  `parent_node_id` int(11) NOT NULL COMMENT '??action',
  `description` text,
  PRIMARY KEY (`node_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_notify`
-- 

CREATE TABLE `edo_notify` (
  `notify_id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `receive` int(11) NOT NULL,
  `type` char(80) NOT NULL,
  `data` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`notify_id`),
  KEY `receive` (`receive`,`is_read`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_notify`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_open_login`
-- 

CREATE TABLE `edo_open_login` (
  `id` int(13) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` varchar(255) NOT NULL COMMENT '自身系统用户ID',
  `open_id` varchar(50) NOT NULL COMMENT '于qq号appID生成的唯一ID,对应app',
  `access_token` varchar(100) NOT NULL COMMENT 'accesstoken',
  `login_from` int(1) NOT NULL COMMENT '从哪登陆进来的,0=qq,1=sina,',
  `last_login_time` datetime NOT NULL,
  `timeout` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '0:不可用;1:可用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='第三方登陆数据表' AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_open_login`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_space`
-- 

CREATE TABLE `edo_space` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `hit` int(11) unsigned NOT NULL DEFAULT '0',
  `setting` text NOT NULL,
  `credit_score` int(11) unsigned NOT NULL DEFAULT '0',
  `credit_exp` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_space`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_system_data`
-- 

CREATE TABLE `edo_system_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `list` char(30) DEFAULT 'default',
  `key` char(50) DEFAULT 'default',
  `value` text,
  `mtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `list` (`list`,`key`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_tag`
-- 

CREATE TABLE `edo_tag` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(120) NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_tag`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_template`
-- 

CREATE TABLE `edo_template` (
  `tpl_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `title` text,
  `body` text,
  `lang` varchar(255) NOT NULL DEFAULT 'zh',
  `type` varchar(255) DEFAULT NULL,
  `type2` varchar(255) DEFAULT NULL,
  `is_cache` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_template_record`
-- 

CREATE TABLE `edo_template_record` (
  `tpl_record_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `tpl_name` varchar(255) NOT NULL DEFAULT '',
  `tpl_alias` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `type2` varchar(255) DEFAULT NULL,
  `data` text,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`tpl_record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_template_record`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_ucenter_user_link`
-- 

CREATE TABLE `edo_ucenter_user_link` (
  `uid` int(11) NOT NULL,
  `uc_uid` mediumint(8) NOT NULL,
  `uc_username` char(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_ucenter_user_link`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_app`
-- 

CREATE TABLE `edo_user_app` (
  `user_app_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `display_order` int(5) NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_app_id`),
  KEY `display_order` (`display_order`),
  KEY `app_id` (`app_id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_user_app`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_blacklist`
-- 

CREATE TABLE `edo_user_blacklist` (
  `uid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `ctime` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_user_blacklist`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_count`
-- 

CREATE TABLE `edo_user_count` (
  `uid` int(11) NOT NULL,
  `atme` mediumint(6) NOT NULL,
  `comment` mediumint(6) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_user_count`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_data`
-- 

CREATE TABLE `edo_user_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text,
  `mtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user-key` (`uid`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_user_data`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_group`
-- 

CREATE TABLE `edo_user_group` (
  `user_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `ctime` int(11) DEFAULT NULL,
  `icon` varchar(120) NOT NULL,
  PRIMARY KEY (`user_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_group_link`
-- 

CREATE TABLE `edo_user_group_link` (
  `user_gorup_link_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) NOT NULL,
  `user_group_title` varchar(255) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`user_gorup_link_id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_group_popedom`
-- 

CREATE TABLE `edo_user_group_popedom` (
  `user_group_popedom_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  PRIMARY KEY (`user_group_popedom_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_medal`
-- 

CREATE TABLE `edo_user_medal` (
  `user_medal_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `medal_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `data` text,
  PRIMARY KEY (`user_medal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_online`
-- 

CREATE TABLE `edo_user_online` (
  `uid` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_privacy`
-- 

CREATE TABLE `edo_user_privacy` (
  `uid` int(11) NOT NULL,
  `key` varchar(120) NOT NULL,
  `value` varchar(120) NOT NULL,
  UNIQUE KEY `key` (`key`),
  UNIQUE KEY `key_2` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_user_privacy`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_profile`
-- 

CREATE TABLE `edo_user_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `module` varchar(150) NOT NULL,
  `data` longtext,
  `type` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_user_profile`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_set`
-- 

CREATE TABLE `edo_user_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldkey` varchar(120) NOT NULL,
  `fieldname` varchar(120) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `module` varchar(60) NOT NULL,
  `showspace` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_tag`
-- 

CREATE TABLE `edo_user_tag` (
  `user_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`user_tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_user_tag`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user_type`
-- 

CREATE TABLE `edo_user_type` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_validation`
-- 

CREATE TABLE `edo_validation` (
  `validation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `from_uid` int(11) NOT NULL DEFAULT '0',
  `to_user` varchar(255) NOT NULL DEFAULT '0',
  `data` text,
  `code` varchar(120) NOT NULL DEFAULT '0',
  `target_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`validation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_validation`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo`
-- 

CREATE TABLE `edo_weibo` (
  `weibo_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `content` text NOT NULL,
  `ctime` int(11) NOT NULL,
  `from` tinyint(1) NOT NULL,
  `comment` mediumint(8) NOT NULL,
  `transpond_id` int(11) NOT NULL DEFAULT '0',
  `transpond` mediumint(8) NOT NULL,
  `type` varchar(255) DEFAULT '0',
  `type_data` text,
  `from_data` text,
  `isdel` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`weibo_id`),
  KEY `type` (`uid`,`type`),
  KEY `transpond` (`uid`,`transpond_id`),
  KEY `ctime` (`ctime`),
  KEY `uid_2` (`uid`,`isdel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_atme`
-- 

CREATE TABLE `edo_weibo_atme` (
  `uid` int(11) NOT NULL,
  `weibo_id` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`weibo_id`),
  KEY `weibo_id` (`weibo_id`,`uid`),
  KEY `uid_2` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_weibo_atme`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_attach`
-- 

CREATE TABLE `edo_weibo_attach` (
  `weibo_id` int(11) NOT NULL,
  `attach_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `weibo_type` tinyint(3) NOT NULL,
  `mtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `user_attach` (`uid`,`attach_id`,`weibo_type`),
  KEY `weibo_index` (`weibo_id`,`weibo_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_weibo_attach`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_comment`
-- 

CREATE TABLE `edo_weibo_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `reply_comment_id` int(11) NOT NULL,
  `reply_uid` int(11) NOT NULL,
  `weibo_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `ctime` int(11) NOT NULL,
  `isdel` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `weibo_id` (`weibo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_comment`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_favorite`
-- 

CREATE TABLE `edo_weibo_favorite` (
  `uid` int(11) NOT NULL,
  `weibo_id` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`weibo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `edo_weibo_favorite`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_follow`
-- 

CREATE TABLE `edo_weibo_follow` (
  `follow_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`follow_id`),
  KEY `fid` (`fid`,`type`),
  KEY `uid` (`uid`,`type`),
  KEY `uid_fid` (`uid`,`fid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_follow`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_follow_group`
-- 

CREATE TABLE `edo_weibo_follow_group` (
  `follow_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`follow_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_follow_group`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_follow_group_link`
-- 

CREATE TABLE `edo_weibo_follow_group_link` (
  `follow_group_link_id` int(11) NOT NULL AUTO_INCREMENT,
  `follow_group_id` int(11) NOT NULL,
  `follow_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`follow_group_link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_follow_group_link`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_plugin`
-- 

CREATE TABLE `edo_weibo_plugin` (
  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_name` varchar(120) NOT NULL,
  `icon_pic` varchar(120) NOT NULL,
  `plugin_path` varchar(255) NOT NULL,
  PRIMARY KEY (`plugin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_star`
-- 

CREATE TABLE `edo_weibo_star` (
  `star_id` int(11) NOT NULL AUTO_INCREMENT,
  `star_group_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`star_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_star`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_star_group`
-- 

CREATE TABLE `edo_weibo_star_group` (
  `star_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `top_group_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`star_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_star_group`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_topic`
-- 

CREATE TABLE `edo_weibo_topic` (
  `topic_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `count` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`topic_id`),
  KEY `count` (`count`),
  KEY `name` (`name`,`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_topic`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_topic_link`
-- 

CREATE TABLE `edo_weibo_topic_link` (
  `weibo_topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `weibo_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '0',
  `transpond_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`weibo_topic_id`),
  KEY `topic_type` (`topic_id`,`type`),
  KEY `topic_transpond` (`topic_id`,`transpond_id`),
  KEY `weibo` (`weibo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_topic_link`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_weibo_topics`
-- 

CREATE TABLE `edo_weibo_topics` (
  `topics_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) unsigned NOT NULL,
  `domain` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `recommend` enum('1','0') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  `isdel` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`topics_id`),
  UNIQUE KEY `page` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edo_weibo_topics`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edo_violate_deposit_log`
-- 

CREATE TABLE `edo_violate_deposit_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `money` varchar(30) NOT NULL COMMENT '违章押金金额',
  `transaction_number` varchar(40) NOT NULL COMMENT '金额交易流水号',
  `order_id` int(11) NOT NULL COMMENT '订单号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `status` smallint(6) NOT NULL COMMENT '0:未结算 1:已结算',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=armscii8 COMMENT='违章押金记录表' AUTO_INCREMENT=78 ;




--
-- 表的结构 `edo_cash_account`
--

CREATE TABLE `edo_cash_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `amount` varchar(40) NOT NULL COMMENT '余额',
  `freeze_money` varchar(40) NOT NULL COMMENT '冻结金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='现金账户表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `edo_cash_account`
--



--
-- 表的结构 `edo_account_log`
--

CREATE TABLE `edo_account_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id,日志id',
  `money` varchar(50) NOT NULL COMMENT '操作金额',
  `use_type` varchar(30) NOT NULL COMMENT '操作动作',
  `time` int(11) NOT NULL COMMENT '操作时间',
  `transaction_number` varchar(50) NOT NULL COMMENT '交易流水号',
  `user_id` int(11) NOT NULL COMMENT '操作者id',
  `remark` varchar(50) NOT NULL COMMENT '备注',
  `account_type` varchar(50) NOT NULL COMMENT '操作账户类型',
  `account_id` int(11) NOT NULL COMMENT '操作账户ID',
  `error_code` int(11) NOT NULL COMMENT '操作错误码',
  `balance_amount` int(11) NOT NULL COMMENT '完成当前操作后，所剩余额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='账户操作日志表' AUTO_INCREMENT=79 ;

--
-- 转存表中的数据 `edo_account_log`
--



--
-- 表的结构 `edo_freeze_record`
--

CREATE TABLE `edo_freeze_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `account_id` int(11) NOT NULL COMMENT '记录金钱账户id',
  `money` varchar(50) NOT NULL COMMENT '交易金额',
  `transaction_number` varchar(50) NOT NULL COMMENT '交易流水号',
  `time` int(11) NOT NULL COMMENT '交易时间',
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `transaction_number` (`transaction_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='冻结记录表' AUTO_INCREMENT=74 ;

--
-- 转存表中的数据 `edo_freeze_record`
--

-- --------------------------------------------------------

--
-- 表的结构 `edo_cp_client_soft`
--

CREATE TABLE IF NOT EXISTS `edo_cp_client_soft` (
  `version_code` int(11) NOT NULL,
  `version_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `version_desc` varchar(256) COLLATE utf8_bin NOT NULL,
  `version_url` varchar(256) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`version_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

-- 
-- 表的结构 `edo_cp_credit_card`
-- 

CREATE TABLE `edo_cp_credit_card` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `credit_card_year` int(8) NOT NULL COMMENT '信用卡有效期年',
  `credit_card_month` int(8) NOT NULL COMMENT '信用卡有效期月',
  `credit_card_CVN2` int(8) NOT NULL COMMENT 'CVN2',
  `sort_order` int(11) NOT NULL,
  `credit_card_id` varchar(32) NOT NULL,
  `credit_card_bank` varchar(16) default NULL,
  `user_id` bigint(20) NOT NULL COMMENT '支付通道编码列表',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;
 

-- 
-- 表的结构 `edo_cp_identity_approve`
-- 

CREATE TABLE `edo_cp_identity_approve` (
  `id` int(13) NOT NULL auto_increment COMMENT '自增id',
  `approve_stat` int(1) NOT NULL COMMENT '用位表示的结果。身份证正面通过1；其它通过2；学生证通过4',
  `user_id` int(13) unsigned NOT NULL COMMENT '用户 id',
  `post_time` int(13) NOT NULL COMMENT '提交时间',
  `img_identity_front` varchar(255) NOT NULL COMMENT '身份证正面url',
  `img_others` varchar(255) NOT NULL COMMENT '身份证背面,驾驶证正反面',
  `img_student` varchar(255) NOT NULL COMMENT '学生证url',
  `identity_num` varchar(32) default NULL COMMENT '审核通过时记录身份证号码',
  `is_processed` int(1) NOT NULL COMMENT '0=未处理(待审核),1=已处理(已审核),2=审核中（防止审核的不是用户最新上传的照片）',
  `first_approve_time` int(11) NOT NULL COMMENT '第一次通过审核的时间',
  `sort_order` int(3) NOT NULL COMMENT '排序',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
-- --------------------------------------------------------

-- 
-- 表的结构 `edo_user`
--
 
CREATE TABLE `edo_user` (
  `uid` int(11) NOT NULL auto_increment,
  `email` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `uname` varchar(255) default NULL,
  `sex` tinyint(1) NOT NULL default '0',
  `province` mediumint(6) NOT NULL default '0',
  `city` mediumint(6) NOT NULL default '0',
  `location` varchar(255) default NULL,
  `admin_level` varchar(255) default '0',
  `commend` tinyint(1) default NULL,
  `is_active` tinyint(1) default '0',
  `is_init` tinyint(1) NOT NULL,
  `is_synchronizing` tinyint(1) NOT NULL default '0',
  `ctime` int(11) default NULL,
  `identity` tinyint(1) NOT NULL default '1',
  `score` int(11) NOT NULL default '0',
  `myop_menu_num` int(2) NOT NULL default '10',
  `api_key` varchar(255) default NULL,
  `domain` char(80) default NULL,
  `phone_num` varchar(32) default NULL,
  `alias` varchar(64) default NULL,
  `phone` varchar(255) NOT NULL COMMENT '手机号码',
  `account_stat` int(3) NOT NULL COMMENT '可用=0、违章=1、欠费=2、禁用=3,删除=4',
  `vip_card_id` int(13) NOT NULL COMMENT '会员卡ID',
  `user_group_id` int(13) NOT NULL COMMENT '用户群组ID',
  `approve_id` int(13) NOT NULL COMMENT '审核id',
  `user_type` int(3) NOT NULL COMMENT '会员类别、0=个人、1=学生',
  `true_name` varchar(255) default NULL COMMENT '真实姓名',
  `alipay_name` varchar(255) default NULL COMMENT '支付宝账户昵称',
  `alipay_account` varchar(255) default NULL COMMENT '支付宝账户',
  `invoice_title` varchar(255) default NULL COMMENT '发票抬头',
  `cash_remain` int(13) NOT NULL COMMENT '现金账户余额',
  `invoice_address` varchar(255) default NULL COMMENT '发票寄送地址',
  `invoice_zip` int(7) default NULL COMMENT '发票寄送地址邮编',
  `validfor` int(11) NOT NULL COMMENT '驾照有效期',
  `birthday` varchar(22) NOT NULL COMMENT '出生日期',
  `consume_point` int(11) NOT NULL default '0' COMMENT '消费积分',
  `token` varchar(32) NOT NULL COMMENT '系统登陆token',
  `token_expire` int(11) NOT NULL COMMENT 'token过期时间戳',
  `protocal_version` smallint(6) NOT NULL COMMENT '签订协议的版本',
  `super_password` varchar(64) default NULL COMMENT '超级密码',
  `find_password_date` date NOT NULL,
  `find_password_times` smallint(6) NOT NULL,
  `need_modify_password` smallint(6) NOT NULL COMMENT '是否需要修改密码。0：不需要，1：需要',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `email` (`email`),
  KEY `location` (`location`),
  KEY `token` (`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

-- 
-- 导出表中的数据 `edo_user`
-- 



-- --------------------------------------------------------




-- 
-- 表的结构 `edo_cp_medal`
-- 

CREATE TABLE `edo_cp_medal` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `file` varchar(128) NOT NULL COMMENT 'php文件所指目录',
  `class_name` varchar(32) NOT NULL COMMENT '类名',
  `icon` varchar(64) NOT NULL COMMENT '图片上传的地址',
  `medaldesc` varchar(256) NOT NULL COMMENT '描述奖章信息',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;


-- --------------------------------------------------------


-- 
-- 表的结构 `edo_cp_user2medal`
-- 

CREATE TABLE `edo_cp_user2medal` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `medal_id` int(11) NOT NULL,
  `time` datetime NOT NULL COMMENT '领取时间',
  `status` smallint(6) NOT NULL COMMENT '0代表未领取 1代表已领取 2代表已使用',
  `relative_attr` varchar(64) NOT NULL COMMENT '由metal_id决定，比如，metal_id对应的奖章为"转转转"，那么，这儿的值就是开放平台的weibo_id；如果是“掌门人”，值就是“被邀请并首次完成租车”的用户数。 如果“易多达人”，对应充值的transaction_id',
  `use_time` int(11) NOT NULL COMMENT '使用奖章的时间',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- 
-- --------------------------------------------------------

--
-- 表的结构 `edo_timer`
--

CREATE TABLE `edo_timer` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增',
  `exec_time` int(11) NOT NULL COMMENT '将要在此时刻执行',
  `php_uri` varchar(128) NOT NULL COMMENT '要使用的php文件',
  `task_type` varchar(16) DEFAULT NULL COMMENT '任务类型',
  `param` varchar(256) NOT NULL COMMENT '序列化的参数',
  PRIMARY KEY (`id`),
  KEY `exec_time` (`exec_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='定时任务管理表' AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `edo_cp_user_buy_rank` (
  `id` int(3) NOT NULL COMMENT '等级',
  `name` varchar(50) NOT NULL COMMENT '等级名称',
  `point` int(3) NOT NULL COMMENT '所需点数',
  `revise_fee` varchar(20) DEFAULT NULL COMMENT '修订费率',
  `free_times` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE  `edo_cp_sys_config` (
 `type` VARCHAR( 16 ) NOT NULL COMMENT  '类型，比如取消订单计费策略配置‘cancel_order’',
 `param` VARCHAR( 16 ) NOT NULL COMMENT  '字段名,比如time',
 `value` VARCHAR( 32 ) NOT NULL COMMENT  '值，比如24'
) ENGINE = INNODB;



-- --------------------------------------------------------
--
-- 表的结构 `edo_cp_car_group2user_group`
--


CREATE TABLE `edo_cp_car_group2user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增',
  `car_group` int(11) NOT NULL COMMENT '车群组id',
  `user_group` int(11) NOT NULL COMMENT '用户组id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='车群的车只能被用户群使用' AUTO_INCREMENT=1 ;



CREATE TABLE  `edo`.`edo_cp_car_init_info` (
`car_id` INT( 11 ) NOT NULL COMMENT  '汽车id',
`init_time` INT( 11 ) NOT NULL COMMENT  '投入运营时间',
`init_mile` INT( 11 ) NOT NULL COMMENT  '投入运营里程数',
`compact_start` INT( 11 ) NOT NULL COMMENT  '运营合同期开始时间',
`compact_end` INT( 11 ) NOT NULL COMMENT  '运营合同期结束时间',
`provider_code` VARCHAR( 32 ) NOT NULL COMMENT  '运营商代号',
PRIMARY KEY (  `car_id` )
) ENGINE = MYISAM COMMENT =  '车辆初始信息';


CREATE TABLE `edo_cp_outlay_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增',
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `money` varchar(11) NOT NULL COMMENT '缴费金额',
  `car_id` int(11) NOT NULL COMMENT '汽车id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='缴费记录表' AUTO_INCREMENT=1 ;


CREATE TABLE `edo_cp_car_halt` (
  `id` int(11) NOT NULL COMMENT '自增',
  `car_id` int(11) NOT NULL COMMENT '汽车id',
  `reason` varchar(64) NOT NULL COMMENT '禁用原因',
  `time` int(11) NOT NULL COMMENT '禁用时间',
  `admin_id` int(11) NOT NULL COMMENT '管理员id'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='车辆禁用记录表';

create table car_device
(
   id                             bigint                         not null AUTO_INCREMENT,
   car_id                         bigint,
   hardware_version               varchar(8),
   software_version               varchar(8),
   status                         smallint,
   longitude                      int,
   latitude                       int,
   last_update_time               datetime,
   sys_state                      smallint,
   vss_counter                    varchar(32),
   oil_card_status                smallint,
   park_card_status               smallint,
   car_status                     smallint,
   last_start_time                datetime,
   door_status                    smallint,
   last_door_open_time            datetime,
   model                          varchar(30),
   oil_card                       varchar(255),
   sim                            varchar(50),
   serial                         varchar(50),
   cts							  int,
   primary key (id)
)AUTO_INCREMENT=10001;

create table order_charge
(
   id                             int                            not null AUTO_INCREMENT,
   order_no                       varchar(16),
   item                           smallint,
   amount                         int,
   order_id                       bigint,
   add_time                       datetime,
   status                         smallint,
   pay_time                       datetime,
   user_id                        bigint,
   primary key (id)
);

create table user_protocal
(
   version                        smallint
);

create table user_location
(
   id                             bigint                         not null AUTO_INCREMENT,
   user_id                        bigint,
   relate_operation               varchar(32),
   longitude                      int,
   latitude                       int,
   accuracy                       int,
   altitude                       int,
   provider                       varchar(16),
   city_id                        bigint,
   others                         varchar(512),
   primary key (id)
);

create table upp_payment_log
(
   id                             bigint                         not null AUTO_INCREMENT,
   trans_type                     varchar(32),
   submit_time                    datetime,
   order_id                       varchar(32),
   order_generate_time            datetime,
   settle_date                    date,
   transmit_time                  datetime,
   bill_amount                    int,
   account_number1                varchar(32),
   trans_serial_number            varchar(32),
   trans_amount                   int,
   orig_submit_time               datetime,
   payment_type                   varchar(32),
   errcode						  varchar(8),
   primary key (id)
);

create table order_balance
(
   id                             bigint                         not null AUTO_INCREMENT,
   order_id                       int,
   pay_amount                     int,
   account_type                   smallint,
   ext_id                         varchar(64),
   relation                       smallint,
   remarks                        varchar(255),
   primary key (id)
);

create table charge2balance
(
   balance_id                     bigint,
   charge_id                      bigint
);

create table cash_account_charge_req
(
   id                             bigint                         not null AUTO_INCREMENT,
   user_id                        bigint,
   amount                         int,
   status                         smallint,
   time                           datetime,
   primary key (id)
);

create table T_Car_Pos
(
   FCar_id                        bigint,
   FLongitude                     int,
   FLatitude                      int,
   FRadius                        int
);

create table invoice_info
(
   id                             bigint                         not null AUTO_INCREMENT,
   type                           smallint,
   ext_id                         bigint,
   invoice_title                  varchar(256),
   invoice_address                varchar(256),
   contact                        varchar(32),
   phone                          varchar(32),
   zip_code                       varchar(16),
   primary key (id)
);

-- 表的结构 `business_consume`
--

CREATE TABLE `business_consume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `is_public` smallint(6) NOT NULL COMMENT '是否公用',
  `ctime` int(11) NOT NULL COMMENT '支付时间',
  `money_provenance` varchar(256) COLLATE utf8_bin NOT NULL,
  `reason` varchar(256) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `business_consume`
--

create table order_evaluation
(
   order_no                       varchar(32),
   level                          smallint,
   content                        varchar(512)
);


CREATE TABLE IF NOT EXISTS `web_upp_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(32) COLLATE utf8_bin NOT NULL,
  `qid` varchar(32) COLLATE utf8_bin NOT NULL,
  `amount` int(11) NOT NULL,
  `fron_url` varchar(256) COLLATE utf8_bin NOT NULL,
  `back_url` varchar(256) COLLATE utf8_bin NOT NULL,
  `type` varchar(16) COLLATE utf8_bin NOT NULL,
  `order_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='网页银联支付记录' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `web_upp_log`
--
CREATE TABLE IF NOT EXISTS `edo_cp_recharge` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `amount` int(11) NOT NULL COMMENT '充值金额',
  `operator_id` int(11) NOT NULL COMMENT '操作人id',
  `recharge_time` int(11) NOT NULL COMMENT '充值时间',
  `reason` text CHARACTER SET utf8 NOT NULL COMMENT '充值原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- 表的结构 `upp_mobile_log`
--
CREATE TABLE `upp_mobile_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_time` varchar(32) DEFAULT NULL,
  `settleDate` varchar(20) DEFAULT NULL COMMENT '结算时间',
  `respCode` varchar(20) DEFAULT NULL,
  `orderNumber` varchar(32) DEFAULT NULL,
  `exchangeRate` varchar(20) DEFAULT NULL COMMENT '汇率',
  `charset` varchar(10) DEFAULT NULL,
  `signature` varchar(32) DEFAULT NULL,
  `sysReserved` varchar(32) DEFAULT NULL,
  `acqCode` bigint(20) DEFAULT NULL,
  `traceNumber` varchar(32) DEFAULT NULL,
  `settleCurrency` bigint(20) DEFAULT NULL,
  `version` varchar(10) DEFAULT NULL,
  `transType` varchar(10) DEFAULT NULL,
  `settleAmount` bigint(20) DEFAULT NULL,
  `signMethod` varchar(10) DEFAULT NULL,
  `transStatus` varchar(10) DEFAULT NULL,
  `merId` varchar(32) DEFAULT NULL,
  `qn` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;


-- 表的结构 `after_pay_group`
--

CREATE TABLE `after_pay_group` (
  `group_id` int(11) NOT NULL,
  `limit_money` int(11) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 表的结构 `edo_car_stop`
--

CREATE TABLE `edo_car_stop` (
  `group_id` int(10) NOT NULL,
  UNIQUE KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

