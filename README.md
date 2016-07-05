# sidi

## instalación

1. Crear copia de /admin/lib/common/mysqldb-sample.class.php 
   a /admin/lib/common/mysqldb.class.php
   
2. Crear copia de /admin/lib/common/mysqldb_despla_import-sample.class.php 
   a /admin/lib/common/mysqldb_despla_import.class.php

3. Cree un link simbólico de admin: 
   $ln -s admin t

4. Permisos de escritura recursivos para:

###ADMIN
/admin/dato_s_valor/csv
/admin/dato_sectorial/reportes
/admin/org/csv
/admin/org/reportes
/admin/mina/csv
/admin/desplazamiento/csv
/admin/evento_c/
/admin/evento_c/reporte_eventos.xls

###CONSULTA
consulta/csv
consulta/pdf
consulta/resumen/desplazamiento
consulta/resumen/mina

###GRAFICAS FLASH
/chart-data.php
/chart-data_v2.php

###MAPSERVER
/consulta/test_mapserver/test_map_area_depto.map
/consulta/test_mapserver/test_map_area_mpio.map
/images/cache_mapserver/consulta/
/images/cache_mapserver/perfil/

###LOGS
/_slog/*

###PERFILES
perfiles/

###STATIC
static/
