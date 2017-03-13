<?php
 
Route::group(['namespace' => 'Trigves\Arm\Controllers', 'prefix'=>'dbmapper'], function() {
    App::make('Trigves\Arm\Development\Arm')->ArmCheckTables();
});