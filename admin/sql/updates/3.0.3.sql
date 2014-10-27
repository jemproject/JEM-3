ALTER TABLE `#__jem_settings`
	DROP `datdesclimit`,
	DROP `discatheader`,
	DROP `showtime`,
	DROP `showtimedetails`,
	DROP `tld`,
	DROP `lg`,
	DROP `show_print_icon`,
	DROP `show_archive_icon`,
	DROP `comunsolution`,
	DROP `comunoption`,
	DROP `showdetailsadress`,
	DROP `showlocdescription`,
	DROP `showdetlinkvenue`,
	DROP `icons`,
	DROP `show_email_icon`,
	DROP `filter`,
	DROP `showdetailstitle`,
	DROP `showevdescription`,
	DROP `showstate`,
	DROP `display`,
	ADD `vevent` text NOT NULL DEFAULT '',
  	ADD `vvenue` text NOT NULL DEFAULT '',
  	ADD `vvenues` text NOT NULL DEFAULT '',
  	ADD `vcalendar` text NOT NULL DEFAULT '',
  	ADD `vcategories` text NOT NULL DEFAULT '',
  	ADD `vcategory` text NOT NULL DEFAULT '';
	
ALTER TABLE `#__jem_events`
	ADD startDateTime varchar(100) NOT NULL DEFAULT '',
	ADD endDateTime varchar(100) NOT NULL DEFAULT '',
	ADD note varchar(100) NOT NULL DEFAULT '',
	CHANGE `language` `language` CHAR( 7 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '*';
	