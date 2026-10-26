<?php
// Forward legacy upro.php requests to modern products.php preserving query string
$query_string = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: products.php" . $query_string);
exit();
?>
