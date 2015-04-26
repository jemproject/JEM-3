ALTER TABLE `#__jem_categories` 
	ADD `asset_id` INT( 10 ) UNSIGNED NOT NULL,
	CHANGE `groupid` `groupid` VARCHAR(100) NOT NULL DEFAULT '0';
;
ALTER TABLE `#__jem_venues` ADD `asset_id` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `#__jem_events` 
	ADD `registering` TEXT NOT NULL,
	ADD `asset_id` INT( 10 ) UNSIGNED NOT NULL
 ;
ALTER TABLE `#__jem_settings`
	DROP `weekdaystart`;