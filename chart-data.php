<?
                include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );
                
                $g = new sidihChart();
                
                $g->title('Número de eventos por confrontación de actores');
                
                // label each point with its value
                $g->set_x_labels( array('EjÃ©rcito Nacional...','Fuerzas Armadas...','Sin Determinar...','EjÃ©rcito de...','EjÃ©rcito Nacional...','EjÃ©rcito Nacional...','EjÃ©rcito Nacional...','EjÃ©rcito Nacional...','Sin determinar...','PolicÃ­a |...','Fuerzas Armadas...','Delincuencia','Nuevos Grupos...','Bandas Emergentes...','PolicÃ­a |...') );
                $g->set_x_label_style( 8, '#000000',2);$bar = new bar(90, '#0066ff' );
$bar->data = array(235,180,97,31,29,12,11,11,10,9,9,7,5,4,3);
$g->data_sets[] = $bar;
                    $g->set_tool_tip( '#x_label# <br> #val# Eventos' );		
                    $g->set_y_max( 250 );
                    $g->y_label_steps(5);
                    //Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
                    $g->set_x_legend('Mes


',12);
                    
                    $g->set_y_legend('Número de Eventos',12);
                    
                    $g->set_num_decimals(0);echo $g->render();
                ?>