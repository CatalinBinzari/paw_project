DROP TABLE IF EXISTS cereri_bursa;
DROP TABLE IF EXISTS conturi;
DROP TABLE IF EXISTS bursieri;
DROP TABLE IF EXISTS burse;
DROP TABLE IF EXISTS elevi;
/*DROP TABLE IF EXISTS scoli;*/
DROP TABLE IF EXISTS lista_de_asteptare;
DROP TABLE IF EXISTS administratori;

/* exec with C:\Users\cb\Desktop\bd_builder.sql*/
/*CREATE TABLE scoli(
id_scoala INT(11) AUTO_INCREMENT PRIMARY KEY,
denumirea_institutiei VARCHAR(80) NOT NULL,
oras VARCHAR(80) NOT NULL,
adresa VARCHAR(100),
telefon VARCHAR(12),
email VARCHAR(100)
);
*/


CREATE TABLE elevi(
id_elev INT(10) AUTO_INCREMENT,
cnp VARCHAR(13) NOT NULL,
nume VARCHAR(30) NOT NULL,
prenume VARCHAR(30) NOT NULL,
data_nasterii DATE NOT NULL,
id_scoala INT(11) NOT NULL,
medie DECIMAL(4) DEFAULT '0',
email VARCHAR(50) NOT NULL,
parola VARCHAR(50) NOT NULL,
PRIMARY KEY(id_elev),
FOREIGN KEY (id_scoala) REFERENCES scoli(id_scoala)	
		ON DELETE CASCADE
);
/*INSERT INTO people(first_name,last_name,birth_date)
VALUES('John','Doe','1990-09-01');*/

CREATE TABLE burse(
tip_bursa VARCHAR(50) NOT NULL PRIMARY KEY,
valoare_bursa DECIMAL(8)
);

CREATE TABLE bursieri(
id_elev INT(10) NOT NULL,
tip_bursa VARCHAR(50) NOT NULL,
FOREIGN KEY (id_elev) REFERENCES elevi(id_elev)
		ON DELETE CASCADE,
FOREIGN KEY (tip_bursa) REFERENCES burse(tip_bursa)	
		ON DELETE CASCADE
);

CREATE TABLE conturi(
	id_elev INT(10) NOT NULL,
	email VARCHAR(50) NOT NULL,
	parola VARCHAR(50) NOT NULL,
	FOREIGN KEY (id_elev) REFERENCES elevi(id_elev)
			ON DELETE CASCADE
);

CREATE TABLE lista_de_asteptare(
	cnp VARCHAR(13) NOT NULL,
	nume VARCHAR(30) NOT NULL,
	prenume VARCHAR(30) NOT NULL,
	data_nasterii DATE NOT NULL,
	id_scoala INT(11) DEFAULT '1',/*trebuie un combobx cu scoli si id)*/
	email VARCHAR(50) NOT NULL,
	parola VARCHAR(50) NOT NULL
);

CREATE TABLE administratori(
	id_admin INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(50) NOT NULL,
	parola VARCHAR(50) NOT NULL
);

CREATE TABLE cereri_bursa(
id_elev INT(10) NOT NULL,
tip_bursa VARCHAR(50) NOT NULL,
aprobare VARCHAR(20) DEFAULT 'In asteptare',
FOREIGN KEY (id_elev) REFERENCES elevi(id_elev)
		ON DELETE CASCADE,
FOREIGN KEY (tip_bursa) REFERENCES burse(tip_bursa)	
		ON DELETE CASCADE
);

CREATE TABLE logs(
	utilizator_id VARCHAR(20),
	functie VARCHAR(20),
	actiune_logica VARCHAR(100)
);

insert into burse(tip_bursa,valoare_bursa) values ('sociala','300');
insert into burse(tip_bursa,valoare_bursa) values ('performanta','1000');
insert into burse(tip_bursa,valoare_bursa) values ('merit','600');

CREATE TABLE statistica(
	an INT(4),
	burse_merit INT(4),
	burse_sociale INT(4),
	burse_performanta INT(4)
);
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('1999','967','1967','67');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2000','667','2967','167');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2001','367','3967','267');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2002','1267','2967','167');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2003','867','4967','67');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2004','347','1967','367');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2005','667','3967','267');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2006','367','1967','167');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2007','1267','967','467');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2008','967','1967','167');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2009','967','3967','367');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2010','967','1967','67');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2011','367','2967','467');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2012','667','1967','167');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2013','967','4967','367');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2014','887','1967','467');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2015','967','1967','667');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2016','1267','1967','867');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2017','967','967','867');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2018','467','1967','167');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2019','967','2967','367');
insert into statistica(an,burse_merit,burse_sociale,burse_performanta) values ('2020','1267','3967','367');


insert into scoli (denumirea_institutiei,  oras, adresa,  telefon,  email) 
	values ('SCOALA GIMNAZIALA NR.2 GALATI', 'Galati', 'Tiglina 1, Str.Regiment 11 Siret nr.2','312544','scoala2_gl_ro@yahoo.com');

insert into scoli (denumirea_institutiei,  oras, adresa,  telefon,  email) 
	values ('SCOALA GIMNAZIALA NR.3 "I.L.CARAGIALE" GALATI ', 'Galati', 'B-dul Dunarea nr.60','472422','scoala_il_caragiale@yahoo.com');

insert into scoli (denumirea_institutiei,  oras, adresa,  telefon,  email) 
	values ('SCOALA GIMNAZIALA NR.5  "CUZA VODA"  GALATI ', 'Galati', 'Str.Gorunului nr.6','468366','	scoala5cuzavoda@yahoo.fr');

insert into scoli (denumirea_institutiei,  oras, adresa,  telefon,  email) 
	values ('SCOALA GIMNAZIALA NR.7  "CONSTANTIN BRANCOVEANU" GALATI', 'Galati', 'Str.Brailei nr.204','410236','N/S');