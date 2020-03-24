<?php
if(isset($_GET['error'])){
	header('Location: error.php?error=' . $_GET['error']);
}
?>
<!DOCTYPE HTML>
<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
	<title>Order has been created.</title>
	<link rel="stylesheet" type="text/css" href="css/payu.css">
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>Order has been <span class="green">created</span></h1>
    </div>
    <div class="green padding">THANK YOU FOR PAYMENT</div>
</div>
</body>
</html>
