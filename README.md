# Clinic Management System
small clinic management system web based application. developed with PHP Line framework traditional web style.
It manages patients, medical drugs in store, examination process and generates report.
3 actors doctor, lab specialist and cashier.

## how to prepare for use?
1. php requirements
2. first you have to build the database.
3. second you will start development server.

### PHP REQUIREMENTS
php 7.4.12

php have to be defined in environment variables on windows

it also need pdo_mysql, pdo_sqlsrv extension if you are using MSSQL Server.

### HOW TO BUILD THE DATABASE?
**a.** first create a database named by the first database name in the DBConfiguration file ~/apps/conf/DBConfiguration.php

    class DBConfiguration {
    
        public static $conf = [
        
            "MySql" => [
            
                "first" => [
                    "DB_NAME" => "examination",
                    "DB_USERNAME" => "root",
                    "DB_PASSWORD" => "",
                    "HOST_ADDRESS" => "localhost:3306"
                ]
                
            ],
            "MsSql" => [
                "first" => [
                    "DB_NAME" => "my_school",
                    "DB_USERNAME" => "Abnet",
                    "DB_PASSWORD" => "",
                    "HOST_ADDRESS" => "localhost"
                ],
                "second" => [
                    "DB_NAME" => "my_school",
                    "DB_USERNAME" => "abnet",
                    "DB_PASSWORD" => "",
                    "HOST_ADDRESS" => "localhost"
                ]
            ]
        ];
    
    }

**b.** after that execute the following commands in terminal/cmd

`php absoft export [Table Builder Name]`

in this case

    php absoft export Patients
    php absoft export Medicine
    php absoft export Laboratory

if you want to get help on CLI execute the following command

`php absoft help`

if you want to get help on specific command use

`php absoft help [command name]`


### HOW TO START DEVELOPMENT SERVER?
to Start development server execute the following command!

`php absoft run [port number]`

ex.

`php absoft run 2000`

if you don't specify port number by default it will start listen on port **http://localhost:1111**
