<html>
<head>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
 $(document).ready(function() {
 	 $("#responsecontainer").load("http://www.spia.nl/curl/response.php");
   var refreshId = setInterval(function() {
      $("#responsecontainer").load('response.php?randval='+ Math.random());
   }, 9000);
   $.ajaxSetup({ cache: false });
});
</script>
</head>
<body>
<div id="responsecontainer">
<?php

// set the url of the page you would have previously linked to in the iframe

$url = 'https://docs.google.com/spreadsheets/d/1z-xa4-FTljjHLBhSq-QvIuMJVngHOmUSk7Kv9wuTV48/pubhtml?single=true&gid=1871218&range=A1:O38&widget=false&chrome=false';

// Setup the new css you want to inject into the page 
$css = '
<style type="text/css">

#embed_1551578144 {
border:0px !important;
}

#embed_81817997 {
border:0px !important;
}

#embed_2121991091 {
border:0px !important;
}

#embed_338691710 {
border:0px !important;
}

#embed_338691710 img {
height:65px !important;
}

::-webkit-scrollbar {
display:none !important;
}

</style>
';

// Get the file contents (you may want to replace this with a curl request 
$site_content = file_get_contents($url);

// a simple way to inject style into this page would be to ad it directly above the closing head tag (if there is one) 
// this can be changed to any element, or even using the dom class you could ammend this with more detail. 
$site_content = str_replace('</head>', $css.'</head>', $site_content);

// you may also need to inject a base href tag so all the links inside are still correct
// comment out the next line if not needed 
$site_content = str_replace('<head>', '<head><base href="'.$url.'" />', $site_content);

// return the site contents to the browser
echo $site_content;
?>
</div>
</body>
</html>