<!DOCTYPE html>
<html lang="en">
<head>
<title></title>
<meta charset="utf-8">
<meta name="author" content="Isaiah Cory">
<meta name="description" content="">
    
<link rel="stylesheet"
href="stylesheets/styles.css"
media="screen">

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
$(function() {
$( "#datepicker" ).datepicker({
dateFormat: "yy-mm-dd",
showOn: "button",
buttonImage: "imgdir/calendar2.gif",
buttonImageOnly: true
});
});
</script>

<!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
<![endif]-->
    
</head>