<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{title}</title>
        <link type="text/css" href="{THEME}/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link type="text/css" href="{THEME}/bootstrap/css/simadeco.css" rel="stylesheet">
        <link type="text/css" href="{THEME}/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
        <script src="{THEME}/scripts/pace.min.js"></script>

        <script type="text/javascript">

            
            Pace.on("start", function() {
                //$("#loadingOverlay").show();
            });

            Pace.on("done", function() {
                $("#loadingOverlay").fadeOut(500);
            });
            function sourceDeferedLoad() {
                var jsList = [
                    "{THEME}/scripts/jquery-ui-1.10.1.custom.min.js"
                            , "{THEME}/bootstrap/js/bootstrap.min.js"
                            , "{THEME}/scripts/jquery-1.9.1.min.js"
                            , "{THEME}/scripts/form.js"
                            //[module=articles]
                            , "{THEME}/scripts/flot/jquery.flot.js"
                            , "{THEME}/ckeditor/ckeditor.js"
                            //, "{THEME}/scripts/tooltipsy.min.js"
                            //[/module]
                            
                            //[module=listview]
                            , "{THEME}/scripts/datatables/jquery.dataTables.js"
                            //[/module]                            

                ];
                var cssList = [
                    "{THEME}/css/theme.prod.css"
                ];
                var i;
                for (i = 0; i < jsList.length; ++i) {
                    var element = document.createElement("script");
                    element.src = jsList[i];
                    document.body.appendChild(element);
                    console.debug('Loaded: ' + jsList[i]);
                }

                for (i = 0; i < cssList.length; ++i) {
                    if (document.createStyleSheet)
                        document.createStyleSheet(src);
                    else {
                        var stylesheet = document.createElement('link');
                        stylesheet.href = cssList[i];
                        stylesheet.rel = 'stylesheet';
                        stylesheet.type = 'text/css';
                        document.getElementsByTagName('head')[0].appendChild(stylesheet);
                    }
                }
                $(window).unload(function(){
                  alert("Goodbye!");
                });
            }

            if (window.addEventListener)
                window.addEventListener("load", sourceDeferedLoad, false);
            else if (window.attachEvent)
                window.attachEvent("onload", sourceDeferedLoad);
            else
                window.onload = sourceDeferedLoad;

        </script>

    </head>
    <body>
        <div id="loadingOverlay"
             style="   
             background:#FFF url('data:image/gif;base64,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') no-repeat center center;
             height: 100%;
             width: 100%;
             position: fixed;
             z-index: 1031;
             left: 0%;
             top: 0%;
             margin: 0;
             opacity: 0.5;
             "
             >&nbsp;</div>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                        <i class="icon-reorder shaded"></i></a><a class="brand" href="{HOME_DIR}/{ADMIN_FILE}" title="SIMAdeco - Panel de administración">SIMAdeco</a>
                    [navbar]
                    <div class="nav-collapse collapse navbar-inverse-collapse">
                        <ul class="nav nav-icons">
                            <li class="active"><a href="#"><i class="icon-envelope"></i></a></li>
                            <li><a href="#"><i class="icon-eye-open"></i></a></li>
                            <li><a href="#"><i class="icon-bar-chart"></i></a></li>
                        </ul>
                        <form class="navbar-search pull-left input-append" action="#">
                            <input type="text" class="span3">
                            <button class="btn border" type="button">
                                <i class="icon-search"></i>
                            </button>
                        </form>
                        <ul class="nav pull-right">
                            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Opciones
                                    <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{HOME_DIR}/{ADMIN_FILE}/?do=articles&amp;action=create">Nuevo artículo</a></li>
                                    <li class="divider"></li>
                                    <li class="nav-header">Más opciones</li>
                                    <li><a href="{HOME_DIR}/{ADMIN_FILE}/?do=sysconfig">Configuración del sistema</a></li>
                                </ul>
                            </li>
                            <li><a href="{HOME_DIR}" target="_blank">Página Principal</a></li>
                            <li class="nav-user dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="{THEME}/images/user.png" class="nav-avatar" alt="" />
                                    <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Your Profile</a></li>
                                    <li><a href="#">Edit Profile</a></li>
                                    <li><a href="#">Account Settings</a></li>
                                    <li class="divider"></li>
                                    <li><a href="?admin_debug=false">Salir</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    [/navbar]            
                    <!-- /.nav-collapse -->
                </div>
            </div>
            <!-- /navbar-inner -->
        </div>
        <!-- /navbar -->
        <div class="wrapper">
            <div class="container">

                <div class="row">
                    {sidebar}
                    <div class="{content-align}">
                        <div class="content">
                            {info}
                            {content}
                        </div>
                        <!--/.content-->
                    </div>
                    <!--/.span9-->
                </div>
            </div>
            <!--/.container-->
        </div>
        <!--/.wrapper-->
        <div class="footer">
            <div class="container">
                <b class="copyright">
                    &copy; 2014 SIMAdeco - Mher.es</b> Todos los derechos reservados.
                <br /><b class="copyright">&copy; 2014 Edmin - EGrappler.com </b>Todos los derechos reservados. 
            </div>
        </div>
        <script src="{THEME}/scripts/jquery-1.9.1.min.js"></script>




        [module=listview]
        <script src="{THEME}/scripts/datatables/jquery.dataTables.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.datatable-1').dataTable();
                $('.dataTables_paginate').addClass("btn-group datatable-pagination");
                $('.dataTables_paginate > a').wrapInner('<span />');
                $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
                $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
            });
        </script>
        [/module]

        [module=other]
        <script src="{THEME}/scripts/flot/jquery.flot.min.js" type="text/javascript"></script>
        <script src="{THEME}/scripts/flot/jquery.flot.resize.js" type="text/javascript"></script>
        <script src="{THEME}/scripts/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="{THEME}/scripts/common.js" type="text/javascript"></script>
        [/module]
    </body>
</html>
