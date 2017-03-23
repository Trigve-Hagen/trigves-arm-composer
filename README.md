# trigves-arm
PLEASE USE ON A NEW INSTALL OF LARAVEL FIRST TILL YOU UNDERSTAND HOW IT WORKS. Go into the root of your Laravel installation and run composer require trigves/arm from the command line. Add Trigves\Arm\ArmServiceProvider:class, to the providers array in config/app.php, fill in you database credentials in .env and run php artisan vendor:publish from your root. It will create an arm.php in the config folder where you can manage the tablesArray from. You will have to build your application from the array from there on out. It does not work with make:auth or Voyager. Out of the box it will automatically create the users table by default but you can erase it by erasing it from the tablesArray. I left it in there as and example because you have to follow the naming conventions. It builds it the moment you publish. Build the tablesArray in the same style but add your own names and fields. The first two fields are the table name, the unique auto increment id. They have to be in the array. The created_at and the updated_at are optional but some servers only allow one. The rest are also optional. They can be changed to VARCHAR TEXT INT etc.. For the moment this class is a stand alone project and has not been configured to work with Voyager or Artisan commands. I will be working on this for the future updates. There is some overhead in page load time when in development. When you go to production comment out the Service in the providers array in config/app.php and the load time will go back to normal. I will be working on an auth system for the next version. It is not done still as I need to creat migrations.

YOU REALLY HAVE TO WATCH WHEN YOU ARE CREATING YOUR TABLES AND FIELDS  
 * NAMING TWO ROWS THE SAME WILL HALT EXECUTION AND CREATE AN ERROR  
 * NO TWO FIELDS IN A TABLE CAN HAVE THE SAME NAME  
 * NAMING TWO TABLES THE SAME WILL HALT EXECUTION AND CREATE AN ERROR  
 * NO TWO TABLES CAN HAVE THE SAME NAME 
 * SQL WILL TURN THE TABLE NAMES THAT HAVE UPPERCASE CHARACTERS TO LOWER CASES AND YOUR ARRAY WON'T MATCH THE DATABASES
 * STICK TO LOWER CASE IN THE SECTIONS THAT HAVE LOWER CASE AND ONLY USE UPPER CASE WHEN TYPING THE ROW
 * UNLESS YOU WANT TO MESS WITH IT FOR YOUR PURPOSES!!!

 * IT WAS ERRORING ON LOCALHOST BECAUSE I HAD THE TABLE NAMES INCLUDING UPPER CASE CHARACTERS. WHEN IT CREATED THE DATABASES IT TURNED THEM TO LOWER CASE. THEN WHEN I CHECKED MY $_tablesArray (UPPER CASE) AGAINST THE DATABASE ARRAY(LOWWER CASE) IT RETURNED NOT IN THE ARRAY AND DELETED THEM BOTH. - users_c3p0r2d2007OG - has to be users_c3p0r2d2007og

Due to adding new features to the class last week I was running into new errors. When debbugging I realized the new error was not a new error at all but this. If you had named two databases the same they would both be erased when dropping a table. I added new logic for reporting the error. I also adding logic to report having two rows with the same name and errors and messages for everything else. PLease be carefull anyways. I take no responsibility for lost data! Use at your risk. If I have no new problems to report in the next month of developing with it than it is done. Happy Coding!!  

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
