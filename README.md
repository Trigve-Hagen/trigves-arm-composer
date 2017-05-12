# trigves-arm
PLEASE USE ON A NEW INSTALL OF LARAVEL FIRST TILL YOU UNDERSTAND HOW IT WORKS. - WHEN FIRST PUT INTO USE IT WILL ERASE ANYTHING DONE BEFORE IT AND CREATE A SAMPLE USER DATABASE Go into the root of your Laravel installation and run composer require trigves/arm from the command line. Add Trigves\Arm\ArmServiceProvider:class, to the providers array in config/app.php, fill in you database credentials in .env and run php artisan vendor:publish from your root. It will create an arm.php in the config folder where you can manage the tablesArray from. You will have to build your application from the array from there on out. It does not work with make:auth or Voyager. Out of the box it will automatically create the users table by default but you can erase it by erasing it from the tablesArray. I left it in there as and example because you have to follow the naming conventions. It builds it the moment you publish. Build the tablesArray in the same style but add your own names and fields. The first two fields are the table name, the unique auto increment id. They have to be in the array. The created_at and the updated_at are optional but some servers only allow one. The rest are also optional. They can be changed to VARCHAR TEXT INT etc.. For the moment this class is a stand alone project and has not been configured to work with Voyager or Artisan commands. There is some overhead in page load time when in development. When you go to production comment out the Service in the providers array in config/app.php and the load time will go back to normal.

YOU REALLY HAVE TO WATCH WHEN YOU ARE CREATING YOUR TABLES AND FIELDS  
 * WHEN FIRST PUT INTO USE IT WILL ERASE ANYTHING DONE BEFORE IT AND CREATE A SAMPLE USER DATABASE
 * NAMING TWO ROWS THE SAME WILL HALT EXECUTION AND CREATE AN ERROR   
 * NAMING TWO TABLES THE SAME WILL HALT EXECUTION AND CREATE AN ERROR  
 * STICK TO LOWER CASE IN THE SECTIONS THAT HAVE LOWER CASE AND ONLY USE UPPER CASE WHEN TYPING THE ROW  
 
I take no responsibility for lost data! Use at your risk. Happy Coding!!  

INSTALLATION  
1) goto root of application and type - composer require trigves/arm  
2) Place 'Trigves\Arm\ArmServiceProvider::class,' in the providers array in config/app  
3) Create the table in phpmyadmin and fill in your .env variables.  


3/10/2017 - updates  
* added support for adding multiple rows in a table that are next to each other.  
* added support for droping unneeded tables - just erase them from the array.  

3/13/2017 - updates  
* added support for changing row names  
* added error reporting
