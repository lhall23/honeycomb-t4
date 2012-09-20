/*
 * honeycomb.sql 
 * -Lee Hall Wed 05 Sep 2012 10:55:06 PM EDT
 */

SET ROLE honeycomb;
BEGIN;

DROP TABLE IF EXISTS users CASCADE; 
CREATE TABLE users (
    user_id     SERIAL PRIMARY KEY,
    user_name   varchar UNIQUE NOT NULL,
    password    varchar, 
    auth_hash   varchar UNIQUE,  
    email       varchar UNIQUE NOT NULL,
    first_name  varchar,
    last_name   varchar,
    quota       integer DEFAULT 0
);      

COMMENT ON COLUMN users.password IS 'sha1 hash of password';
COMMENT ON COLUMN users.auth_hash IS 
    'onetime key used for registration and lost passwords';
COMMIT;


