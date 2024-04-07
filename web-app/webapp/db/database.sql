-- 2024-03-07 start and defining functions

drop function if exists ${prefix}ColumnCount;

create function `${prefix}ColumnCount` (tabName varchar(64), colName varchar(64)) returns int
begin
    declare result int;
    set result = (select count(1) from information_schema.columns  
         where TABLE_SCHEMA = database() and
         TABLE_NAME = tabName and COLUMN_NAME = colName 
        );
    return result; 
end;

drop function if exists ${prefix}IndexCount;

create function `${prefix}IndexCount` (tabName varchar(64), idxName varchar(64)) returns int
begin
    declare result int;
    set result = (select count(1) from information_schema.statistics  
         where TABLE_SCHEMA = database() and
         TABLE_NAME = tabName and INDEX_NAME = idxName 
        );
    return result; 
end;

drop function if exists ${prefix}ColumnType;

create function `${prefix}ColumnType` (tabName varchar(64), colName varchar(64)) returns varchar(64)
begin
    declare result varchar(64);
    set result = (
        select COLUMN_TYPE from information_schema.COLUMNS
         where TABLE_SCHEMA = database() and
         TABLE_NAME = tabName and COLUMN_NAME = colName );
    return result; 
end;

-- 2024-03-08 end of test functions

create table if not exists `${prefix}AppUser` (
    `UserId` int primary key not null auto_increment,
    `Email` varchar(255) not null,
    `Level` int not null default 0,
    `Created` timestamp not null default current_timestamp,
    unique (`Email`)
);

create table if not exists `${prefix}UserPassword` (
    `UserId` int not null,
    `Password` varchar(64),
    `Created` timestamp not null default current_timestamp,
    `Used` timestamp,
    unique (`UserId`, `Password`)
);

-- 2024-04-07 user created