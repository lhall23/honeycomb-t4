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
	enabled		boolean DEFAULT false,
    quota       integer DEFAULT 0
);      

COMMENT ON COLUMN users.password IS 'sha1 hash of password';
COMMENT ON COLUMN users.auth_hash IS 
    'onetime key used for registration and lost passwords';
COMMENT ON COLUMN users.enabled IS 'Has user been verified?';

DROP TABLE IF EXISTS files CASCADE; 
CREATE TABLE files (
    file_id     SERIAL PRIMARY KEY,
    user_id     INTEGER NOT NULL 
        REFERENCES users(user_id) ON DELETE CASCADE, 
    file_name   varchar,
    location    varchar UNIQUE
);

DROP TABLE IF EXISTS groups CASCADE;    
CREATE TABLE groups (
    group_id    SERIAL PRIMARY KEY,
	owner_id		INTEGER NOT NULL
        REFERENCES users(user_id) ON DELETE CASCADE, 
    group_name  varchar UNIQUE
);
COMMENT ON COLUMN groups.owner_id IS 'Group creator/owner';

DROP TABLE IF EXISTS group_files;   
CREATE TABLE group_files (
    group_id    INTEGER NOT NULL
        REFERENCES groups(group_id) ON DELETE CASCADE,
    file_id     INTEGER NOT NULL
        REFERENCES files(file_id) ON DELETE CASCADE,
    UNIQUE(group_id,file_id)
);
COMMENT ON TABLE group_files IS 'join table for files and groups';

DROP TABLE IF EXISTS group_members; 
CREATE TABLE group_members (
    group_id    INTEGER NOT NULL
        REFERENCES groups(group_id) ON DELETE CASCADE,
    user_id     INTEGER NOT NULL
        REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE(group_id,user_id)
);
COMMENT ON TABLE group_members IS 'join table for users and groups';

INSERT INTO USERS(user_name,password,email,enabled,quota) 
    VALUES ('test', md5('test'), 'test@test.com',true,10*1024*1024);

INSERT INTO USERS(user_name,password,email,enabled,quota) 
    VALUES ('admin', md5('admin') , 'admin@test.com',true,10*1024*1024);
COMMIT;


