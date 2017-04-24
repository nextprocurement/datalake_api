<?php
// Provisional
header ("Location: APIDocs/");
include "../phplib/globals.inc.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="x-ua-compatible" content="IE=edge">
        <title>ELIXIR Benchmarking platform. Data Store API</title>
        <link rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="images/favicon-16x16.png" sizes="16x16" />
        <link href='css/typography.css' media='screen' rel='stylesheet' type='text/css'/>
        <link href='css/reset.css' media='screen' rel='stylesheet' type='text/css'/>
        <link href='css/screen.css' media='screen' rel='stylesheet' type='text/css'/>
        <link href='css/reset.css' media='print' rel='stylesheet' type='text/css'/>
        <link href='css/print.css' media='print' rel='stylesheet' type='text/css'/>

        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/custom.css">


        <script src='lib/object-assign-pollyfill.js' type='text/javascript'></script>
        <script src='lib/jquery-1.8.0.min.js' type='text/javascript'></script>
        <script src='lib/jquery.slideto.min.js' type='text/javascript'></script>
        <script src='lib/jquery.wiggle.min.js' type='text/javascript'></script>
        <script src='lib/jquery.ba-bbq.min.js' type='text/javascript'></script>
        <script src='lib/handlebars-4.0.5.js' type='text/javascript'></script>
        <script src='lib/lodash.min.js' type='text/javascript'></script>
        <script src='lib/backbone-min.js' type='text/javascript'></script>
        <script src='js/swagger-ui.js' type='text/javascript'></script>
        <script src='lib/highlight.9.1.0.pack.js' type='text/javascript'></script>
        <script src='lib/highlight.9.1.0.pack_extended.js' type='text/javascript'></script>
        <script src='lib/jsoneditor.min.js' type='text/javascript'></script>
        <script src='lib/marked.js' type='text/javascript'></script>
        <script src='lib/swagger-oauth.js' type='text/javascript'></script>

        <!-- Some basic translations -->
        <!-- <script src='lang/translator.js' type='text/javascript'></script> -->
        <!-- <script src='lang/ru.js' type='text/javascript'></script> -->
        <!-- <script src='lang/en.js' type='text/javascript'></script> -->

        <script type="text/javascript">
            $(function () {
                var url = window.location.search.match(/url=([^&]+)/);
                url = "<?php print $GLOBALS['baseURL']?>/benchAPI.json";                
                hljs.configure({
                    highlightSizeThreshold: 5000
                });

                // Pre load translate...
                if (window.SwaggerTranslator) {
                    window.SwaggerTranslator.translate();
                }
                window.swaggerUi = new SwaggerUi({
                    url: url,
                    dom_id: "swagger-ui-container",
                    supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                    onComplete: function (swaggerApi, swaggerUi) {
                        if (typeof initOAuth == "function") {
                            initOAuth({
                                clientId: "your-client-id",
                                clientSecret: "your-client-secret-if-required",
                                realm: "your-realms",
                                appName: "your-app-name",
                                scopeSeparator: " ",
                                additionalQueryStringParams: {}
                            });
                        }

                        if (window.SwaggerTranslator) {
                            window.SwaggerTranslator.translate();
                        }
                    },
                    onFailure: function (data) {
                        log("Unable to Load SwaggerUI");
                    },
                    docExpansion: "none",
                    jsonEditor: false,
                    defaultModelRendering: 'schema',
                    showRequestHeaders: false,
                    showOperationIds: false
                });

                window.swaggerUi.load();

                function log() {
                    if ('console' in window) {
                        console.log.apply(console, arguments);
                    }
                }
            });
        </script>
    </head>

    <body class="swagger-section">
        <div class="container-fluid">
            <nav class="navbar navbar-inverse">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="http://elixir-europe.org"><img src="http://www.elixir-europe.org/global/images/elixir-logo-transparent-ultrasmall.png" alt="Elixir"/></a>

                    </div>
                    <div id="navbar" class="collapse navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="home.htm">Home</a></li>
                            <li><a href="Community/search.html">Communities</a></li>
                            <li><a href="help">Rest API</a></li>
                            <li><a href="about">About</a></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </nav>
            <div id="ElixirHeader">
                <h1>ELIXIR</h1>
                <h2>Benchmarking platform</h2>
            </div>

            <div class="panel" style="padding-left:10px">
            <div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
            <div id="swagger-ui-container" class="swagger-ui-wrap"></div>
            </div>

            <div class="row quicklinks nomargin">
                <div class="col-md-4">
                    <h4>Quick links</h4>
                </div>
            </div>
            <div class="row  bg-info nomargin">

                <div class="col-md-2 col-md-offset-1">
                    <h4>Benchmarking</h4>
                    <ul class="list-unstyled">
                        <li><a href="Community.html">Communities</a></li>
                        <li><a href="BenchmarkingEvent.html">Events</a></li>
                        <li><a href="Tool.html">Tools</a></li>
                        <li><a href="Dataset.html">Datasets</a></li>
                        <li><a href="Metrics.html">Metrics</a></li>
                        <!--            <li><a href="/BenchmarkingStoreAPI#/info.html">Statistics</a></li>-->
                    </ul>
                </div>
                <div class="col-md-2">
                    <h4>Tools</h4>
                    <ul class="list-unstyled">
                        <li><a href="http://bio.tools">ELIXIR Registry</a></li>
                        <li><a href="/elixibilitas">Tools monitoring</a></li>
                        <li><a href="#">Tools statistics</a></li>
                        <li><a href="#">Web services registry</a></lI>
                    </ul>
                </div>
                <div class="col-md-7">
                    <h4><a href="about.html">About us</a></h4>
                    <div class="panel panel-collapse">
                        <img src="images/IRBCN_1Vert_ANG_CMYK_10YEARS.png" width="40">
                        <img src="images/cnio.gif" width="110">

                        <img src="images/SIB_LogoQ_GBv.png" width="100">
                        <img src="images/logoBSC_es.jpg" width="100">
                        <img src="images/logo_CRG.png" width="60">
                        <img src="images/logo_inb.gif" width="60">

                        <img src="images/ELIXIR_logo.png" style="margin-left:10px" align="right">
                    </div>
                </div>
            </div>
            <footer>
                <div class="footer">
                    <img src="http://elixir-europe.org/global/images/eu-flag.jpg" alt="EU flag" style="float:left; width: 46px; margin: 3px 10px 0 20px;" width="45">
                    ELIXIR is partly funded by the European Commission within the Research Infraestructure programme of Horizon 2020.<br>
                    ELIXIR Excelerate is funded by the European Comission, contract no. 676559
                </div>
            </footer>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="##baseURL##/js/bootstrap.min.js"></script>

    </body>
</html>
