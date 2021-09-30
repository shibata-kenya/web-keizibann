<head>
    <meta charset="UTF-8">
    <title>WEB掲示板_shiba</title>
</head>
<body>
     <!--タイトル表示-->
     <div style="font-size:40px; background-color:lightskyblue">
         <strong>--WEB掲示板--</strong>
     </div>
     <hr>
     
     <?php
         //___データ接続___
         $dsn='データベース名';
         $user='ユーザー名';
         $password='パスワード';
         $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
         
         //___データベース内にテーブルを作成___
         $sql="CREATE TABLE IF NOT EXISTS keizibann"
             ."("
             ."id INT AUTO_INCREMENT PRIMARY KEY,"
             ."name char(32),"
             ."comment TEXT,"
             ."time TIMESTAMP,"
             ."pass char(32)"
             .");";
         $stmt=$pdo->query($sql);
         
         //___フォームから送信された値を変数に代入___
         // from新規投稿フォーム「名前」
         if(isset($_POST["name"])){
             $a_name=$_POST["name"];
         }
         // from新規投稿フォーム「コメント」
         if(isset($_POST["comment"])){
             $a_com=$_POST["comment"];
         }
         // from新規投稿フォーム「パスワード」
         if(isset($_POST["c_password"])){
             $a_c_pass=$_POST["c_password"];
         }
         // from削除フォーム「削除対象番号」
         if(isset($_POST["delete"])){
             $delete=$_POST["delete"];
         }
         // from削除フォーム「パスワード」
         if(isset($_POST["d_password"])){
             $d_pass=$_POST["d_password"];
         }
         // from編集選択フォーム「編集対象番号」
         if(isset($_POST["edit"])){
             $edit=$_POST["edit"];
         }
         // from編集選択フォーム「パスワード」
         if(isset($_POST["e_password"])){
             $e_pass=$_POST["e_password"];
         }
         
         //___新規投稿機能を付ける___
         //投稿(編集実行)フォームに名前、コメント、パスワードがセットされており
         //編集対象番号がセットされていない場合
         if(isset($a_name) && isset($a_com) && isset($a_c_pass)){
             if(empty($_POST["edited_num"])){
                 $sql = $pdo -> prepare("INSERT INTO keizibann (name, comment, time, pass) VALUES (:name, :comment, :time, :pass)");
                 $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                 $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                 $sql -> bindParam(':time', $time, PDO::PARAM_STR);
                 $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                 $name=$a_name;
                 $comment=$a_com;
                 $time=date("Y/m/d H:i:s");
                 $pass=$a_c_pass;
                 $sql -> execute();
             }else{
                 //___編集実行機能を付ける___
                 //投稿(編集実行)フォームから名前、コメント、パスワードがセットされており
                 //編集対象番号もセットされている場合
                 $id=$_POST["edited_num"]; 
                 $name=$a_name;
                 $comment=$a_com;
                 $time=date("Y/m/d H:i:s");
                 $pass=$a_c_pass; 
                 $sql='UPDATE keizibann SET name=:name,comment=:comment,time=:time,pass=:pass WHERE id=:id';
                 $stmt=$pdo->prepare($sql);
                 $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                 $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                 $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                 $stmt->bindParam(':time', $time, PDO::PARAM_STR);
                 $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                 $stmt->execute();
             }
         }
         
         //___削除機能を付ける___
         //削除対象番号、パスワードがセットされている場合
         if(isset($delete) && isset($d_pass)){
             //データレコードを抽出
             $sql='SELECT * FROM keizibann';
             $stmt=$pdo->query($sql);
             $result=$stmt->fetchALL();
             foreach($result as $row){
                 //idと削除対象番号が一致した場合
                 if($row['id']==$delete){
                     //新規投稿時に設定したパスワードと
                     //削除フォームで入力したパスワードが一致した場合
                     if($row['pass']==$d_pass){
                         $id=$delete;
                         $sql='DELETE FROM keizibann WHERE id=:id';
                         $stmt=$pdo->prepare($sql);
                         $stmt->bindParam(':id',$delete,PDO::PARAM_INT);
                         $stmt->execute();
                     }
                 }
             }
         }
         
         //___編集対象選択機能を付ける___
         //編集対象番号、パスワードがセットされている場合
         if(isset($edit) && isset($e_pass)){
             //データレコードを抽出
             $sql='SELECT * FROM keizibann';
             $stmt=$pdo->query($sql);
             $result=$stmt->fetchALL();
             foreach($result as $row){
                 //idと編集対象番号が一致した場合
                 if($row['id']==$edit){
                     //新規投稿時に設定したパスワードと
                     //編集番号選択フォームで入力したパスワードが一致した場合
                     if($row['pass']==$e_pass){
                         $e_id=$row['id'];
                         $e_name=$row['name'];
                         $e_com=$row['comment'];
                     }
                 }
             }    
         }
     ?>
     
     <!--投稿(編集)フォーム作成-->
     <form action="" method="post">
         投稿<br>
         <input type="text" name="name" placeholder="名前"
         value="<?php  
         //表示される条件を追加(パスワードが送信&パスワード一致)
         if(isset($edit) && isset($e_pass)){
             //データレコードを抽出
             $sql='SELECT * FROM keizibann';
             $stmt=$pdo->query($sql);
             $result=$stmt->fetchALL();
             foreach($result as $row){
                 //idと編集対象番号が一致した場合
                 if($row['id']==$edit){
                     //新規投稿時に設定したパスワードと
                     //編集番号選択フォームで入力したパスワードが一致した場合
                     if($row['pass']==$e_pass){
                         echo $e_name;
                     }
                 }
             }    
         } ?>">
        <br>
        <input type="text" name="comment" placeholder="コメント"
         value="<?php  
         //表示される条件を追加(パスワードが送信&パスワード一致)
         if(isset($edit) && isset($e_pass)){
             //データレコードを抽出
             $sql='SELECT * FROM keizibann';
             $stmt=$pdo->query($sql);
             $result=$stmt->fetchALL();
             foreach($result as $row){
                 //idと編集対象番号が一致した場合
                 if($row['id']==$edit){
                     //新規投稿時に設定したパスワードと
                     //編集番号選択フォームで入力したパスワードが一致した場合
                     if($row['pass']==$e_pass){
                         echo $e_com;
                     }
                 }
             }    
         } ?>">
        <input type="hidden" name="edited_num"
         value="<?php  
         //表示される条件を追加(パスワードが送信&パスワード一致)
         if(isset($edit) && isset($e_pass)){
             //データレコードを抽出
             $sql='SELECT * FROM keizibann';
             $stmt=$pdo->query($sql);
             $result=$stmt->fetchALL();
             foreach($result as $row){
                 //idと編集対象番号が一致した場合
                 if($row['id']==$edit){
                     //新規投稿時に設定したパスワードと
                     //編集番号選択フォームで入力したパスワードが一致した場合
                     if($row['pass']==$e_pass){
                         echo $e_id;
                     }
                 }
             }    
         } ?>">
         <br>
        <input type="password" name="c_password" placeholder="パスワード">
        <input type="submit" value="投稿">
     </form>
     
     <!--削除フォーム作成-->
     <form action="" method="post">
         削除<br>
         <input type="text" name="delete" placeholder="削除対象番号">
         <br>
         <input type="password" name="d_password" placeholder="パスワード">
         <input type="submit" value="削除">
     </form>
     
     <!--編集番号指定用フォーム作成-->
     <form action="" method="post">
         編集選択<br>
         <input type="text" name="edit" placeholder="編集対象番号">
         <br>
         <input type="password" name="e_password" placeholder="パスワード">
         <input type="submit" value="編集選択">
     </form>
     <br>
     
     <?php
         //___表示機能を付ける___
         $sql='SELECT * FROM keizibann';
         $stmt=$pdo->query($sql);
         $result=$stmt->fetchALL();
         foreach($result as $row){
             echo $row['id'].', ';
             echo $row['name'].', ';
             echo $row['time'].'<br>';
             echo $row['comment'].'<br>';
             echo "<hr>";
         }
     ?> 
</body> 