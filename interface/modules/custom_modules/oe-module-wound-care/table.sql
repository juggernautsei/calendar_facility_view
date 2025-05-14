#IfNotTable module_wound_care_settings
CREATE TABLE `module_wound_care_settings` (
     `id` INT AUTO_INCREMENT PRIMARY KEY,
     `setting_type` VARCHAR(255) NOT NULL,
     `settings_json` VARCHAR(255) NOT NULL,
     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
#EndIf

#IfNotTable wound_assessments
CREATE TABLE wound_assessments (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `pid` BIGINT NOT NULL,
    `user_id` BIGINT NOT NULL,
    `encounter` BIGINT NOT NULL,
    `wound_id` BIGINT NOT NULL,
    `wound_location` VARCHAR(255) NOT NULL,
    `wound_size` VARCHAR(50) NOT NULL,
    `exudate` VARCHAR(50) NOT NULL,
    `wound_type` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
#EndIf

INSERT INTO `registry` (`name`, `state`, `directory`, `id`, `sql_run`, `unpackaged`, `date`, `priority`, `category`, `nickname`, `patient_encounter`, `therapy_group_encounter`, `aco_spec`, `form_foreign_id`) VALUES
    ('Wound Care AI Assistant',
     1,
     'assistant',
     310,
     1,
     1,
     CURDATE(),
     0,
     'Clinical',
     'Jarvis',
     1,
     0,
     'encounters|notes',
     NULL
    );
