#!/bin/bash

echo Masukkan nama DB yang akan di backup ?
read namaDB
FILE="$namaDB-$(date +%Y%m%d-%H%M%S).sql"
mysqldump -u root -p --port=3306 --routines --triggers --events --databases $namaDB > $FILE
rm DB_$namaDB.zip
zip DB_$namaDB.zip $FILE


