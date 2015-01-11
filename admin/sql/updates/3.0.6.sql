ALTER TABLE `#__jem_events` 
	ADD `mailing` TEXT NOT NULL ,
	ADD `opendate` INT NOT NULL DEFAULT '0';

ALTER TABLE `#__jem_settings`
  	ADD `veditevent` text NOT NULL DEFAULT '';
  	
ALTER TABLE `#__jem_venues`
	ADD `phone` VARCHAR(100) NOT NULL DEFAULT '',
	ADD `fax` VARCHAR(100) NOT NULL DEFAULT '',
	ADD `email` VARCHAR(100) NOT NULL DEFAULT '';