<!DOCTYPE html>
<html>
    <head>
        <title>Image https://images.rbg.vic.gov.au/cip/asset/download/public/60930 | Image Service | Atlas</title>
        <style>
        html, body {
            height:100%;
            padding: 0;
            margin:0;
        }
        #imageViewerContainer {
            height: 100%;
            padding: 0;
        }
        #imageViewer {
            width: 100%;
            height: 100%;
            margin: 0;
        }
        
        .powered-by-ala-image {
            position: absolute;
            width: 200px;
            bottom: 20px;
            right: 0;
        }

        </style>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://images.ala.org.au/assets/ala/images-client.css"/>
    </head>
    <body style="padding:0;">
        <div id="imageViewerContainer" class="container-fluid">
            <div id="imageViewer"> </div>
            <img src="https://avh.ala.org.au/assets/ALA-powered-by-logo-inline.png" alt="" class="powered-by-ala-image" />
        </div>
        <script type="text/javascript" src="https://images.ala.org.au/assets/head.js" ></script>
        <script type="text/javascript" src="<?=site_url()?>js/ala/images-client.min.js" ></script>
        <script>
            $(document).ready(function() {
                var options = {
                    auxDataUrl : "",
                    imageServiceBaseUrl : "https://images.ala.org.au",
                    imageClientBaseUrl : "https://images.ala.org.au"
                };
                imgvwr.viewImage($("#imageViewer"), "<?=$uuid ?>", "", "", options);
                
                $('body').on('load', '.leaflet-control-container', function() {
                    $('a[href^="https://images.ala.org.au"]').attr('target', '_blank');
                });
            });
        </script>
    </body>
</html>
