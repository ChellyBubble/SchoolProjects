/* Chelsea Kochan
 * CSC 425 - Database
 * Mini-Project 3 - Company Database
 * 10/29/2014
*/


create database Company;
use cs425107;

create table A_EMPLOYEE
	(Fname		varchar(20)		not null,
	Minit		varchar(1),
	Lname		varchar(20)		not null,
	Ssn			varchar(9)		not null,
	Bdate		DATE			not null,
	Address		varchar(40)		not null,
	Sex			char(1)			not null,
	Salary		int				not null,
	Super_ssn	varchar(9),
	Dno			int				not null,
	Email		varchar(60)		not null,
	User_name	varchar(20)		not null, 
	Password 	varchar(20)		not null,
	Admin_user	boolean 		not null, 
	primary key(Ssn));

create table A_DEPARTMENT
	(Dname			varchar(20)		not null,
	Dnumber			int				not null,
	Mgr_ssn			varchar(9)		not null,
	Mgr_start_date	DATE			not null,
	primary key(Dnumber));

create table A_DEPT_LOCATIONS
	(Dnumber	int				not null,
	Dlocation	varchar(20)		not null,
	primary key(Dnumber, Dlocation));

create table A_PROJECT
	(Pname		varchar(20)		not null,
	Pnumber		int				not null,
	Plocation	varchar(20)		not null,
	Dnum		int				not null,
	primary key(Pnumber));

create table A_WORKS_ON
	(Essn		varchar(9)		not null,
	Pno			int				not null,
	Hours		float,
	primary key(Essn, Pno));

create table A_DEPENDENT
	(Essn			varchar(9)		not null,
	Dependent_name 	varchar(20)		not null,
	Sex				char(1)			not null,
	Bdate			DATE			not null,
	Relationship	varchar(10)		not null,
	primary key(Essn, Dependent_name));

INSERT INTO A_EMPLOYEE 
	values ("John",'B',"Smith","123456789",'1965-01-09',"731 Fondren, Houston, TX",'M',30000,"333445555",5, "jsCS425@yahoo.com", "jsmith", "pass", false);
INSERT INTO A_EMPLOYEE 
	values ("Franklin",'T',"Wong","333445555",'1955-12-08',"638 Voss, Houston, TX",'M',40000,"888665555",5, "fwCS425@yahoo.com", "fwong", "pass", true);
INSERT INTO A_EMPLOYEE 
	values ("Alicia",'J',"Zelaya","999887777",'1968-01-19',"3321 Castle, Spring, TX",'F',25000,"987654321",4, "azCS425@yahoo.com", "azelaya", "pass", false);
INSERT INTO A_EMPLOYEE 
	values ("Jennifer",'S',"Wallace","987654321",'1941-06-20',"291 Berry, Bellaire, TX",'F',43000,"888665555",4, "jwCS425@yahoo.com", "jwallace", "pass", true);
INSERT INTO A_EMPLOYEE 
	values ("Ramesh",'K',"Narayan","666884444",'1962-09-15',"975 Fire Oak, Humble, TX",'M',38000,"333445555",5, "rnCS425@yahoo.com", "rnarayan", "pass", false);
INSERT INTO A_EMPLOYEE 
	values ("Joyce",'A',"English","453453453",'1972-07-31',"5631 Rice, Houston, TX",'F',25000,"333445555",5, "jeCS425@yahoo.com", "jenglish", "pass", false);
INSERT INTO A_EMPLOYEE 
	values ("Ahmad",'V',"Jabbar","987987987",'1969-03-29',"980 Dallas, Houston, TX",'M',25000,"987654321",4, "ajCS425@yahoo.com", "ajabbar", "pass", false);
INSERT INTO A_EMPLOYEE 
	values ("James",'E',"Borg","888665555",'1937-11-10',"450 Stone, Houston, TX",'M',55000,NULL,1, "jbCS425@yahoo.com", "jborg", "pass", true);

INSERT INTO A_DEPARTMENT 
	values ("Research",5,"333445555",'1988-05-22');
INSERT INTO A_DEPARTMENT 
	values ("Administration",4,"987654321",'1995-01-01');
INSERT INTO A_DEPARTMENT 
	values ("Headquarters",1,"888665555",'1981-06-19');

INSERT INTO A_DEPT_LOCATIONS 
	values (1,"Houston");
INSERT INTO A_DEPT_LOCATIONS 
	values (4,"Stafford");
INSERT INTO A_DEPT_LOCATIONS 
	values (5,"Bellaire");
INSERT INTO A_DEPT_LOCATIONS 
	values (5,"Sugarland");
INSERT INTO A_DEPT_LOCATIONS 
	values (5,"Houston");

INSERT INTO A_DEPENDENT 
	values ("333445555","Alice",'F','1986-04-05',"Daughter");
INSERT INTO A_DEPENDENT 
	values ("333445555","Theodore",'M','1983-10-25',"Son");
INSERT INTO A_DEPENDENT 
	values ("333445555","Joy",'F','1958-05-03',"Spouse");
INSERT INTO A_DEPENDENT 
	values ("987654321","Abner",'M','1942-02-28',"Spouse");
INSERT INTO A_DEPENDENT
	values ("123456789","Michael",'M','1988-01-04',"Son");
INSERT INTO A_DEPENDENT 
	values ("123456789","Alice",'F','1988-12-30',"Daughter");
INSERT INTO A_DEPENDENT 
	values ("123456789","Elizabeth",'F','1967-05-05',"Spouse");

INSERT INTO A_PROJECT 
	values ("ProductX",1,"Bellaire",5);
INSERT INTO A_PROJECT 
	values ("ProductY",2,"Sugarland",5);
INSERT INTO A_PROJECT 
	values ("ProductZ",3,"Houston",5);
INSERT INTO A_PROJECT 
	values ("Computerization",10,"Stafford",4);
INSERT INTO A_PROJECT 
	values ("Reorganization",20,"Houston",1);
INSERT INTO A_PROJECT 
	values ("Newbenefits",30,"Stafford",4);

INSERT INTO A_WORKS_ON 
	values ("123456789",1,32.5);
INSERT INTO A_WORKS_ON 
	values ("123456789",2,7.5);
INSERT INTO A_WORKS_ON 
	values ("666884444",3,40.0);
INSERT INTO A_WORKS_ON 
	values ("453453453",1,20.0);
INSERT INTO A_WORKS_ON 
	values ("453453453",2,20.0);
INSERT INTO A_WORKS_ON 
	values ("333445555",2,10.0);
INSERT INTO A_WORKS_ON 
	values ("333445555",3,10.0);
INSERT INTO A_WORKS_ON 
	values ("333445555",10,10.0);
INSERT INTO A_WORKS_ON 
	values ("333445555",20,10.0);
INSERT INTO A_WORKS_ON 
	values ("999887777",30,30.0);
INSERT INTO A_WORKS_ON 
	values ("999887777",10,10.0);
INSERT INTO A_WORKS_ON 
	values ("987987987",10,30.0);
INSERT INTO A_WORKS_ON 
	values ("987987987",30,5.0);
INSERT INTO A_WORKS_ON 
	values ("987654321",30,20.0);
INSERT INTO A_WORKS_ON 
	values ("987654321",20,15.0);
INSERT INTO A_WORKS_ON 
	values ("888665555",20,null);

ALTER TABLE A_EMPLOYEE
	ADD FOREIGN KEY(Super_ssn)
	REFERENCES A_EMPLOYEE(Ssn)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE A_EMPLOYEE
	ADD FOREIGN KEY(Dno)
	REFERENCES A_DEPARTMENT(Dnumber)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE A_DEPARTMENT
	ADD FOREIGN KEY(Mgr_ssn)
	REFERENCES A_EMPLOYEE(Ssn)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE A_DEPT_LOCATIONS
	ADD FOREIGN KEY(Dnumber)
	REFERENCES A_DEPARTMENT(Dnumber)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE A_DEPENDENT
	ADD FOREIGN KEY(Essn)
	REFERENCES A_EMPLOYEE(Ssn)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE A_PROJECT
	ADD FOREIGN KEY(Dnum)
	REFERENCES A_DEPARTMENT(Dnumber)
	ON DELETE CASCADE
	ON UPDATE CASCADE;
	
ALTER TABLE A_WORKS_ON
	ADD FOREIGN KEY(Essn)
	REFERENCES A_EMPLOYEE(Ssn)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE A_WORKS_ON
	ADD FOREIGN KEY(Pno)
	REFERENCES A_PROJECT(Pnumber)
	ON DELETE CASCADE
	ON UPDATE CASCADE;
