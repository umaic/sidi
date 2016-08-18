# sidi

## Instalación

1. Crear copia de los siguientes archivos y colocar valores de: user_db, password_db y db_name

    - /admin/lib/common/mysqldb-sample.class.php --> /admin/lib/common/mysqldb.class.php y colocar user, password y db_name
    - /admin/lib/common/mysqldb_despla_import-sample.class.php --> /admin/lib/common/mysqldb_despla_import.class.php

3. Crear un link simbólico de admin: 
   $ln -s admin t
   
3. Crear los directorios
    - /_sgol/
    - /_sgol/login
    - /perfiles
    - /static/4w

4. Permisos de escritura recursivos para:

    ###Admin
    - /admin/dato_s_valor/csv
    - /admin/dato_sectorial/reportes
    - /admin/org/csv
    - /admin/org/reportes
    - /admin/mina/csv
    - /admin/desplazamiento/csv
    - /admin/evento_c/
    - /admin/evento_c/reporte_eventos.xls

    ###Consulta
    - /consulta/csv
    - /consulta/pdf
    - /consulta/resumen/desplazamiento
    - /consulta/resumen/mina

    ###Gráficas flash
    - /chart-data.php
    - /chart-data_v2.php

    ###Mapserver
    - /consulta/test_mapserver/test_map_area_depto.map
    - /consulta/test_mapserver/test_map_area_mpio.map
    - /images/cache_mapserver/consulta/
    - /images/cache_mapserver/perfil/

    ###Logs
    - /_sgol/*

    ###Perfiles
    - /perfiles

    ###Static
    - /static/*
