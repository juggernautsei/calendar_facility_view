
CREATE TABLE IF NOT EXISTS `module_mass_email` (
     `id` int NOT NULL,
     `message` text NOT NULL,
     `username` varchar(255) NOT NULL,
     `date_created` date NOT NULL
) ENGINE=InnoDB;

#IfMissingColumn module_mass_email id
ALTER TABLE `module_mass_email` ADD PRIMARY KEY(`id`);
ALTER TABLE `module_mass_email` CHANGE `id` `id` INT(5) NOT NULL AUTO_INCREMENT;
ALTER TABLE `module_mass_email` CHANGE `date_created` `date_created` DATETIME NOT NULL;
#EndIF
-- -----------------------------------------------------
INSERT INTO `background_services` (`name`, `title`, `active`, `running`, `next_run`, `execute_interval`, `function`, `require_once`, `sort_order`) VALUES
    ('Patient Mass Email', 'Send out notifications', '1', '0', '2022-08-15 07:00:00', '1440', 'start_mass_email', '/interface/modules/custom_modules/oe-patient-communication/public/send_email_notification.php', '100');

