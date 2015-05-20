<?php

require_once("php/connectionVariables.php");
$connection = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="description" content="MEXPRESS is a web tool for the visualization of the TCGA expression, DNA methylation and clinical data, as well as the relationships between them.">
        <!-- START facebook open graph meta tags -->
        <meta property="og:title" content="MEXPRESS"/>
        <meta property="og:url" content="http://mexpress.be"/>
        <meta property="og:site_name" content="MEXPRESS"/>
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="http://mexpress.be/images/plot.png"/>
        <meta property="og:description" content="MEXPRESS is a web tool for the visualization of the TCGA expression, DNA methylation and clinical data, as well as the relationships between them."/>
        <!-- END facebook open graph meta tags -->
        <title>MEXPRESS</title>
        <link rel="icon" type="image/png" href="images/m_icon.png">
        <link rel="stylesheet" type="text/css" href="css/global.css" />
        <link rel="stylesheet" type="text/css" href="css/mexpress.css" />
        <script type="text/javascript" src="javascript/d3.v3.js"></script>
        <script type="text/javascript" src="javascript/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="javascript/jquery.autocomplete.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:600' rel='stylesheet' type='text/css'>
        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script type="text/javascript">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-54184541-2']);
            _gaq.push(['_trackPageview']);
            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
        </script>
    </head>
    <body>
        <div class="container">
            <header>
                <div class="bannerContent">
                    <a href="/"><img class="logo" src="images/m.png" /></a>
                    <div class=bannerText>
                        <a href="/" class="bannerTitle">MEXPRESS</a>
                        <div class="separator"></div>
                        <a href="about" class="bannerLink">How to use MEXPRESS</a>
                    </div>
                </div>
            </header>
            <div class="mainContent">
                <div class="userInput">
                    <form id="userInputForm" action="javascript:checkUserInput();">
                        <div class="userInputSub">
                            <strong>Enter a gene symbol or Ensembl id:</strong><br>
                            <input type="text" id="gene" value="" />
                        </div>
                        <div id="sampleSelectionSub" class="userInputSub">
                            <div class="sampleSelectionContainer">
                                <strong>Select a <a href="https://tcga-data.nci.nih.gov/tcga/" target="_blank" title="This link will take you to the TCGA website.">TCGA</a> data source:</strong>
                                <div class="update">
                                    <?php
                                        $latest_update = file_get_contents("data/latest_update.txt");
                                        if ($latest_update != ""){
                                            echo "Latest update: $latest_update";
                                        }
                                    ?>
                                </div>
                                <div class="sampleSelectionList">
                                    <?php
                                        // show the source options to the user
                                        $query = "SELECT DISTINCT source, full_source_name FROM data_information";
                                        $result = mysqli_query($connection, $query);
                                        if (!$result){
                                            echo '<p><strong>Could not connect to the database!</strong></p>';
                                        }
                                        $tumorSources = array();
                                        while ($row = mysqli_fetch_assoc($result)){
                                            $source = $row['source'];
                                            $source = strtoupper($source);
                                            $resp = $row['full_source_name'];
                                            $tumorSources[$source] = $resp;
                                        }
                                        ksort($tumorSources);
                                        foreach($tumorSources as $key => $value){
                                            $fullSourceName = $value;
                                            if (strlen($fullSourceName) > 28){
                                                $fullSourceName = substr($fullSourceName, 0, 28);
                                                $fullSourceName = preg_replace('/ $/', "", $fullSourceName);
                                                $fullSourceName = $fullSourceName."...";
                                            }
                                            echo "<div class='sampleSelectionElement'><span class='sourceName'>$key</span> <span class='fullSourceName' title='$value'>$fullSourceName<span></div>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="plot" class="inputButton" onClick="_gaq.push(['_trackEvent', 'plotButton', 'click', '', 0, false]);"/>
                    </form>
                    <!--<div class="warningMessage">
                        <p>
                            Dear user,
                        </p>
                        <p>
                            We are currently experiencing some database problems.
                            We are doing our best to get MEXPRESS up and running again as soon as possible!
                            We apologize for the inconvenience!
                        </p>
                    </div>-->
                </div>
                <div class="plotContainer">
                    <div class="plotInfo"></div>
                    <div class="loadingAnimation"><img src="images/m.gif" /></div>
                    <div class="plotWindow hidden"></div>
                </div>
                <div class="clearFloat"></div>
            </div>
        </div>
        <footer>
            <p>
                <em>By downloading, analyzing, and/or utilizing TCGA data for publication purposes, the user accepts the data use restrictions and requirements as outlined in the TCGA Publication Guidelines.
                See <a href="http://cancergenome.nih.gov/abouttcga/policies/publicationguidelines" target="_blank">http://cancergenome.nih.gov/abouttcga/policies/publicationguidelines</a> for additional information.</em>
            </p>
            <p>
                Developed by Alexander Koch &#64; <a href="http://www.biobix.be" target="_blank">BIOBIX</a> &#8211; Ghent University
            </p>
            <p>
                Contact: <a href="mailto:alexander.koch@ugent.be?Subject=MEXPRESS">alexander.koch@ugent.be</a>
            </p>
            <p>
                <a href="http://mexpress.be">MEXPRESS</a> &#8211; 2015
            </p>
        </footer>
        <script type="text/javascript" src="javascript/general.js"></script>
        <script type="text/javascript" src="javascript/autocomplete.js"></script>
        <script type="text/javascript" src="javascript/sampleSelection.js"></script>
        <script type="text/javascript" src="javascript/downloadSvg.js"></script>
        <!--<script type="text/javascript" src="javascript/mexpress.min.js"></script>-->
        <script type="text/javascript" src="javascript/statistics.2.js"></script>
        <script type="text/javascript" src="javascript/methylationLinePlot.js"></script>
    </body>
</html>    