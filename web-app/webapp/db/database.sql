-- 2024-03-07 defining test functions

-- function that detects the presence of a column

drop function if exists ${prefix}ColumnCount;

create function `${prefix}ColumnCount` (tabName varchar(64), colName varchar(64)) returns int
READS SQL DATA
DETERMINISTIC
begin
    declare result int;
    set result = (select count(1) from information_schema.columns  
         where TABLE_SCHEMA = database() and
         TABLE_NAME = tabName and COLUMN_NAME = colName 
        );
    return result; 
end;

-- function that detects the presence of an index

drop function if exists ${prefix}IndexCount;

create function `${prefix}IndexCount` (tabName varchar(64), idxName varchar(64)) returns int
READS SQL DATA
DETERMINISTIC
begin
    declare result int;
    set result = (select count(1) from information_schema.statistics  
         where TABLE_SCHEMA = database() and
         TABLE_NAME = tabName and INDEX_NAME = idxName 
        );
    return result; 
end;

-- function that returns the column type 

drop function if exists ${prefix}ColumnType;

create function `${prefix}ColumnType` (tabName varchar(64), colName varchar(64)) returns varchar(64)
READS SQL DATA
DETERMINISTIC
begin
    declare result varchar(64);
    set result = (
        select COLUMN_TYPE from information_schema.COLUMNS
         where TABLE_SCHEMA = database() and
         TABLE_NAME = tabName and COLUMN_NAME = colName );
    return result; 
end;

-- 2024-03-07 test functions created
-- 2024-03-08 tables for user management

-- user table without history

create table if not exists `${prefix}AppUser` (
    `UserId` int primary key not null auto_increment,
    `Email` varchar(255) not null,
    `Level` int not null default 0,
    `Created` timestamp not null default current_timestamp,
    unique (`Email`)
);

-- separate password table, so users can use several passwords

create table if not exists `${prefix}UserPassword` (
    `UserId` int not null,
    `Password` varchar(64), -- hashed, not plain
    `Created` timestamp not null default current_timestamp,
    `Used` timestamp,
    unique (`UserId`, `Password`)
);

-- 2024-03-08 user table created
-- 2024-04-07 SiteConfig functions

create table if not exists `${prefix}SiteConfig` (
    `Name` varchar(64) primary key not null,
    `Value` varchar(255),
    `Modified` timestamp not null default current_timestamp
);

-- 2024-04-19 SiteConfig created

-- 2024-08-18 Pages

create table if not exists `${prefix}Page` (
    `PageId` int not null,
    `Position` int not null,
    `Name` varchar(255),
    `Picture` varchar(255),
    `Description` varchar(512),
    `Content` text,
    `IsActive` TINYINT(1) NOT NULL DEFAULT 0,
    `Created` timestamp not null default current_timestamp
)

-- 2024-08-18 Pages created

