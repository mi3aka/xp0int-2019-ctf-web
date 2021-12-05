CREATE database `easysql` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'easysql'@'%' IDENTIFIED BY 'fa3f0baead05bca30d3d7f63caad7b1c';
GRANT ALL PRIVILEGES ON easysql.* TO 'easysql'@'%';
FLUSH PRIVILEGES;

use easysql;
create table users(`id` int not null,`username` varchar(255) null,`password` varchar(255) null,constraint users_pk primary key (id));
insert into users(`id`,`username`,`password`) values (1,'zhangsan','zhangsan'),(2,'lisi','lisi');
create table flag(`flag` varchar(255) not null,constraint users_pk primary key (flag));
insert into flag(`flag`) values ('flag{u_c4ught_me}');