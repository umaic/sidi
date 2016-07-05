#!/bin/bash

java -jar yuicompressor-2.4.8.jar style/p4w_consulta.css -o style/p4w_consulta.min.css

java -jar yuicompressor-2.4.8.jar js/p4w/consulta.js -o js/p4w/consulta.min.js
java -jar yuicompressor-2.4.8.jar admin/js/p4w/url_parser.js -o admin/js/p4w/url_parser.min.js
