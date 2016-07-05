<script type="text/javascript">
$(function(){ 

    $('.page:not(:first)').hide();

    $('ul.nav > li > a ').click(function(){ 
        $('.page').hide();
        $('#' + $(this).data('page')).show();

        return false;
    });
});
</script>


<div id="perfilv2">
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><?php echo $info['nombre_ubicacion'] ?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#" data-page="gen"><i class="fa fa-area-chart"></i> Contexto general</a></li>
        <li class=""><a href="#" data-page="hum"><i class="fa fa-street-view"></i> Contexto humanitario</a></li>
        <li class=""><a href="#" data-page="sector"><i class="fa fa-street-view"></i> Contexto por sector</a></li>
        <li class=""><a href="#" data-page="des"><i class="fa fa-street-view"></i> Contexto desarrollo</a></li>
    </ul>
    </div>
</nav>

<div class="container-fluid">

    <div id="gen" class="page row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="x_panel tile">
                <div class="x_title">
                    <h2>Datos generales</h2>
                    <div class="clearfix"></div>
                </div>
                <?php echo $info['generales']['iz'] ?>
            </div>
        </div>
    </div>
    <div id="hum" class="page row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="x_panel tile">
                <div class="x_title">
                    <h2>Necesidades Humanitarias</h2>
                    <div class="clearfix"></div>
                </div>
                <?php echo $info['hum_necesidades'] ?>
            </div>
        </div>
        <div class="col-md-5 col-sm-6 col-xs-12">
            <div class="x_panel tile">
                <div class="x_title">
                    <h2>Impacto Humanitario - Violencia</h2>
                    <div class="clearfix"></div>
                </div>
                <?php echo $info['hum_impacto_violencia'] ?>
            </div>
            
                <div class="x_panel tile">
                <div class="x_title">
                    <h2>Impacto Humanitario - Desastres</h2>
                    <div class="clearfix"></div>
                </div>
                <?php echo $info['hum_impacto_desastres'] ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="x_panel tile">
                <div class="x_title">
                    <h2>Respuesta Humanitaria</h2>
                    <div class="clearfix"></div>
                </div>
                <?php echo $info['hum_respuesta'] ?>
            </div>
        </div>
    </div>

                <!-- footer content -->

                <footer>
                    <div class="">
                        <p class="pull-right">Gentelella Alela! a Bootstrap 3 template by <a>Kimlabs</a>. |
                            <span class="lead"> <i class="fa fa-paw"></i> Gentelella Alela!</span>
                        </p>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
            </div>
            <!-- /page content -->

        </div>

    </div>

    <script>
        NProgress.done();
    </script>
    <!-- /datepicker -->
    <!-- /footer content -->
</div>
</div>

