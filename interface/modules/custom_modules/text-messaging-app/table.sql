
#IfNotTable text_message_module
CREATE TABLE IF NOT EXISTS `text_message_module` (
`id` int NOT NULL,
`fromnumber` varchar(15) DEFAULT NULL,
`text` varchar(255) NOT NULL,
`date` datetime(6) NOT NULL
);

ALTER TABLE `text_message_module` ADD PRIMARY KEY(`id`);
ALTER TABLE `text_message_module` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `text_message_module` ADD `provider_id` VARCHAR(25) NULL AFTER `id`;

#EndIf

#IfNotTable text_notification_log
CREATE TABLE IF NOT EXISTS `text_notification_log` (
`iLogId` int(11) NOT NULL,
`pid` bigint(20) NOT NULL,
`pc_eid` int(11) UNSIGNED DEFAULT NULL,
`sms_gateway_type` varchar(50)  NOT NULL,
`smsgateway_info` varchar(255)  NOT NULL,
`message` text NOT NULL,
`email_sender` varchar(255) NOT NULL,
`email_subject` varchar(255) NOT NULL,
`type` enum('SMS','Email') NOT NULL,
`patient_info` text NOT NULL,
`pc_eventDate` date NOT NULL,
`pc_endDate` date NOT NULL,
`pc_startTime` time NOT NULL,
`pc_endTime` time NOT NULL,
`dSentDateTime` datetime NOT NULL
);
ALTER TABLE `text_notification_log` ADD PRIMARY KEY(`iLogId`);
ALTER TABLE `text_notification_log` CHANGE `iLogId` `iLogId` INT NOT NULL AUTO_INCREMENT;
#EndIf

#IfNotRow2D background_services value SMS_REMINDERS
INSERT INTO `background_services` (`name`, `title`, `active`, `running`, `next_run`, `execute_interval`, `function`, `require_once`, `sort_order`) VALUES
    ('SMS_REMINDERS', 'SMS Appointment Reminders', 0, 0, '2022-01-18 08:25:00', 1440, 'start_sms_reminders', '/interface/modules/custom_modules/text-messaging-app/lib/sms_appointment.php', 100);
#EndIf

#IfNotRow2D background_services value Provider_Reminders
INSERT INTO `background_services` (`name`, `title`, `active`, `running`, `next_run`, `execute_interval`, `function`, `require_once`, `sort_order`) VALUES
    ('Provider_Reminders', 'Provider daily schedule', '1', '0', '2022-08-15 08:00:00', '1440', 'start_appt_reminders', '/interface/modules/custom_modules/text-messaging-app/public/provider_appt_notification.php', '100');
#EndIf

#IfNotTable text_notification_messages
CREATE TABLE IF NOT EXISTS `text_notification_messages` (
    `id` int(5) NOT NULL,
    `cdr_category` int(3) NULL,
    `language` varchar(10) NULL,
    `message_content` varchar(255) NULL
);

ALTER TABLE `text_notification_messages` ADD PRIMARY KEY(`id`);
ALTER TABLE `text_notification_messages` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT;
#EndIf

#IfNotTable text_settings
CREATE TABLE IF NOT EXISTS `text_settings` (
    `setting_id` int(3) NOT NULL,
    `settings_type` varchar(45) NOT NULL,
    `settings` text NULL
);
ALTER TABLE `text_settings` ADD PRIMARY KEY(`setting_id`);
ALTER TABLE `text_settings` CHANGE `setting_id` `setting_id` INT NOT NULL AUTO_INCREMENT;
#EndIf
