use sys;

drop database if exists `example-site`;
create database `example-site`;

drop user if exists 'ex-site-user'@'%';
create user 'ex-site-user' identified by 'RNq7nbOh_lwFnrgy';

GRANT CREATE, DROP, ALTER, 
    SELECT, INSERT, UPDATE, DELETE, 
    CREATE ROUTINE, ALTER ROUTINE, EXECUTE
    ON `example-site`.* TO 'ex-site-user'@'%';
