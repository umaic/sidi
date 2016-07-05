#!/bin/bash

# Departamentos
topojson -o deptos_topo.json deptos_geonode.json -q 1000 --id-property admin1Pcod -p admin1Name

# Municipios
#topojson -o mpios_topo.json mpios_geonode.json -q 1000 --id-property MUN_P_CODE -p MUNNAME
