CREATE TABLE IF NOT EXISTS `#__jem_events` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `locid` int(11) unsigned NOT NULL default '0',
  `dates` date NULL default NULL,
  `enddates` date NULL default NULL,
  `times` time NULL default NULL,
  `endtimes` time NULL default NULL,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `created_by` int(11) unsigned NOT NULL default '0',
  `modified` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL default '0',
  `version` int(11) unsigned NOT NULL default '0',
  `author_ip` varchar(15) NOT NULL default '',
  `created` datetime NOT NULL,
  `introtext` mediumtext NOT NULL,
  `meta_keywords` varchar(200) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `recurrence_first_id` int(11) NOT NULL default '0',
  `recurrence_interval` int(2) NOT NULL default '0',
  `recurrence_type` int(2) NOT NULL default '0',
  `recurrence_counter` int(11) NOT NULL default '0',
  `recurrence_limit` int(11) NOT NULL default '0',
  `recurrence_limit_date` date NULL default NULL,
  `recurrence_byday` varchar(20) NULL default NULL,
  `datimage` varchar(100) NOT NULL default '',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `registra` tinyint(1) NOT NULL default '0',
  `unregistra` tinyint(1) NOT NULL default '0',
  `maxplaces` int(11) NOT NULL default '0',
  `waitinglist` tinyint(1) NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `contactid` tinyint(4) NOT NULL default '0',
  `custom1` varchar(200) NOT NULL DEFAULT '',
  `custom2` varchar(200) NOT NULL DEFAULT '',
  `custom3` varchar(100) NOT NULL DEFAULT '',
  `custom4` varchar(100) NOT NULL DEFAULT '',
  `custom5` varchar(100) NOT NULL DEFAULT '',
  `custom6` varchar(100) NOT NULL DEFAULT '',
  `custom7` varchar(100) NOT NULL DEFAULT '',
  `custom8` varchar(100) NOT NULL DEFAULT '',
  `custom9` varchar(100) NOT NULL DEFAULT '',
  `custom10` varchar(100) NOT NULL DEFAULT '',
  `fulltext` mediumtext NOT NULL,
  `created_by_alias` varchar(255) NOT NULL,
  `access` int(10) NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `attribs` varchar(5120) NOT NULL,
  `language` char(7) NOT NULL DEFAULT '*',
  `recurrence_group` varchar(20) NOT NULL,
  `recurrence_until` varchar(20) NOT NULL,
  `recurrence_freq` varchar(20) NOT NULL,
  `wholeday` varchar(2) NOT NULL,
  `recurrence_count` int(11) NOT NULL,
  `recurrence_weekday` varchar(100) NOT NULL,
  `startDateTime` varchar(100) NOT NULL,
  `endDateTime` varchar(100) NOT NULL,
  `note` varchar(100) NOT NULL,
  `mailing` TEXT NOT NULL,
  `opendate` INT NOT NULL DEFAULT '0',
  `registering` text NOT NULL,
  `asset_id` INT( 10 ) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__jem_venues` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `venue` varchar(50) NOT NULL default '',
  `alias` varchar(100) NOT NULL default '',
  `url` varchar(200)  NOT NULL default '',
  `street` varchar(50) default NULL,
  `postalCode` varchar(20) default NULL,
  `city` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `country` varchar(2) default NULL,
  `latitude` float(10,6) default NULL,
  `longitude` float(10,6) default NULL,
  `locdescription` mediumtext NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `locimage` varchar(100) NOT NULL default '',
  `map` tinyint(4) NOT NULL default '0',
  `created_by` int(11) unsigned NOT NULL default '0',
  `author_ip` varchar(15) NOT NULL default '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL default '0',
  `version` int(11) unsigned NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `publish_up` datetime NOT NULL,
  `publish_down` datetime NOT NULL,
  `custom1` varchar(200) NOT NULL DEFAULT '',
  `custom2` varchar(200) NOT NULL DEFAULT '',
  `custom3` varchar(100) NOT NULL DEFAULT '',
  `custom4` varchar(100) NOT NULL DEFAULT '',
  `custom5` varchar(100) NOT NULL DEFAULT '',
  `custom6` varchar(100) NOT NULL DEFAULT '',
  `custom7` varchar(100) NOT NULL DEFAULT '',
  `custom8` varchar(100) NOT NULL DEFAULT '',
  `custom9` varchar(100) NOT NULL DEFAULT '',
  `custom10` varchar(100) NOT NULL DEFAULT '',
  `timezone` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(100) NOT NULL DEFAULT '',
  `fax` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `asset_id` INT( 10 ) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;


CREATE TABLE IF NOT EXISTS `#__jem_dates` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(10) NOT NULL,
  `calendar` varchar(10) NOT NULL,
  `ignore_date` varchar(10) NOT NULL,
  `ignore_calendar` int(11) NOT NULL,
  `holiday` varchar(10) NOT NULL,
  `date_range` int(4) NOT NULL,
  `date_name` varchar(200) NOT NULL,
  `date_startdate_range` varchar(200) NOT NULL,
  `date_enddate_range` varchar(200) NOT NULL,
  `date_singledate` int(4) NOT NULL,
  `enabled` int(4) NOT NULL,
  `calendar_name` varchar(200) NOT NULL,
  `calendar_linked` varchar(1000) NOT NULL,
  `calendar_class` varchar(200) NOT NULL,
  `calendar_img` varchar(200) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci` ;

CREATE TABLE IF NOT EXISTS `#__jem_recurrence` (
  `itemid` int(11) NOT NULL,
  `categories` varchar(200) NOT NULL,
  `groupid` varchar(200) NOT NULL,
  `groupid_ref` varchar(50) DEFAULT NULL,
  `first_id` int(11) NOT NULL,
  `counter` int(11) NOT NULL,
  `interval` int(11) NOT NULL,
  `byday` varchar(20) NOT NULL,
  `dtend` varchar(20) NOT NULL,
  `dtstart` varchar(20) NOT NULL,
  `until` varchar(20) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exdate` varchar(20) NOT NULL,
  `groupidhide` int(11) NOT NULL,
  `recurrence_id` varchar(20) NOT NULL,
  `startdate_org` varchar(20) NOT NULL,
  `enddate_org` varchar(20) NOT NULL,
  `startdate_new` varchar(20) NOT NULL,
  `enddate_new` varchar(20) NOT NULL,
  `freq` varchar(20) NOT NULL,
  `deleted` int(11) NOT NULL,
  `ignore` int(11) NOT NULL,
  `wholeday` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `groupid_ref` (`groupid_ref`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;


CREATE TABLE IF NOT EXISTS `#__jem_recurrence_master` (
  `itemid` int(11) NOT NULL,
  `categories` varchar(200) NOT NULL,
  `groupid` varchar(200) NOT NULL,
  `groupid_ref` varchar(50) DEFAULT NULL,
  `first_id` int(11) NOT NULL,
  `counter` int(11) NOT NULL,
  `interval` int(11) NOT NULL,
  `byday` varchar(20) NOT NULL,
  `dtend` varchar(20) NOT NULL,
  `dtstart` varchar(20) NOT NULL,
  `until` varchar(20) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exdate` varchar(20) NOT NULL,
  `groupidhide` int(11) NOT NULL,
  `recurrence_id` varchar(20) NOT NULL,
  `startdate_org` varchar(20) NOT NULL,
  `enddate_org` varchar(20) NOT NULL,
  `startdate_new` varchar(20) NOT NULL,
  `enddate_new` varchar(20) NOT NULL,
  `freq` varchar(20) NOT NULL,
  `deleted` int(11) NOT NULL,
  `link` varchar(200) NOT NULL,
  `location` varchar(200) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `title` varchar(200) NOT NULL,
  `venue` varchar(200) NOT NULL,
  `city` varchar(200) NOT NULL,
  `state` varchar(200) NOT NULL,
  `url` varchar(200) NOT NULL,
  `street` varchar(200) NOT NULL,
  `countryname` varchar(200) NOT NULL,
  `locid` int(10) NOT NULL,
  `exdates` varchar(1000) NOT NULL,
  `wholeday` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `groupid_ref` (`groupid_ref`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE `utf8_general_ci` ;

CREATE TABLE IF NOT EXISTS `#__jem_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `catname` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(100) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `image` varchar(100) NOT NULL DEFAULT '',
  `color` varchar(20) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) unsigned NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `language` varchar(7) NOT NULL,
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `path` varchar(255) NOT NULL,
  `metadata` varchar(2048) NOT NULL,
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `email` varchar(200) NOT NULL DEFAULT '',
  `asset_id` INT( 10 ) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__jem_cats_event_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL default '0',
  `itemid` int(11) NOT NULL default '0',
  `ordering` tinyint(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category event relation` (`catid`,`itemid`),
  KEY `catid` (`catid`),
  KEY `itemid` (`itemid`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__jem_register` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `event` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `uregdate` varchar(50) NOT NULL default '',
  `uip` varchar(15) NOT NULL default '',
  `waiting` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__jem_groups` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(150) NOT NULL default '',
  `description` mediumtext NOT NULL,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `addvenue` int(11) NOT NULL,
  `addevent` int(11) NOT NULL,
  `publishvenue` int(11) NOT NULL,
  `editvenue` int(11) NOT NULL,
  `publishevent` int(11) NOT NULL,
  `editevent` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__jem_groupmembers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `member` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__jem_settings` (
  `id` int(11) unsigned NOT NULL,
  `oldevent` tinyint(4) NOT NULL,
  `minus` tinyint(4) NOT NULL,
  `showtitle` tinyint(4) NOT NULL,
  `showlocate` tinyint(4) NOT NULL,
  `showcity` tinyint(4) NOT NULL,
  `showmapserv` tinyint(4) NOT NULL,
  `tablewidth` varchar(20) NOT NULL,
  `datewidth` varchar(20) NOT NULL,
  `titlewidth` varchar(20) NOT NULL,
  `locationwidth` varchar(20) NOT NULL,
  `citywidth` varchar(20) NOT NULL,
  `formatdate` varchar(100) NOT NULL,
  `formatShortDate` varchar(20) NOT NULL,
  `formattime` varchar(100) NOT NULL,
  `timename` varchar(50) NOT NULL,
  `showdetails` tinyint(4) NOT NULL,
  `showlinkvenue` tinyint(4) NOT NULL,
  `delivereventsyes` tinyint(4) NOT NULL,
  `autopubl` tinyint(4) NOT NULL,
  `deliverlocsyes` tinyint(4) NOT NULL,
  `autopublocate` tinyint(4) NOT NULL,
  `showcat` tinyint(4) NOT NULL,
  `catfrowidth` varchar(20) NOT NULL,
  `evdelrec` tinyint(4) NOT NULL,
  `evpubrec` tinyint(4) NOT NULL,
  `locdelrec` tinyint(4) NOT NULL,
  `locpubrec` tinyint(4) NOT NULL,
  `sizelimit` varchar(20) NOT NULL,
  `imagehight` varchar(20) NOT NULL,
  `imagewidth` varchar(20) NOT NULL,
  `gddisabled` tinyint(4) NOT NULL,
  `imageenabled` tinyint(4) NOT NULL,
  `catlinklist` tinyint(4) NOT NULL,
  `eventedit` tinyint(4) NOT NULL,
  `eventeditrec` tinyint(4) NOT NULL,
  `eventowner` tinyint(4) NOT NULL,
  `venueedit` tinyint(4) NOT NULL,
  `venueeditrec` tinyint(4) NOT NULL,
  `venueowner` tinyint(4) NOT NULL,
  `lightbox` tinyint(4) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `showeventimage` tinyint(4) NOT NULL,
  `statewidth` varchar(20) NOT NULL,
  `regname` tinyint(4) NOT NULL,
  `storeip` tinyint(4) NOT NULL,
  `lastupdate` varchar(20) NOT NULL DEFAULT '',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tableeventimagewidth` varchar(20) NOT NULL,
  `display_num` tinyint(4) NOT NULL,
  `cat_num` tinyint(4) NOT NULL,
  `showatte` int(11) NOT NULL,
  `attewidth` varchar(10) NOT NULL,
  `attachments_path` varchar(100) NOT NULL DEFAULT 'media/com_jem/attachments',
  `attachments_maxsize` varchar(15) NOT NULL DEFAULT '1000',
  `attachments_types` varchar(100) NOT NULL DEFAULT 'txt,doc,jpg,jpeg,png',
  `ownedvenuesonly` int(11) NOT NULL,
  `recurrence_anticipation` varchar(20) NOT NULL DEFAULT '60',
  `ical_max_items` tinyint(4) NOT NULL DEFAULT '100',
  `empty_cat` tinyint(4) NOT NULL DEFAULT '1',
  `defaultCountry` varchar(10) NOT NULL,
  `globalattribs` varchar(5120) NOT NULL,
  `css` varchar (5120) NOT NULL DEFAULT '',
  `version` varchar (20) NOT NULL DEFAULT '3.0.7',
  `vevent` text NOT NULL DEFAULT '',
  `vvenue` text NOT NULL DEFAULT '',
  `vvenues` text NOT NULL DEFAULT '',
  `vcategories` text NOT NULL DEFAULT '',
  `vcategory` text NOT NULL DEFAULT '',
  `vcalendar` text NOT NULL DEFAULT '',
  `veditevent` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__jem_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `object` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `frontend` tinyint(1) NOT NULL default '1',
  `access` tinyint(3) NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `added_by` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__jem_countries` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `continent` varchar(2) NOT NULL,
  `iso2` varchar(2) NOT NULL,
  `iso3` varchar(3) NOT NULL,
  `un` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `iso2` (`iso2`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;


INSERT INTO `#__jem_settings` (`id`, `oldevent`, `minus`, `showtitle`, `showlocate`, `showcity`, `showmapserv`, `tablewidth`, `datewidth`, `titlewidth`, `locationwidth`, `citywidth`, `formatdate`, `formatShortDate`, `formattime`, `timename`, `showdetails`, `showlinkvenue`, `delivereventsyes`, `autopubl`, `deliverlocsyes`, `autopublocate`, `showcat`, `catfrowidth`, `evdelrec`, `evpubrec`, `locdelrec`, `locpubrec`, `sizelimit`, `imagehight`, `imagewidth`, `gddisabled`, `imageenabled`, `catlinklist`, `eventedit`, `eventeditrec`, `eventowner`, `venueedit`, `venueeditrec`, `venueowner`, `lightbox`, `meta_keywords`, `meta_description`, `showeventimage`, `statewidth`, `regname`, `storeip`, `lastupdate`, `checked_out`, `checked_out_time`, `tableeventimagewidth`, `display_num`, `cat_num`, `showatte`, `attewidth`, `attachments_path`, `attachments_maxsize`, `attachments_types`, `ownedvenuesonly`, `recurrence_anticipation`, `ical_max_items`, `empty_cat`, `defaultCountry`, `globalattribs`, `css`, `vevent`, `vvenue`, `vvenues`, `vcategories`, `vcategory`) VALUES
(1, 2, 1, 1, 1, 1, 1, '100%', '20%', '40%', '20%', '', 'D, j. F Y', 'd.m.y', '%H.%M', 'h', 1, 1, -2, -2, -2, -2, 1, '', 1, 1, 1, 1, '100', '100', '100', 1, 1, 1, -2, 1, 0, -2, 1, 0, 1, '[title], [a_name], [times]', 'The event titled [title] starts on [dates]!', 0, '0', 0, 0, '1411731117', 0, '0000-00-00 00:00:00', '10%', 10, 0, 0, '10%','media/com_jem/attachments', '1000', 'txt,csv,htm,html,xml,css,doc,xls,zip,rtf,ppt,pdf,swf,flv,avi,wmv,mov,jpg,jpeg,gif,png,tar.gz', 0, '60', 100, 1, '0', '', '', '', '', '', '', '');


INSERT IGNORE INTO `#__jem_countries` (`id`, `continent`, `iso2`, `iso3`, `un`, `name`) VALUES
(1, 'AS', 'AF', 'AFG', 4, 'Afghanistan, Islamic Republic of'),
(2, 'EU', 'AX', 'ALA', 248, 'Aland Islands'),
(3, 'EU', 'AL', 'ALB', 8, 'Albania, Republic of'),
(4, 'AF', 'DZ', 'DZA', 12, 'Algeria, People''s Democratic Republic of'),
(5, 'OC', 'AS', 'ASM', 16, 'American Samoa'),
(6, 'EU', 'AD', 'AND', 20, 'Andorra, Principality of'),
(7, 'AF', 'AO', 'AGO', 24, 'Angola, Republic of'),
(8, 'NA', 'AI', 'AIA', 660, 'Anguilla'),
(9, 'AN', 'AQ', 'ATA', 10, 'Antarctica'),
(10, 'NA', 'AG', 'ATG', 28, 'Antigua and Barbuda'),
(11, 'SA', 'AR', 'ARG', 32, 'Argentina, Argentine Republic'),
(12, 'AS', 'AM', 'ARM', 51, 'Armenia, Republic of'),
(13, 'NA', 'AW', 'ABW', 533, 'Aruba'),
(14, 'OC', 'AU', 'AUS', 36, 'Australia, Commonwealth of'),
(15, 'EU', 'AT', 'AUT', 40, 'Austria, Republic of'),
(16, 'AS', 'AZ', 'AZE', 31, 'Azerbaijan, Republic of'),
(17, 'NA', 'BS', 'BHS', 44, 'Bahamas, Commonwealth of The'),
(18, 'AS', 'BH', 'BHR', 48, 'Bahrain, Kingdom of'),
(19, 'AS', 'BD', 'BGD', 50, 'Bangladesh, People''s Republic of'),
(20, 'NA', 'BB', 'BRB', 52, 'Barbados'),
(21, 'EU', 'BY', 'BLR', 112, 'Belarus, Republic of'),
(22, 'EU', 'BE', 'BEL', 56, 'Belgium, Kingdom of'),
(23, 'NA', 'BZ', 'BLZ', 84, 'Belize'),
(24, 'AF', 'BJ', 'BEN', 204, 'Benin, Republic of'),
(25, 'NA', 'BM', 'BMU', 60, 'Bermuda, Bermuda Islands'),
(26, 'AS', 'BT', 'BTN', 64, 'Bhutan, Kingdom of'),
(27, 'SA', 'BO', 'BOL', 68, 'Bolivia, Plurinational State of Bolivia'),
(28, 'EU', 'BA', 'BIH', 70, 'Bosnia and Herzegovina'),
(29, 'AF', 'BW', 'BWA', 72, 'Botswana, Republic of'),
(30, 'AN', 'BV', 'BVT', 74, 'Bouvet Island'),
(31, 'SA', 'BR', 'BRA', 76, 'Brazil, Federative Republic of'),
(32, 'AS', 'IO', 'IOT', 86, 'British Indian Ocean Territory'),
(33, 'NA', 'VG', 'VGB', 92, 'Virgin Islands (British), British Virgin Islands'),
(34, 'AS', 'BN', 'BRN', 96, 'Brunei Darussalam'),
(35, 'EU', 'BG', 'BGR', 100, 'Bulgaria, Republic of'),
(36, 'AF', 'BF', 'BFA', 854, 'Burkina Faso'),
(37, 'AF', 'BI', 'BDI', 108, 'Burundi, Republic of'),
(38, 'AS', 'KH', 'KHM', 116, 'Cambodia, Kingdom of'),
(39, 'AF', 'CM', 'CMR', 120, 'Cameroon, Republic of'),
(40, 'NA', 'CA', 'CAN', 124, 'Canada'),
(41, 'AF', 'CV', 'CPV', 132, 'Cape Verde, Republic of'),
(42, 'NA', 'KY', 'CYM', 136, 'Cayman Islands, The'),
(43, 'AF', 'CF', 'CAF', 140, 'Central African Republic'),
(44, 'AF', 'TD', 'TCD', 148, 'Chad, Republic of'),
(45, 'SA', 'CL', 'CHL', 152, 'Chile, Republic of'),
(46, 'AS', 'CN', 'CHN', 156, 'China, People''s Republic of'),
(47, 'AS', 'CX', 'CXR', 162, 'Christmas Island'),
(48, 'AS', 'CC', 'CCK', 166, 'Cocos (Keeling) Islands'),
(49, 'SA', 'CO', 'COL', 170, 'Colombia, Republic of'),
(50, 'AF', 'KM', 'COM', 174, 'Comoros, Union of the'),
(51, 'AF', 'CD', 'COD', 180, 'Congo, Democratic Republic of the'),
(52, 'AF', 'CG', 'COG', 178, 'Congo, Republic of the'),
(53, 'OC', 'CK', 'COK', 184, 'Cook Islands'),
(54, 'NA', 'CR', 'CRI', 188, 'Costa Rica, Republic of'),
(55, 'AF', 'CI', 'CIV', 384, 'Cote d''Ivoire (Ivory Coast), Republic of'),
(56, 'EU', 'HR', 'HRV', 191, 'Croatia, Republic of'),
(57, 'NA', 'CU', 'CUB', 192, 'Cuba, Republic of'),
(58, 'AS', 'CY', 'CYP', 196, 'Cyprus, Republic of'),
(59, 'EU', 'CZ', 'CZE', 203, 'Czech Republic'),
(60, 'EU', 'DK', 'DNK', 208, 'Denmark, Kingdom of'),
(61, 'AF', 'DJ', 'DJI', 262, 'Djibouti, Republic of'),
(62, 'NA', 'DM', 'DMA', 212, 'Dominica, Commonwealth of'),
(63, 'NA', 'DO', 'DOM', 214, 'Dominican Republic'),
(64, 'SA', 'EC', 'ECU', 218, 'Ecuador, Republic of'),
(65, 'AF', 'EG', 'EGY', 818, 'Egypt, Arab Republic of'),
(66, 'NA', 'SV', 'SLV', 222, 'El Salvador, Republic of'),
(67, 'AF', 'GQ', 'GNQ', 226, 'Equatorial Guinea, Republic of'),
(68, 'AF', 'ER', 'ERI', 232, 'Eritrea, State of'),
(69, 'EU', 'EE', 'EST', 233, 'Estonia, Republic of'),
(70, 'AF', 'ET', 'ETH', 231, 'Ethiopia, Federal Democratic Republic of'),
(71, 'EU', 'FO', 'FRO', 234, 'Faroe Islands, The'),
(72, 'SA', 'FK', 'FLK', 238, 'Falkland Islands (Malvinas), The'),
(73, 'OC', 'FJ', 'FJI', 242, 'Fiji, Republic of the Fiji'),
(74, 'EU', 'FI', 'FIN', 246, 'Finland, Republic of'),
(75, 'EU', 'FR', 'FRA', 250, 'France, French Republic'),
(76, 'SA', 'GF', 'GUF', 254, 'French Guiana'),
(77, 'OC', 'PF', 'PYF', 258, 'French Polynesia'),
(78, 'AN', 'TF', 'ATF', 260, 'French Southern Territories'),
(79, 'AF', 'GA', 'GAB', 266, 'Gabon, Gabonese Republic'),
(80, 'AF', 'GM', 'GMB', 270, 'Gambia, Republic of The'),
(81, 'AS', 'GE', 'GEO', 268, 'Georgia'),
(82, 'EU', 'DE', 'DEU', 276, 'Germany, Federal Republic of'),
(83, 'AF', 'GH', 'GHA', 288, 'Ghana, Republic of'),
(84, 'EU', 'GI', 'GIB', 292, 'Gibraltar'),
(85, 'EU', 'GR', 'GRC', 300, 'Greece, Hellenic Republic'),
(86, 'NA', 'GL', 'GRL', 304, 'Greenland'),
(87, 'NA', 'GD', 'GRD', 308, 'Grenada'),
(88, 'NA', 'GP', 'GLP', 312, 'Guadeloupe'),
(89, 'OC', 'GU', 'GUM', 316, 'Guam'),
(90, 'NA', 'GT', 'GTM', 320, 'Guatemala, Republic of'),
(91, 'EU', 'GG', 'GGY', 831, 'Guernsey'),
(92, 'AF', 'GN', 'GIN', 324, 'Guinea, Republic of'),
(93, 'AF', 'GW', 'GNB', 624, 'Guinea-Bissau, Republic of'),
(94, 'SA', 'GY', 'GUY', 328, 'Guyana, Co-operative Republic of'),
(95, 'NA', 'HT', 'HTI', 332, 'Haiti, Republic of'),
(96, 'AN', 'HM', 'HMD', 334, 'Heard Island and McDonald Isla'),
(97, 'EU', 'VA', 'VAT', 336, 'Vatican City, State of the Vatican City'),
(98, 'NA', 'HN', 'HND', 340, 'Honduras, Republic of'),
(99, 'AS', 'HK', 'HKG', 344, 'Hong Kong'),
(100, 'EU', 'HU', 'HUN', 348, 'Hungary'),
(101, 'EU', 'IS', 'ISL', 352, 'Iceland, Republic of'),
(102, 'AS', 'IN', 'IND', 356, 'India, Republic of'),
(103, 'AS', 'ID', 'IDN', 360, 'Indonesia, Republic of'),
(104, 'AS', 'IR', 'IRN', 364, 'Iran, Islamic Republic of'),
(105, 'AS', 'IQ', 'IRQ', 368, 'Iraq, Republic of'),
(106, 'EU', 'IE', 'IRL', 372, 'Ireland'),
(107, 'EU', 'IM', 'IMN', 833, 'Isle of Man'),
(108, 'AS', 'IL', 'ISR', 376, 'Israel, State of'),
(109, 'EU', 'IT', 'ITA', 380, 'Italy, Italian Republic'),
(110, 'NA', 'JM', 'JAM', 388, 'Jamaica'),
(111, 'AS', 'JP', 'JPN', 392, 'Japan'),
(112, 'EU', 'JE', 'JEY', 832, 'Jersey, Bailiwick of'),
(113, 'AS', 'JO', 'JOR', 400, 'Jordan, Hashemite Kingdom of'),
(114, 'AS', 'KZ', 'KAZ', 398, 'Kazakhstan, Republic of'),
(115, 'AF', 'KE', 'KEN', 404, 'Kenya, Republic of'),
(116, 'OC', 'KI', 'KIR', 296, 'Kiribati, Republic of'),
(117, 'AS', 'KP', 'PRK', 408, 'North Korea, Democratic People''s Republic of Korea'),
(118, 'AS', 'KR', 'KOR', 410, 'South Korea, Republic of Korea'),
(119, 'AS', 'KW', 'KWT', 414, 'Kuwait, State of'),
(120, 'AS', 'KG', 'KGZ', 417, 'Kyrgyzstan, Kyrgyz Republic'),
(121, 'AS', 'LA', 'LAO', 418, 'Laos, Lao People''s Democratic Republic'),
(122, 'EU', 'LV', 'LVA', 428, 'Latvia, Republic of'),
(123, 'AS', 'LB', 'LBN', 422, 'Lebanon, Republic of'),
(124, 'AF', 'LS', 'LSO', 426, 'Lesotho, Kingdom of'),
(125, 'AF', 'LR', 'LBR', 430, 'Liberia, Republic of'),
(126, 'AF', 'LY', 'LBY', 434, 'Libya'),
(127, 'EU', 'LI', 'LIE', 438, 'Liechtenstein, Principality of'),
(128, 'EU', 'LT', 'LTU', 440, 'Lithuania, Republic of'),
(129, 'EU', 'LU', 'LUX', 442, 'Luxembourg, Grand Duchy of'),
(130, 'AS', 'MO', 'MAC', 446, 'Macao, The Macao Special Administrative Region'),
(131, 'EU', 'MK', 'MKD', 807, 'Macedonia, The Former Yugoslav Republic of'),
(132, 'AF', 'MG', 'MDG', 450, 'Madagascar, Republic of'),
(133, 'AF', 'MW', 'MWI', 454, 'Malawi, Republic of'),
(134, 'AS', 'MY', 'MYS', 458, 'Malaysia'),
(135, 'AS', 'MV', 'MDV', 462, 'Maldives, Republic of'),
(136, 'AF', 'ML', 'MLI', 466, 'Mali, Republic of'),
(137, 'EU', 'MT', 'MLT', 470, 'Malta, Republic of'),
(138, 'OC', 'MH', 'MHL', 584, 'Marshall Islands, Republic of the'),
(139, 'NA', 'MQ', 'MTQ', 474, 'Martinique'),
(140, 'AF', 'MR', 'MRT', 478, 'Mauritania, Islamic Republic of'),
(141, 'AF', 'MU', 'MUS', 480, 'Mauritius, Republic of'),
(142, 'AF', 'YT', 'MYT', 175, 'Mayotte'),
(143, 'NA', 'MX', 'MEX', 484, 'Mexico, United Mexican States'),
(144, 'OC', 'FM', 'FSM', 583, 'Micronesia, Federated States of'),
(145, 'EU', 'MD', 'MDA', 498, 'Moldova, Republic of'),
(146, 'EU', 'MC', 'MCO', 492, 'Monaco, Principality of'),
(147, 'AS', 'MN', 'MNG', 496, 'Mongolia'),
(148, 'EU', 'ME', 'MNE', 499, 'Montenegro'),
(149, 'NA', 'MS', 'MSR', 500, 'Montserrat'),
(150, 'AF', 'MA', 'MAR', 504, 'Morocco, Kingdom of'),
(151, 'AF', 'MZ', 'MOZ', 508, 'Mozambique, Republic of'),
(152, 'AS', 'MM', 'MMR', 104, 'Myanmar (Burma), Republic of the Union of Myanmar'),
(153, 'AF', 'NA', 'NAM', 516, 'Namibia, Republic of'),
(154, 'OC', 'NR', 'NRU', 520, 'Nauru, Republic of'),
(155, 'AS', 'NP', 'NPL', 524, 'Nepal, Federal Democratic Republic of'),
(157, 'EU', 'NL', 'NLD', 528, 'Netherlands, Kingdom of the'),
(158, 'OC', 'NC', 'NCL', 540, 'New Caledonia'),
(159, 'OC', 'NZ', 'NZL', 554, 'New Zealand'),
(160, 'NA', 'NI', 'NIC', 558, 'Nicaragua, Republic of'),
(161, 'AF', 'NE', 'NER', 562, 'Niger, Republic of'),
(162, 'AF', 'NG', 'NGA', 566, 'Nigeria, Federal Republic of'),
(163, 'OC', 'NU', 'NIU', 570, 'Niue'),
(164, 'OC', 'NF', 'NFK', 574, 'Norfolk Island'),
(165, 'OC', 'MP', 'MNP', 580, 'Northern Mariana Islands'),
(166, 'EU', 'NO', 'NOR', 578, 'Norway, Kingdom of'),
(167, 'AS', 'OM', 'OMN', 512, 'Oman, Sultanate of'),
(168, 'AS', 'PK', 'PAK', 586, 'Pakistan, Islamic Republic of'),
(169, 'OC', 'PW', 'PLW', 585, 'Palau, Republic of'),
(170, 'AS', 'PS', 'PSE', 275, 'Palestine, State of Palestine (or Occupied Palestinian Territory)'),
(171, 'NA', 'PA', 'PAN', 591, 'Panama, Republic of'),
(172, 'OC', 'PG', 'PNG', 598, 'Papua New Guinea, Independent State of'),
(173, 'SA', 'PY', 'PRY', 600, 'Paraguay, Republic of'),
(174, 'SA', 'PE', 'PER', 604, 'Peru, Republic of'),
(175, 'AS', 'PH', 'PHL', 608, 'Philippines, Republic of the'),
(176, 'OC', 'PN', 'PCN', 612, 'Pitcairn'),
(177, 'EU', 'PL', 'POL', 616, 'Poland, Republic of'),
(178, 'EU', 'PT', 'PRT', 620, 'Portugal, Portuguese Republic'),
(179, 'NA', 'PR', 'PRI', 630, 'Puerto Rico, Commonwealth of'),
(180, 'AS', 'QA', 'QAT', 634, 'Qatar, State of'),
(181, 'AF', 'RE', 'REU', 638, 'Reunion'),
(182, 'EU', 'RO', 'ROU', 642, 'Romania'),
(183, 'EU', 'RU', 'RUS', 643, 'Russia, Russian Federation'),
(184, 'AF', 'RW', 'RWA', 646, 'Rwanda, Republic of'),
(185, 'NA', 'BL', 'BLM', 652, 'Saint Barthelemy'),
(186, 'AF', 'SH', 'SHN', 654, 'Saint Helena, Ascension and Tristan da Cunha'),
(187, 'NA', 'KN', 'KNA', 659, 'Saint Kitts and Nevis, Federation of Saint Christopher and Nevis'),
(188, 'NA', 'LC', 'LCA', 662, 'Saint Lucia'),
(189, 'NA', 'MF', 'MAF', 663, 'Saint Martin'),
(190, 'NA', 'PM', 'SPM', 666, 'Saint Pierre and Miquelon'),
(191, 'NA', 'VC', 'VCT', 670, 'Saint Vincent and the Grenadines'),
(192, 'OC', 'WS', 'WSM', 882, 'Samoa, Independent State of'),
(193, 'EU', 'SM', 'SMR', 674, 'San Marino, Republic of'),
(194, 'AF', 'ST', 'STP', 678, 'Sao Tome and Principe, Democratic Republic of'),
(195, 'AS', 'SA', 'SAU', 682, 'Saudi Arabia, Kingdom of'),
(196, 'AF', 'SN', 'SEN', 686, 'Senegal, Republic of'),
(197, 'EU', 'RS', 'SRB', 688, 'Serbia, Republic of'),
(198, 'AF', 'SC', 'SYC', 690, 'Seychelles, Republic of'),
(199, 'AF', 'SL', 'SLE', 694, 'Sierra Leone, Republic of'),
(200, 'AS', 'SG', 'SGP', 702, 'Singapore, Republic of'),
(201, 'EU', 'SK', 'SVK', 703, 'Slovakia, Slovak Republic'),
(202, 'EU', 'SI', 'SVN', 705, 'Slovenia, Republic of'),
(203, 'OC', 'SB', 'SLB', 90, 'Solomon Islands'),
(204, 'AF', 'SO', 'SOM', 706, 'Somalia, Somali Republic'),
(205, 'AF', 'ZA', 'ZAF', 710, 'South Africa, Republic of'),
(206, 'AN', 'GS', 'SGS', 239, 'South Georgia and the South Sandwich Islands'),
(207, 'EU', 'ES', 'ESP', 724, 'Spain, Kingdom of'),
(208, 'AS', 'LK', 'LKA', 144, 'Sri Lanka, Democratic Socialist Republic of'),
(209, 'AF', 'SD', 'SDN', 736, 'Sudan, Republic of the'),
(210, 'SA', 'SR', 'SUR', 740, 'Suriname, Republic of'),
(211, 'EU', 'SJ', 'SJM', 744, 'Svalbard and Jan Mayen'),
(212, 'AF', 'SZ', 'SWZ', 748, 'Swaziland, Kingdom of'),
(213, 'EU', 'SE', 'SWE', 752, 'Sweden, Kingdom of'),
(214, 'EU', 'CH', 'CHE', 756, 'Switzerland, Swiss Confederation'),
(215, 'AS', 'SY', 'SYR', 760, 'Syria, Syrian Arab Republic'),
(216, 'AS', 'TW', 'TWN', 158, 'Taiwan, Republic of China (Taiwan)'),
(217, 'AS', 'TJ', 'TJK', 762, 'Tajikistan, Republic of'),
(218, 'AF', 'TZ', 'TZA', 834, 'Tanzania, United Republic of'),
(219, 'AS', 'TH', 'THA', 764, 'Thailand, Kingdom of'),
(220, 'AS', 'TL', 'TLS', 626, 'Timor-Leste (East Timor), Democratic Republic of'),
(221, 'AF', 'TG', 'TGO', 768, 'Togo, Togolese Republic'),
(222, 'OC', 'TK', 'TKL', 772, 'Tokelau'),
(223, 'OC', 'TO', 'TON', 776, 'Tonga, Kingdom of'),
(224, 'NA', 'TT', 'TTO', 780, 'Trinidad and Tobago, Republic '),
(225, 'AF', 'TN', 'TUN', 788, 'Tunisia, Republic of'),
(226, 'AS', 'TR', 'TUR', 792, 'Turkey, Republic of'),
(227, 'AS', 'TM', 'TKM', 795, 'Turkmenistan'),
(228, 'NA', 'TC', 'TCA', 796, 'Turks and Caicos Islands'),
(229, 'OC', 'TV', 'TUV', 798, 'Tuvalu'),
(230, 'AF', 'UG', 'UGA', 800, 'Uganda, Republic of'),
(231, 'EU', 'UA', 'UKR', 804, 'Ukraine'),
(232, 'AS', 'AE', 'ARE', 784, 'United Arab Emirates'),
(233, 'EU', 'GB', 'GBR', 826, 'United Kingdom, United Kingdom of Great Britain and Northern Ireland'),
(234, 'NA', 'US', 'USA', 840, 'United States, United States of America'),
(235, 'OC', 'UM', 'UMI', 581, 'United States Minor Outlying Islands'),
(236, 'NA', 'VI', 'VIR', 850, 'Virgin Islands (US), Virgin Islands of the United States'),
(237, 'SA', 'UY', 'URY', 858, 'Uruguay, Eastern Republic of'),
(238, 'AS', 'UZ', 'UZB', 860, 'Uzbekistan, Republic of'),
(239, 'OC', 'VU', 'VUT', 548, 'Vanuatu, Republic of'),
(240, 'SA', 'VE', 'VEN', 862, 'Venezuela, Bolivarian Republic'),
(241, 'AS', 'VN', 'VNM', 704, 'Vietnam, Socialist Republic of'),
(242, 'OC', 'WF', 'WLF', 876, 'Wallis and Futuna'),
(243, 'AF', 'EH', 'ESH', 732, 'Western Sahara'),
(244, 'AS', 'YE', 'YEM', 887, 'Yemen, Republic of'),
(245, 'AF', 'ZM', 'ZMB', 894, 'Zambia, Republic of'),
(246, 'AF', 'ZW', 'ZWE', 716, 'Zimbabwe, Republic of'),
(247, 'NA', 'BQ', 'BES', 535, 'Bonaire, Sint Eustatius and Saba'),
(248, 'NA', 'CW', 'CUW', 531, 'Curacao'),
(249, 'NA', 'SX', 'SXM', 534, 'Sint Maarten'),
(250, 'AF', 'SS', 'SSD', 728, 'South Sudan, Republic of'),
(251, 'EU', 'XK', 'XKX', '',  'Kosovo, Republic of');

INSERT IGNORE INTO `#__jem_categories` (`id`, `parent_id`, `lft`, `rgt`, `level`, `catname`, `alias`, `access`, `published`) VALUES
(1, 0, 0, 1, 0, 'root', 'root', 1, 1);