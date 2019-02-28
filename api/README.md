<h1>REST API for working with file system</h1>

<br />
<h4><a href="https://github.com/andreivadimovich/yii2-app-advanced/tree/master/api#install-1">Install</a></h4>
<h4><a href="https://github.com/andreivadimovich/yii2-app-advanced/tree/master/api#running-the-tests-1">Running the tests</a></h4>

<h5>
<a href="https://github.com/andreivadimovich/yii2-app-advanced/tree/master/api#thoughts-1">#thoughts</a>
</h5>
<br />

<h3>Requirements</h3>
Apache(ngnix) , MySQL >= 5.6 , PHP >= 5.6 , GIT , <a href="https://getcomposer.org/download">Composer</a> , <a href="https://github.com/yiisoft/yii2-app-advanced">YII2-advanced</a> , <a href="http://guzzlephp.org/">guzzlehttp</a>

<br /><br />
<h3>List of the supported HTTP methods</h3>

<b>1)</b> Upload the file and make record in table
<pre>
POST http://localhost/v1/file-lists
</pre>

<b>2)</b> Update the file contents and assign a name to the file
<pre>
PUT http://localhost/v1/file-lists/_ID_?name=_FILE_NAME_
</pre>
The file contents is in the body. The file name is a contained in GET parameter.

Example of the request in httpie utility
<pre>
http PUT http://localhost/v1/file-lists/_ID_?name=_FILE_NAME_ < '/file/path'
</pre>

<b>3)</b> Download contents of one file (by id):
<pre>
GET http://localhost/v1/file-lists/_ID_ (file_list.id)
</pre>

<b>4)</b> Options. Get simple meta data of the selected file. File creation date /changes, etc
(http://php.net/manual/ru/function.stat.php)
<pre>
OPTIONS http://localhost/v1/file-lists/_ID_
</pre>

<b>5)</b> The file list
<pre>
GET http://localhost/v1/file-lists
</pre>

<br />
<h2>INSTALL</h2>

```
Install Composer 
<pre>
curl -sS https://getcomposer.org/installer | php
</pre>

1) git clone https://github.com/andreivadimovich/yii2-app-advanced.git

2) add line 
"guzzlehttp/guzzle": "^6.2"
to your /project/path/composer.json 

3) /project/path  php composer.phar update 

4) /project/path php init 

5) set up a database connection /common/config/main-local.php 

6) /project/path php migrate up (if it doesn’t work – the data base dump is provided below in this document) 
the migration files are in /console/migrations

7) create directory /api/web/file_list (chmod 777 to a folder and all contents)

8) create file /api/runtime/logs/rest.log
```

<br />
SQL dump:

```
CREATE TABLE `file_list` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
	`name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL , 
	`type` VARCHAR(50) NULL,
	`size` BIGINT(20) NULL,
	PRIMARY KEY (id)
) 
ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci COMMENT = 'List of files';

# for test
CREATE TABLE `test_file_list` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
	`name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL , 
	`type` VARCHAR(50) NULL,
	`size` BIGINT(20) NULL,
	PRIMARY KEY (id)
) 
ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci COMMENT = 'List of files';
```

<br />
<h3>Running the tests</h3>

Codeception - acceptance type.<br />
Configurate the file /common/config/test-local.php (install test database). 

After 
<pre>
/project/path php migrate up
</pre>

1) is called once upon initialization
<pre>
./vendor/bin/codecept bootstrap
</pre>


2)
<pre>
./vendor/bin/codecept build
</pre>

3) 
<pre>
./vendor/bin/codecept run
</pre>



<hr />
<br />
<i><h5>#thoughts</h5></i>

In my opinion the following items are important (in order of priority): 

0) Customize Vagrant / Docker / Chef / Puppet with the app
1) Refactor the code. Distribute everything in the controller in separate files (so that if something accidentally breaks in one place the program won't break completely)
2) Generate random names for the uploaded files
3) Create the date change field in the file_list table
4) Implement RBAC in order to give users their own file space
5) Work on optimizing upload / download files
6) Use exif php extension to get extended file details
7) Solve web security issues (after adding the necessary functionality - until release).


