<?php
//連線到本地資料庫
require_once("pdo-connect.php");

if(isset($_POST["account"])){
    $account=$_POST["account"];
    $name=$_POST["name"];
    $phone=$_POST["phone"];
    $address=$_POST["address"];
    $email=$_POST["email"];
}
//else{
//    exit();
//}
$now=date("Y-m-d H:i:s");

//插入使用者資料

//修改使用者資料
$sql="UPDATE users SET mailing_name='$name',mailing_phone='$phone',mailing_email='$email',mailing_address='$address'WHERE  account='$account'";
$stmt = $db_host->prepare($sql);
$stmt->execute();

//新增訂單
//存資料: 先抓出user的ID
$sql1="SELECT id FROM users WHERE account=?  AND valid=1";
$stmt1 = $db_host->prepare($sql1);
$stmt1->execute([$account]);
$row = $stmt1->fetch();
$id = $row["id"];

//將資料存入user_order
$sql2="INSERT INTO user_order (user_id,order_time,status) VALUES('$id','$now','1')";
$stmt2 = $db_host->prepare($sql2);
$stmt2->execute();
$orderID=$db_host->lastInsertId(); //取得user_order的ID，將存入orderID

//抓出購物車資料，存入order_detail
if (!empty($_SESSION["cart"])) {
    $cart = array();
    $cart = $_SESSION["cart"];
    foreach ($cart as $key => $value) {
        $productID = $value["id"];
        $amount = $value["num"];
        $sql3 = "INSERT INTO order_detail (order_id,product_id,amount) VALUES('$orderID','$productID','$amount')";
        $stmt3 = $db_host->prepare($sql3);
        $stmt3->execute();
    }
}
 //清除購物車
session_start();
ob_start();//清空緩存必須啓動的項
unset($_SESSION['cart']);


header("location: cart-shipping.php");