DROP DATABASE IF EXISTS declaration;
CREATE DATABASE declaration;

use declaration;

DROP TABLE IF EXISTS export;
CREATE TABLE export (
  id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  trace_id VARCHAR(50) NOT NULL,
  xml TEXT,
  status TINYINT(1) DEFAULT 0,
  return_info VARCHAR(100) DEFAULT '',
  return_status TINYINT(3) DEFAULT 0,
  return_xml TEXT,
  create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
