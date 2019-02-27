<h1>REST API for working with file system</h1>

<h4><a href="https://github.com/andreivadimovich/yii2-app-advanced/blob/master/api/README.md#install-1">Install</a></h4>
   
<h3>System requirements</h3>
Apache(ngnix) / MySQL >= 5.6 / PHP >= 5.6 / GIT / <a href="https://getcomposer.org/download">Composer</a> / <a href="https://github.com/yiisoft/yii2-app-advanced">YII2-advanced</a>

<h3>List of the supported HTTP methods</h3>

<b>1)</b> Upload the file and make record in table
<pre>
POST http://localhost/v1/file-lists
</pre>

<b>2)</b> Update the file contents and assign a name to the file
<pre>
PUT http://localhost/v1/file-lists/_ID_?name=_FILE_NAME_
</pre>
The file contents is in the body. The file name is a GET parameter.

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
OPTIONS http://l/v1/file-lists/_ID_
</pre>

<b>5)</b> The file list
<pre>
GET http://localhost/v1/file-lists
</pre>


<h2>INSTALL</h2>

```
0) git clone https://github.com/andreivadimovich/yii2-app-advanced.git

1) php composer.phar update 

2) /path/to/yii2_project php init 

3) set up a database connection /common/config/main-local.php 

4) /path/to/yii2_project php migrate up (if it doesn’t work – the data base dump is provided below in this document) 

5) create directory /api/web/file_list (777 recursive)
```

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
<hr />

<i><h5>#thoughts</h5></i>

In my opinion the following items are important (in order of priority): 

0) Customize Vagrant / Docker / Chef / Puppet with the app;
1) Refactor the code. Distribute everything in the controller in separate files (so that if something accidentally breaks in one place the program won't break completely);
2) Generate random names for the uploaded files;
3) Create the date change field in the file_list table;
4) Implement RBAC in order to give users their own file space; 
5) Solve web security issues; 
6) Work on optimizing upload / download files; 
7) Use exif php extension to get extended file details.



