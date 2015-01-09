ALTER TABLE `#__jem_settings`
  	ADD `veditevent` text NOT NULL DEFAULT '';
  	
ALTER TABLE `#__jem_venues`
	ADD `phone` VARCHAR(100) NOT NULL DEFAULT '',
	ADD `fax` VARCHAR(100) NOT NULL DEFAULT '',
	ADD `email` VARCHAR(100) NOT NULL DEFAULT '';
	
UPDATE `#__jem_settings`
	SET version = '3.0.6';