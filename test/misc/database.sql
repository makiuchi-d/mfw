
drop database if exists `unittest`;
create database `unittest`;

grant all on unittest.* to 'unittest'@'localhost' identified by 'unittestpass';
grant process on *.* to 'unittest'@'localhost' identified by 'unittestpass';
