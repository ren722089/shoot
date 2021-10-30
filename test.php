<?php
$dsn = 'mysql:dbname=tb230565db;host=localhost';
$user = 'tb-230565';
$password = 'beSDhmFVAV';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
$sql = "CREATE TABLE IF NOT EXISTS tbtest"

    //データベースのカラムの定義、今までやった$name=と同じ役割を果たす。idは今までの投稿番号取得、dateは$date=ymdを自動で行ってくれる。
    . " ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date DATETIME DEFAULT CURRENT_TIMESTAMP,"
    . "password char(10)"
    . ");";

$stmt = $pdo->query($sql);


//削除フォーム作成
if (!empty($_POST["del"]) && !empty($_POST["pass2"])) {
    //削除処理
    $del = $_POST["del"];
    $pass2 = $_POST["pass2"];
    //$id=$delの順番が逆なだけで機能しなくなります。プログラムは上から下、左に変数右に代入する値を書かなければならないです。
    //
    $id = $del;
    $password = $pass2;
    var_dump($password);
    //whereでテーブル上の投稿番号（id）ANDパスワード(password)が一致している部分を探す。
    $sql = 'Delete from tbtest where id=:id AND password=:password';
    //prepareで$sql(table)に値をつけてexecuteの実行待ち
    $stmt = $pdo->prepare($sql);
    //bindparamで：idに変数（この場合$id)をINT（insert）する。
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //param_int と param_strについて→前者は数値として扱うため、数字番号の識別。後者は文字列として扱う。
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    //executeは上記の命令を実行するという意味→prepareで準備されたプログラムを実行してる。つまり、executeがなければ実行されない。
    $stmt->execute();
}


//編集フォーム作成(抽出；出力)
if (!empty($_POST["edit"]) && !empty($_POST["pass3"])) {
    $edit = $_POST["edit"];
    $pass3 = $_POST["pass3"];
    $id = $edit;
    $password = $pass3;
    //var_dump($id);
    //var_dump($password);
    //ここから下はmission4-6の補足部分に書かれてたコードを真似してます。
    $sql = 'SELECT * FROM tbtest WHERE id= :id AND password= :password ';
    //echo "hello4";
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        //$rowの中にはテーブルのカラム名が入る
        //ednameを先にrow[name]を後に書く！
        $edname = $row['name'];
        $edcomment = $row['comment'];
        $edpass = $row['password'];
        //echo "hello5";
    }
}

//編集フォームの隠し部分が押された場合
if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass1"])) {
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass1 = $_POST["pass1"];

    //編集モード
    if (!empty($_POST["hd_edit"])) {
        // echo"hello2";
        $hd_edit = $_POST["hd_edit"];
        //var_dump($hd_edit);
        $id = $hd_edit; //変更する投稿番号
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $pass1 = $_POST["pass1"];
        $sql = 'UPDATE tbtest SET name=:name,comment=:comment, password=:password WHERE id=:id AND password=:password';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':password', $pass1, PDO::PARAM_STR);
        $stmt->execute();
    } else {
        //新規投稿モード
        $dsn = 'mysql:dbname=tb230565db;host=localhost';
        $user = 'tb-230565';
        $password = 'beSDhmFVAV';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = $pdo->prepare("INSERT INTO tbtest (name, comment, password) VALUES (:name, :comment, :password)"); //query
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $pass1 = $_POST["pass1"];
        $sql->bindParam(':name', $name, PDO::PARAM_STR);
        $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql->bindParam(':password', $pass1, PDO::PARAM_STR);
        $sql->execute();
        //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。
    }
}
?>

<html>

<head>
    <title>掲示板（オリジナル）</title>
</head>
<form action="" method="post">

    <body>
        <span style="font-size: 50px;"><b>将来の<span style="color: blue">夢</span></b>は<span style="color: red">何</span>ですか??</span>
        <p>【投稿フォーム】</p>
        <p><input type="text" name="name" placeholder="名前を入力してください" value="<?php if (isset($edname) && isset($edcomment)) {
                                                                                echo $edname;
                                                                            } ?>"></p>
        <p><input type="text" name="comment" placeholder="コメントを入力してください" value="<?php if (isset($edname) && isset($edcomment)) {
                                                                                    echo $edcomment;
                                                                                } ?>"></p>
        <p><input type="password" name="pass1" placeholder="パスワードを入力してください" value="<?php if (isset($edname) && isset($edcomment)) {
                                                                                    echo $edpass;
                                                                                } ?>"></p>
        <p><button type="submit" name="submit1">投稿/編集</button></p>
        <input type=hidden name="hd_edit" placeholder="編集判断番号" value="<?php if (isset($edname) && isset($edcomment)) {
                                                                            echo $edit;
                                                                        } ?>">
        <!--編集判断番号はフォーム上に見えないけどこれが入れられると新規でなくて編集の方に分岐させる-->

        <p>【削除フォーム】</p>
        <p><input type="number" name="del" placeholder="削除したい番号を選んでください"></p>
        <p><input type="password" name="pass2" placeholder="パスワードを入力してください"></p>
        <p><button type="submit" name="submit2">削除</button></p>

        <p>【編集フォーム】</p>
        <p><input type="number" name="edit" placeholder="編集したい番号を選んでください"></p>
        <p><input type="password" name="pass3" placeholder="パスワードを入力してください"></p>
        <p><button type="submit" name="submit3">編集</button></p>
    </body>
</form>

<hr>
<p>【投稿内容表示】</p>
<?php
$dsn = 'mysql:dbname=tb230565db;host=localhost';
$user = 'tb-230565';
$password = 'beSDhmFVAV';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
$sql = 'SELECT * FROM tbtest';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row) {
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'] . '<>';
    echo $row['name'] . '<>';
    echo $row['comment'] . '<>';
    echo $row['date'] . '<br>';
    echo "<hr>";
}
?>
