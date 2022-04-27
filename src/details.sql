SELECT `unique_id`,
       `php_version`,
       TRIM('.' FROM REGEXP_SUBSTR(`php_version`, '^[0-9]+\\.[0-9]+\\.')) AS `php_short`,
       TRIM('.' FROM REGEXP_SUBSTR(`php_version`, '^[0-9]+\\.'))          AS `php_major`,
       TRIM('.' FROM REGEXP_SUBSTR(`php_version`, '\\.[0-9]+\\.'))        AS `php_minor`,
       TRIM('.' FROM REGEXP_SUBSTR(`php_version`, '\\.[0-9]+$'))          AS `php_patch`,
       `db_type`                                                          AS `db_driver`,
       CASE `db_type`
           WHEN 'mysql' THEN IF(TRIM('.' FROM REGEXP_SUBSTR(`db_version`, '^[0-9]+\\.')) < 10, 'MySQL', 'MariaDB')
           WHEN 'mysqli' THEN IF(TRIM('.' FROM REGEXP_SUBSTR(`db_version`, '^[0-9]+\\.')) < 10, 'MySQL', 'MariaDB')
           WHEN 'pdomysql' THEN IF(TRIM('.' FROM REGEXP_SUBSTR(`db_version`, '^[0-9]+\\.')) < 10, 'MySQL', 'MariaDB')
           WHEN 'pgsql' THEN 'PostgreSQL'
           WHEN 'postgresql' THEN 'PostgreSQL'
           WHEN 'oracle' THEN 'Oracle'
           WHEN 'sqlazure' THEN 'SQL Server'
           WHEN 'sqlsrv' THEN 'SQL Server'
           WHEN 'sqlite' THEN 'SQLite'
           ELSE `db_type`
           END                                                            AS `db_type`,
       IF(`db_type` IN ('mysql', 'pdomysql', 'pgsql'), 'PDO', 'Native')   AS `db_connection`,
       `db_version`,
       TRIM('.' FROM REGEXP_SUBSTR(`db_version`, '^[0-9]+\\.[0-9]+\\.'))  AS `db_short`,
       TRIM('.' FROM REGEXP_SUBSTR(`db_version`, '^[0-9]+\\.'))           AS `db_major`,
       TRIM('.' FROM REGEXP_SUBSTR(`db_version`, '\\.[0-9]+\\.'))         AS `db_minor`,
       TRIM('.' FROM REGEXP_SUBSTR(`db_version`, '\\.[0-9]+$'))           AS `db_patch`,
       `cms_version`,
       TRIM('.' FROM REGEXP_SUBSTR(`cms_version`, '^[0-9]+\\.[0-9]+\\.')) AS `cms_short`,
       TRIM('.' FROM REGEXP_SUBSTR(`cms_version`, '^[0-9]+\\.'))          AS `cms_major`,
       TRIM('.' FROM REGEXP_SUBSTR(`cms_version`, '\\.[0-9]+\\.'))        AS `cms_minor`,
       TRIM('.' FROM REGEXP_SUBSTR(`cms_version`, '\\.[0-9]+$'))          AS `cms_patch`,
       `server_os`,
       REGEXP_SUBSTR(`server_os`, '[^ ]+')                                AS `platform`,
       `modified`,
       YEAR(`modified`)                                                   AS `year`,
       LEFT(CAST(EXTRACT(YEAR_MONTH FROM `modified`) / 100 AS CHAR), 7)   AS `year_month`,
       MONTH(`modified`)                                                  AS `month`
FROM `jos_jstats`
