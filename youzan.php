<?php
session_start();
if (isset($_SESSION['dbconfig'])) {
    $dbconfig = $_SESSION['dbconfig'];
} else {
    $dbconfig = [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => empty($dbhost) ? '127.0.0.1' : $dbhost,
        // 数据库名
        'database' => empty($dbname) ? '' : $dbname,
        // 数据库用户名
        'username' => empty($dbusername) ? '' : $dbusername,
        // 数据库密码
        'password' => empty($dbpwd) ? '' : $dbpwd,
        // 数据库连接端口
        'hostport' => empty($dbport) ? '3306' : $dbport,
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => empty($dbpre) ? 'osc_' : $dbpre,
        'category_id' => (isset($category_id) && $category_id > 0) ? $category_id : 1
    ];
}
//var_dump($dbconfig);
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>获取有赞商品信息(仅图片标题名称版本)</title>
</head>
<body>
<h1>有赞商品抓取</h1>

<form method="post" action="index.php" enctype="application/x-www-form-urlencoded">
    <div class="form-group">
        <label for="exampleInputEmail1">有赞商品详情页链接</label>
        <textarea  class="form-control" name="goodsLink" rows="5" placeholder="http:// or https://"></textarea>

    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">商品分类id</label>
        <input type="text" class="form-control" name="category_id"  value="<?php echo $dbconfig['category_id'] ?>" placeholder="商品分类 ID">
    </div>
    <div class="form-row">
        <div class="col">
            <input type="text" name="hostname" class="form-control" value="<?php echo $dbconfig['hostname']; ?>"
                   placeholder="数据库地址">
        </div>
        <div class="col">
            <input type="text" name="hostport" class="form-control" value="<?php echo $dbconfig['hostport']; ?>"
                   placeholder="数据库端口">
        </div>
        <div class="col">
            <input type="text" name="prefix" class="form-control" value="<?php echo $dbconfig['prefix']; ?>"
                   placeholder="数据表前缀">
        </div>
    </div>
    <div class="form-row">
        <div class="col">
            <input type="text" name="database" class="form-control" value="<?php echo $dbconfig['database']; ?>"
                   placeholder="数据库名">
        </div>
        <div class="col">
            <input type="text" name="username" class="form-control" value="<?php echo $dbconfig['username']; ?>"
                   placeholder="用户名">
        </div>
        <div class="col">
            <input type="text" name="password" class="form-control" value="<?php echo $dbconfig['password']; ?>"
                   placeholder="密码">
        </div>
    </div>
    <input type="hidden" name="is_ajax" value="1">
    <button type="submit" class="btn btn-primary">提交</button>
</form>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>