<h1>REST API for working with file system</h1>

Create, upload / download / list / update / info
   
<h3>System requirements</h3>
Apache(ngnix) / MySQL >= 5.6 / PHP >= 5.6 / GIT / Composer / YII2-advanced 


<h3>List of supported HTTP methods</h3>

<b>1)</b> Upload file and create record
<pre>
POST http://localhost/v1/file-lists
</pre>

<b>2)</b> Update file content and set file name
<pre>
PUT http://localhost/v1/file-lists/_ID_?name=_FILE_NAME_
</pre>
The file content is in body. File name is a GET parameter.

Example request in httpie utility
<pre>
http PUT http://localhost/v1/file-lists/_ID_?name=_FILE_NAME_ < '/file/path'
</pre>

<b>3)</b> Download content of one file (by id):
<pre>
GET http://localhost/v1/file-lists/_ID_ (file_list.id)
</pre>

<b>4)</b> Options. Get simple meta data of the selected file. Date of file creation /changes, etc
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
https://getcomposer.org/download

2) php init 

3) set up a database connection /common/config/main-local.php 

4) php migrate up (if not work - data base dump there is in this document below) 

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
```




