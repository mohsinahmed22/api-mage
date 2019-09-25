create table categories
(
  id          int auto_increment
    primary key,
  name        varchar(256)                          not null,
  description text                                  not null,
  created     datetime                              not null,
  modified    timestamp default current_timestamp() not null
)
  engine = InnoDB
  charset = utf8;

create table prod
(
  id      int auto_increment
    primary key,
  sku     varchar(255) default 'NULL' null,
  qty     int default 'NULL'          null,
  item_id int default 'NULL'          null,
  constraint prod_sku_uindex
  unique (sku)
)
  engine = InnoDB;

create table products
(
  id          int auto_increment
    primary key,
  name        varchar(32)                           not null,
  description text                                  not null,
  price       decimal                               not null,
  category_id int                                   not null,
  created     datetime                              not null,
  modified    timestamp default current_timestamp() not null
)
  engine = InnoDB;


