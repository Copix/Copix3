create table %TABLENAME% (
	id_bayes int not null auto_increment,
	category_bayes varchar(55) not null,
        datas_bayes text not null,
	numdatas_bayes integer default 0,
	dataset_bayes varchar (55) not null,
	primary key(id_bayes)
) CHARACTER SET utf8;


