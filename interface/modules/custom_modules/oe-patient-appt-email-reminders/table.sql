
INSERT INTO `background_services` (`name`, `title`, `active`, `running`, `next_run`, `execute_interval`, `function`, `require_once`, `sort_order`) VALUES
    ('EMAIL_REMINDERS', 'Email Appointment Reminders', 0, 0, '2022-01-18 08:30:00', 1440, 'start_email_reminders', '/interface/modules/custom_modules/oe-patient-appt-email-reminders/public/email_appointment_service.php', 100);
