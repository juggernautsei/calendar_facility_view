#IfNotTable module_dexcom_credentials
CREATE TABLE IF NOT EXISTS `module_dexcom_credentials`
(
    `pid` int(10) UNSIGNED NOT NULL,
    `refresh_token` varchar(255) DEFAULT NULL,
    `date_created` datetime DEFAULT NULL,
    `date_modified` datetime DEFAULT NULL
);
#EndIf

#IfNotTable module_dexcom_credentials
CREATE TABLE IF NOT EXISTS `module_dexcom_daily_readings`
(
    `id`          int(10) UNSIGNED NOT NULL,
    `pid`         int(10) UNSIGNED NOT NULL,
    `readings`    TEXT NOT NULL
);
ALTER TABLE `module_dexcom_daily_readings` ADD PRIMARY KEY (`id`);
ALTER TABLE `module_dexcom_daily_readings` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
#EndIf
