<?php header ("Content-Type: text/html; charset=utf-8", true); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php echo meta_tag('content-type', 'text/html; charset=utf-8', true) ?>
<title><?php echo get_page_title() ?></title>
    <link href="../../library/dep/bootstrap-3.2.0.min.css"
                                                       rel="stylesheet">
</head>
<body>

<?php
echo $content_for_layout;
?>

</body>
</html>