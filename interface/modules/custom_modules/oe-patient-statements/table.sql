#IfNotRow2D background_services value BALANCE_REMINDERS
INSERT INTO `background_services` (`name`, `title`, `active`, `running`, `next_run`, `execute_interval`, `function`, `require_once`, `sort_order`) VALUES
    ('BALANCE_REMINDERS', 'Patient Balance Reminders', 0, 0, '2022-01-18 08:25:00', 1440, 'sendPatientBalance', '/interface/modules/custom_modules/oe-patient-statements/public/monthly_statements.php', 100) ON DUPLICATE KEY UPDATE `active` = 0, `running` = 0, `next_run` = '2022-01-18 08:25:00', `execute_interval` = 1440, `function` = 'sendPatientBalance', `require_once` = '/interface/modules/custom_modules/oe-patient-statements/public/monthly_statements.php', `sort_order` = 100;
#EndIf

#IfNotTable module_patient_statement_settings
CREATE TABLE `module_patient_statement_settings`
(
    `id`        INT(10)      NOT NULL AUTO_INCREMENT,
    `statement` VARCHAR(255) NOT NULL,
    `date`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user`      VARCHAR(10)      NOT NULL,
    PRIMARY KEY (`id`)
)ENGINE = InnoDB;
#EndIf

#IfNotTable module_patient_statement_log
CREATE TABLE `module_patient_statement_log`
(
    `id`        INT(10)      NOT NULL AUTO_INCREMENT,
    `statement` VARCHAR(255) NOT NULL,
    `date`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user`      VARCHAR(10)      NOT NULL,
    PRIMARY KEY (`id`)
)ENGINE = InnoDB;
#EndIf
