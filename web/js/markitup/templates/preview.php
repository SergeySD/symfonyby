<?php

require_once(dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'local', false);
sfContext::createInstance($configuration);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>markItUp! preview template</title>
<link rel="stylesheet" type="text/css" href="~/templates/preview.css" />
</head>
<body>
<?php
    $textile = new Textile();
    echo $textile->TextileThis(sfContext::getInstance()->getRequest()->getParameter('data'));
?>
</body>
</html>
