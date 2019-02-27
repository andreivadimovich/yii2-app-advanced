#REST API for working with file system

Create, upload / download / list / update / info
   
####System requirements
#####Apache(ngnix) / MySQL >= 5.6 / PHP >= 5.6 / Composer / YII2-advanced
Detail info - https://www.yiiframework.com/doc/guide/2.0/en/start-installation.

After installation go to the [http://localhost/web/requirements.php](http://localhost/web/requirements.php).

###List of supported methods HTTP methods:

#####Upload file and create record
    
    POST http://localhost/v1/file-lists


#####Update file content and set file name

    PUT http://localhost/v1/file-lists/_ID_?name=_FILE_NAME_
    
The file content is in body. Value of the file name variable in GET.

Example requset in httpie
 - http PUT http://localhost/v1/file-lists/_ID_?name=_FILE_NAME_ < '/file/path'


#####Download content of one file (by id):

    GET http://localhost/v1/file-lists/_ID_ (file_list.id)

#####Options. Get simple meta data of the selected file. Date of file creation /changes, etc
(http://php.net/manual/ru/function.stat.php)

    OPTIONS http://l/v1/file-lists/_ID_

#####The file list

    GET http://localhost/v1/file-lists
