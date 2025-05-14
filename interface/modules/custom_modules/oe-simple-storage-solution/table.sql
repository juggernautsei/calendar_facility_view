#IfNotTable module-s3-credentials
CREATE TABLE IF NOT EXISTS module_s3_credentials (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    region TEXT NULL,
    s3_key TEXT NULL,
    s3_secret TEXT NULL,
    s3_region TEXT NULL
)Engine=InnoDB;
#EndIfTable
