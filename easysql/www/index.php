<?php
header("Content-Type: text/html;charset=utf-8");
highlight_file('sql.txt');
echo '<br><br>';

$hostip = "172.17.0.1";//docker0 ip
$port = 4000;
$username = "easysql";
$password = "fa3f0baead05bca30d3d7f63caad7b1c";
$database = "easysql";


if (isset($_POST['id'])) {

    waf($_POST['id']);
    $db = new mysqli($hostip . ":" . $port, $username, $password, $database);

    if (mysqli_connect_errno()) { #检查连接
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    $query = "SELECT * FROM users where id='" . $_POST['id'] . "'";
    $result = $db->query($query);
    $row = $result->fetch_all(MYSQLI_BOTH);
    #var_dump($row);
    $db->close();

    echo "用户名: " . $row[0]['username'];
    echo "<br />";
    echo "密码: " . $row[0]['password'];
}

function waf($str)
{
    $filter = ' |outfile|readfile|;|load_file|\"|sleep|delete|insert|update|database|user|information_schema';
    if (preg_match('/' . $filter . '/i', $str)) {
        exit('hacker!');
    }
}

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<h1>用户信息查询</h1>
<span>我才不会告诉你可以用select flag from flag看到flag呢！</span>
<form action="index.php" method="post">
    <p>id: <input type="text" name="id"/></p>
    <input type="submit" value="Submit"/>
</form>
</html>
