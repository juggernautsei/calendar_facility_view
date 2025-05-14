#IfNotTable module_aws_healthscribe_settings
CREATE TABLE `module_aws_healthscribe_settings` (
     `id` INT AUTO_INCREMENT PRIMARY KEY,
     `access_key` VARCHAR(255) NOT NULL,
     `secret_key` VARCHAR(255) NOT NULL,
     `region` VARCHAR(50) NOT NULL,
     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
#EndIf
