CREATE database `babysql` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'babysql'@'%' IDENTIFIED BY '59ce4acfa157f4169f5a6820a4d3aa3a';
GRANT ALL PRIVILEGES ON babysql.* TO 'babysql'@'%';
FLUSH PRIVILEGES;

use babysql;
create table users(`id` int not null,`username` varchar(255) null,`password` varchar(255) null,constraint users_pk primary key (id));
insert into users(`id`,`username`,`password`) values (1,'zhangsan','zhangsan'),(2,'lisi','lisi');
create table flag(`flag` varchar(255) not null,constraint users_pk primary key (flag));
insert into flag(flag) values ('flag{Hh_N1c3_1}');