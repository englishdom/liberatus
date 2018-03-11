CREATE TABLE user_tokens
(
  user_token_id INT(10) AUTO_INCREMENT
    PRIMARY KEY,
  refresh_token VARCHAR(40) DEFAULT ''             NOT NULL,
  client_id     VARCHAR(32) DEFAULT ''             NOT NULL,
  dt_created    DATETIME DEFAULT CURRENT_TIMESTAMP NULL,
  user_id       INT(10)                            NOT NULL,
  CONSTRAINT refresh_token_uindex
  UNIQUE (refresh_token)
)
  ENGINE = InnoDB;

CREATE INDEX cliend_id_key
  ON user_tokens (client_id);

CREATE INDEX user_id_key
  ON user_tokens (user_id);