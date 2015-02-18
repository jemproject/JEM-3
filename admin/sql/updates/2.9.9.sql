ALTER TABLE `#__jem_events`
	CHANGE recurrence_number recurrence_interval int(2) NOT NULL default '0',
	ADD `recurrence_group` varchar(20) NOT NULL,
	ADD `recurrence_until` varchar(20) NOT NULL,
	ADD `recurrence_freq` varchar(20) NOT NULL,
	ADD `wholeday` varchar(2) NOT NULL;
	
ALTER TABLE `#__jem_venues`
	ADD `timezone` varchar(100) NOT NULL DEFAULT '';

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


ALTER TABLE `#__jem_settings`
	DROP `ical_tz`,
	DROP `datemode`,
	ADD `version` varchar (20) NOT NULL DEFAULT '3.0.6';







