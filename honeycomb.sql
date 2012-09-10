/*
 * honeycomb.sql 
 * -Lee Hall Wed 05 Sep 2012 10:55:06 PM EDT
 */

SET ROLE honeycomb;
BEGIN;

DROP TABLE IF EXISTS users CASCADE;	
CREATE TABLE users (
	user_id 	SERIAL PRIMARY KEY,
	user_name	varchar UNIQUE NOT NULL,
	password	varchar, 
	auth_hash	varchar UNIQUE,  
	email		varchar UNIQUE NOT NULL,
	first_name	varchar,
	last_name	varchar,
	quota		integer DEFAULT 0
);		

COMMENT ON COLUMN users.password IS 'sha1 hash of password';
COMMENT ON COLUMN users.auth_hash IS 
	'onetime key used for registration and lost passwords';

DROP TABLE IF EXISTS files CASCADE;	
CREATE TABLE files (
	file_id		SERIAL PRIMARY KEY,
	user_id		INTEGER NOT NULL 
		REFERENCES users(user_id) ON DELETE CASCADE, 
	file_name	varchar,
	location	varchar UNIQUE
);

DROP TABLE IF EXISTS messages CASCADE;	
CREATE TABLE messages (
	message_id	SERIAL PRIMARY KEY,
	user_id		INTEGER NOT NULL
		REFERENCES users(user_id) ON DELETE CASCADE, 
	subject		varchar,
	body		text 	--This is the same as varchar in pgsql, but 
						--I'm using this as a marker that the contents are
						--expected to be lengthy
);
COMMENT ON COLUMN messages.user_id IS 'user_id of sender';

DROP TABLE IF EXISTS recipients;	
CREATE TABLE recipients (
	message_id	INTEGER NOT NULL 
		REFERENCES messages(message_id) ON DELETE CASCADE,
	user_id 	INTEGER NOT NULL
		REFERENCES users(user_id) ON DELETE CASCADE,
	UNIQUE(message_id,user_id)
);

DROP TABLE IF EXISTS groups CASCADE;	
CREATE TABLE groups (
	group_id 	SERIAL PRIMARY KEY,
	group_name	varchar UNIQUE
);

DROP TABLE IF EXISTS group_files;	
CREATE TABLE group_files (
	group_id	INTEGER NOT NULL
		REFERENCES groups(group_id) ON DELETE CASCADE,
	file_id		INTEGER NOT NULL
		REFERENCES files(file_id) ON DELETE CASCADE,
	UNIQUE(group_id,file_id)
);
COMMENT ON TABLE group_files IS 'join table for files and groups';

DROP TABLE IF EXISTS group_members;	
CREATE TABLE group_members (
	group_id	INTEGER NOT NULL
		REFERENCES groups(group_id) ON DELETE CASCADE,
	user_id		INTEGER NOT NULL
		REFERENCES users(user_id) ON DELETE CASCADE,
	UNIQUE(group_id,user_id)
);
COMMENT ON TABLE group_members IS 'join table for users and groups';

INSERT INTO USERS(user_name,password) VALUES ('test', 'test');

COMMIT;


