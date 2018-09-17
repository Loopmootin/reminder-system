<?php 

//forbindelse til mySQL serveren ved brug af mysqli metode

//1. variabler(konstanter) til forbindelsen
const HOSTNAME = 'localhost'; //server
const MYSQLUSER = 'root'; //superbruger
const MYSQLPASS = 'root'; //password
const MYSQLDB = 'reminder_system'; //database navn

//2. forbindelsen via mysqli metoden
$con = new mysqli(HOSTNAME, MYSQLUSER, MYSQLPASS, MYSQLDB);

//at sikre sig, at alle utf8-tegn kan blive brugt under forbindelsen
$con->set_charset('utf8');


//3. tjek
//hvis der er fejl i forbindelsen 
if($con->connect_error) {
	die($con->connect_error);
	
//hvis der er hul igennem
} else {
	//echo 'halluuu';
}

//PHP slut tag kan undlades, hvis filen indeholder "rent" PHP
?>