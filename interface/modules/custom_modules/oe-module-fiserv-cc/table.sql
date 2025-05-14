CREATE TABLE IF NOT EXISTS `module_fiserv_cc_credentials` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `api_key` varchar(85) DEFAULT NULL,
    `api_secret` varchar(85) DEFAULT NULL,
    `api_name` varchar(63) DEFAULT NULL,
    `host_url` varchar(85) DEFAULT NULL,
    `client_id` varchar(85) DEFAULT NULL,
    `created` datetime NOT NULL DEFAULT current_timestamp(),
    `updated` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='Vendor credentials for Clover';
