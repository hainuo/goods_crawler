<?php
require 'vendor/autoload.php';

use QL\QueryList;
use think\Db;

session_start();

function goUrl($msg, $url)
{
    echo $msg;
    echo '<script>
        setTimeout(function() {
          location.href="' . $url . '"    
          },3000)
</script>';
}

$ajax = isset($_REQUEST['is_ajax']) ? $_REQUEST['is_ajax'] : '0';
$category_id = isset($_REQUEST['category_id'])?intval($_REQUEST['category_id']):1;
if($category_id<0){
    $category_id=1;
}
if (!empty($_POST)) {
    $dbname = $_POST['database'];
    $dbhost = $_POST['hostname'];
    $dbport = $_POST['hostport'];
    $dbusername = $_POST['username'];
    $dbpwd = $_POST['password'];
    $dbpre = $_POST['prefix'];
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
        'category_id'=> ($category_id>0)?$category_id:1
    ];
    $_SESSION['dbconfig'] = $dbconfig;
}
if(isset($_SESSION['dbconfig'])){
    $dbconfig = $_SESSION['dbconfig'];
}
if (!isset($ajax) || $ajax !== '1' || empty($dbconfig['hostname']) || empty($dbconfig['database']) || empty($dbconfig['username']) || empty($dbconfig['hostport']) || empty($dbconfig['password']) || empty($dbconfig['prefix'])) {
    goUrl('数据库数据有误，即将跳转到表单页面', 'youzan.php');
    exit();

} else {
//    var_dump($dbconfig);
    $goodsLinks = $_POST['goodsLink'];
    $goodsLinks = explode("\n",$goodsLinks);
    dump($goodsLinks);
    foreach ($goodsLinks as &$goodsLink) {
        if (empty($goodsLink) || (strpos($goodsLink, 'https://') === false && strpos($goodsLink, 'http://') === false)) {
//            goUrl('商品链接有问题，无法处理数据', 'youzan.php');
//            exit();
            continue;
        }
//    var_dump($goodsLink);
//    $goodsLink='https://detail.youzan.com/show/goods?alias=3f2ul91w405rx&banner_id=t.87733344~goods.2~6~WNrFVGtN&components_style_size=1&reft=1555300491366_1555300496249&spm=f.70430959_t.87733344';
        ignore_user_abort(1);
        set_time_limit(0);
        try {
            $data = QueryList::get($goodsLink)->rules([
                'title' => ['h3.goods-title', 'text'],
                'xiangce' => ['.swiper-image img', 'src'],
                'xiangqing' => ['.rich-text img', 'src'],
                'price' => ['.goods-current-price.pull-left', 'html', '-em']
            ])->query()->getData()->all();
        } catch (\Exception $e) {
            dump($e->getMessage());
            dump($e->getTrace());
            exit;
        }

        if (empty($data)) {
            goUrl('未获取到内容，请使用其他链接', 'youzan.php');
            exit;
        }
        $title = [];
        $xiangce = [];
        $xiangqing = [];
        $price = [];
        foreach ($data as $value) {
            if (isset($value['title'])) {
                $title[] = $value['title'];
            }

            if (isset($value['xiangce'])) {
                $xiangce[] = $value['xiangce'];
            }

            if (isset($value['xiangqing'])) {
                $xiangqing[] = '<p><img src="' . $value['xiangqing'] . '" title="' . basename($value['xiangqing']) . '" alt="' . basename($value['xiangqing']) . '"/></p>';
            }

            if (isset($value['price'])) {
                $price[] = $value['price'];
            }


        }

        if (count($title) == 1) {
            $title = $title[0];
        } else {
            $title = implode('', $title);
        }
        $price = $price[0];
        if (count($xiangce) > 1) {
            unset($xiangce[0]);
            $thumb = $xiangce[1];
        } else {
            $thumb = $xiangce[0];
        }
//    dump($dbconfig);
        try {

            //设置数据库连接
            Db::setConfig($dbconfig);

            //构造商品表数据
            //CREATE TABLE `osc_goods` (
            //  `goods_id` int(11) NOT NULL AUTO_INCREMENT,
            //  `category_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类id',
            //  `type` int(10) NOT NULL DEFAULT '1' COMMENT '1普通商品，2特价',
            //  `name` varchar(64) DEFAULT NULL,
            //  `filter` text COMMENT '筛选',
            //  `pro_no` varchar(40) DEFAULT NULL COMMENT '货号',
            //  `location` varchar(20) DEFAULT NULL COMMENT '产品所在地',
            //  `quantity` int(4) NOT NULL DEFAULT '0' COMMENT '商品数目',
            //  `sale_count` int(11) NOT NULL DEFAULT '0' COMMENT '销量',
            //  `comment_num` int(8) NOT NULL DEFAULT '0' COMMENT '评论数量',
            //  `comment_score` int(11) NOT NULL DEFAULT '0' COMMENT '评论总分',
            //  `image` varchar(64) DEFAULT NULL,
            //  `brand_id` int(11) NOT NULL DEFAULT '0' COMMENT '品牌编号（关联brand主键）',
            //  `shipping` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否需要运送',
            //  `market_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '市场价',
            //  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
            //  `points` int(10) NOT NULL DEFAULT '0' COMMENT '购买商品获得的积分',
            //  `pay_points` int(10) NOT NULL DEFAULT '0' COMMENT '兑换需要的积分',
            //  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
            //  `sale_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '特价(针对特价商品)',
            //  `sale_start_time` varchar(40) DEFAULT NULL COMMENT '特价开启时间段',
            //  `sale_end_time` varchar(40) DEFAULT NULL COMMENT '特价结束时间段',
            //  `sale_buy_limit` varchar(40) DEFAULT NULL COMMENT '限购数量',
            //  `default_gallery` varchar(500) DEFAULT NULL COMMENT '默认图册',
            //  `weight` decimal(15,8) NOT NULL DEFAULT '0.00000000' COMMENT '重量',
            //  `weight_class_id` smallint(5) NOT NULL DEFAULT '0' COMMENT '重量编号（关联weight_class主键）',
            //  `length` decimal(15,8) NOT NULL DEFAULT '0.00000000',
            //  `width` decimal(15,8) NOT NULL DEFAULT '0.00000000',
            //  `height` decimal(15,8) NOT NULL DEFAULT '0.00000000',
            //  `length_class_id` smallint(5) NOT NULL DEFAULT '0',
            //  `subtract` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否扣除库存，0否，1是',
            //  `minimum` int(11) NOT NULL DEFAULT '1' COMMENT '最小起订数目',
            //  `sort_order` int(11) NOT NULL DEFAULT '0',
            //  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
            //  `free_shipping` int(5) NOT NULL DEFAULT '0' COMMENT '免运费，1是，0否',
            //  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '加入时间',
            //  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改的时间',
            //  `viewed` int(5) NOT NULL DEFAULT '0' COMMENT '点击量',
            //  PRIMARY KEY (`goods_id`)
            //) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='商品信息表';

            $goodsInfo = [
                'name' => $title,
                'type' => 1,
                'quantity' => rand(99, 999),
                'sale_count' => rand(5, 99),
                'market_price' => $price,
                'price' => $price,
                'points' => 1,
                'default_gallery' => serialize($xiangce),
                'subtract' => 1,
                'sort_order' => 99,
                'free_shipping' => 1,
                'create_time' => time(),
                'update_time' => time(),
                'viewed' => rand(10, 999),
                'category_id' => $dbconfig['category_id'],
                'brand_id' => 0,
                'status' => 1,
                'image' => $thumb,//因为需要修改原程序所以此处不做处理
            ];
//        dump($goodsInfo);exit;
//        dump($xiangqing);
            $goodsId = Db::name('goods')->insertGetId($goodsInfo);
            //处理相册内容
            $gallery = [];
            foreach ($xiangce as $value) {
                $gallery[] = [
                    'goods_id' => $goodsId,
                    'value_name' => '默认',
                    'image' => $value
                ];
            }
            if (!empty($gallery)) {
                Db::name('goods_gallery')->insertAll($gallery);
            }
            Db::name('goods_description')->insert([
                'goods_id' => $goodsId,
                'description' => implode(' ', $xiangqing)
            ]);

        } catch (\think\Exception $e) {
            dump($e->getMessage());
            dump($e->getTraceAsString());
            continue;
        }
    }
    goUrl('添加成功！','youzan.php');
    exit;


}
