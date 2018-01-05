/* Chelsea Kochan
 * CSC 425 - Database
 * Mini-Project 2 - Company Database
 * 10/12/2014
*/

/* Number 1 */

/* A */ 
INSERT INTO A_EMPLOYEE
    values ("Robert",'F',"Scott","943775543",'1972-06-21',"2365 Newcastle Rd, Bellaire, TX",'M',58000,"888665555",1);

/* B */
INSERT INTO A_PROJECT
	values ("ProductA",4,"Bellaire",2);

/* C  - DID NOT WORK -*/
INSERT INTO A_DEPARTMENT
	values ("Production",4,"943775543",'2007-10-01');

/* D */
INSERT INTO A_WORKS_ON
	values ('677678989',NULL,'40.0');	
	
/* E */
INSERT INTO A_DEPENDENT
	values ("453453453","John",'M','1990-12-12','spouse');
	
/* F */ 
DELETE FROM A_WORKS_ON
	WHERE Essn = "453453453";
	
/* G */ 
DELETE FROM A_EMPLOYEE
	WHERE Ssn = "987654321";
	
/* H */
DELETE FROM A_PROJECT
	WHERE Pname = "ProductX";
	
/* I */
UPDATE A_DEPARTMENT
	SET Mgr_ssn = "999887777" AND Mgr_start_date = '2007-10-01'
	WHERE Dnumber = 5; 
	
/* J */
UPDATE A_EMPLOYEE
	SET Super_ssn = "943775543"
	WHERE Ssn = "999887777";
	
/* K */
UPDATE A_WORKS_ON
	SET Hours = '5.0'
	WHERE Essn = "999887777" AND Pno = 10;
	
/* Number 2 */
	
/* A */
SELECT Lname, Fname
	FROM A_EMPLOYEE
	WHERE Dno = 5 AND Ssn IN (SELECT Essn
                              FROM A_WORKS_ON, A_PROJECT
                              WHERE A_WORKS_ON.Pno = A_PROJECT.Pnumber AND Pname LIKE "ProductX" AND Hours > 10)
	ORDER BY Lname, Fname;
	
/* B */	
SELECT Lname, Fname
	FROM A_EMPLOYEE, A_DEPENDENT
	WHERE A_EMPLOYEE.Ssn = A_DEPENDENT.Essn AND Fname = Dependent_Name
	ORDER BY Lname, Fname; 
	
/* C */ 
SELECT Lname, Fname
	FROM A_EMPLOYEE
	WHERE Super_ssn IN (SELECT Ssn
                        FROM A_EMPLOYEE 
                        WHERE Lname LIKE "Wong" and Fname LIKE "Franklin")
	ORDER BY Lname, Fname;
						
/* D */			  
SELECT Lname, Fname
    FROM A_EMPLOYEE
    WHERE Ssn IN (SELECT Essn
                  FROM A_WORKS_ON 
                  GROUP BY Essn
                  HAVING COUNT(DISTINCT(Pno)) = (SELECT COUNT(Pnumber)
                                       FROM A_PROJECT))
    ORDER BY Lname, Fname; 

/* E */
SELECT Lname, Fname
    FROM A_EMPLOYEE
    WHERE Ssn NOT IN (SELECT Essn
                      FROM A_WORKS_ON)
    ORDER BY Lname, Fname;

/* F */
SELECT Lname, Fname, Address
    FROM A_EMPLOYEE
    WHERE Dno NOT IN (SELECT Dnumber 
                      FROM A_DEPT_LOCATIONS
                      WHERE Dlocation LIKE "Houston") AND Ssn IN (SELECT Essn
                                                                  FROM A_WORKS_ON
                                                                  WHERE Pno IN (SELECT Pnumber
                                                                                FROM A_PROJECT
                                                                                WHERE Plocation LIKE "Houston"))
    ORDER BY Lname, Fname;
	
/* G */
SELECT Lname, Fname
    FROM A_EMPLOYEE, A_DEPARTMENT
    WHERE A_EMPLOYEE.Ssn = A_DEPARTMENT.Mgr_ssn AND Ssn NOT IN(SELECT Essn
                                                            FROM A_DEPENDENT)
    ORDER BY Lname, Fname;

/* Number 3 */

/* A */ 
SELECT Lname, Fname, Ssn, Salary
	FROM A_EMPLOYEE
	WHERE Salary > (SELECT AVG(Salary) 
	                FROM A_DEPARTMENT, A_EMPLOYEE 
                    WHERE A_DEPARTMENT.Dnumber = A_EMPLOYEE.Dno)
    ORDER BY Dno, Lname, Fname;

/* B */                 
CREATE TABLE A_SUM_EMPLOYEES
SELECT Dname, Dnumber, COUNT(Dno) AS Total_Employees, SUM(Salary) AS Sum_Pay, AVG(Salary) As Avg_Pay
    FROM A_DEPARTMENT, A_EMPLOYEE
    WHERE A_DEPARTMENT.Dnumber = A_EMPLOYEE.Dno
    GROUP BY Dname;
	
SELECT Dname, Dnumber, Dlocation
    FROM A_SUM_EMPLOYEES NATURAL JOIN A_DEPT_LOCATIONS
    WHERE Total_Employees = (SELECT MAX(Total_Employees)
                             FROM A_SUM_EMPLOYEES);
					 
/* C */
SELECT Dname, Dnumber, DLocation
    FROM A_SUM_EMPLOYEES NATURAL JOIN A_DEPT_LOCATIONS
    WHERE Sum_Pay = (SELECT MIN(Sum_PAY)
                     FROM A_SUM_EMPLOYEES);
					 
/* D */ 
SELECT Dname, Dnumber, Dlocation 
    FROM A_SUM_EMPLOYEES NATURAL JOIN A_DEPT_LOCATIONS
    WHERE Avg_Pay > (SELECT Avg_Pay
                     FROM A_SUM_EMPLOYEES
                     WHERE Dname LIKE "Administration");
					 
/* E */
CREATE TABLE A_SUM_PROJECTS
SELECT Pname, Pnumber, Plocation, COUNT(Pno) AS Employees_On, SUM(Hours) AS Sum_Hours
    FROM A_PROJECT, A_WORKS_ON
    WHERE A_PROJECT.Pnumber = A_WORKS_ON.Pno
    GROUP BY Pnumber;

SELECT Pname, Pnumber, Plocation
    FROM A_SUM_PROJECTS
    WHERE Sum_Hours = (SELECT MAX(Sum_Hours)
                       FROM A_SUM_PROJECTS);
	
/* F */
SELECT Dname, Total_Employees
    FROM A_SUM_EMPLOYEES
	WHERE Avg_Pay > 30000;
	
/* G */
SELECT Lname, Fname 
	FROM A_EMPLOYEE
	WHERE Dno = (SELECT Dno
                 FROM A_EMPLOYEE
                 WHERE Salary = (SELECT MAX(Salary)
                                 FROM A_EMPLOYEE));
	
/* H */
SELECT Lname, Fname 
	FROM A_EMPLOYEE
	WHERE Salary > (SELECT (MIN(Salary)+ 10000)
                    FROM A_EMPLOYEE);


