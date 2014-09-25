--
-- Update version number
--

UPDATE #__jem_settings
SET `version`='3.0.3'
WHERE `version`='3.0.1';


ALTER TABLE `#__jem_settings`
	DROP `datdesclimit`,
	DROP `discatheader`,
	DROP `showtime`,
	DROP `showtimedetails`,
	ADD `vevent` text NOT NULL DEFAULT '',
  	ADD `vvenue` text NOT NULL DEFAULT '',
  	ADD `vvenues` text NOT NULL DEFAULT '',
  	ADD `vcategories` text NOT NULL DEFAULT '',
  	ADD `vcategory` text NOT NULL DEFAULT '';
	
ALTER TABLE `#__jem_events`
	ADD startDateTime varchar(100) NOT NULL DEFAULT '',
	ADD endDateTime varchar(100) NOT NULL DEFAULT '',
	ADD note varchar(100) NOT NULL DEFAULT '';