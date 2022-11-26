<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style_lo.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
    <title>ログイン画面</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <form action="" name="login_form" method="POST">
        @csrf
        <div class="login_form_top">
            <img src="/img/pzmlogo.png" alt="ロゴ">
        </div>
        <div class="login_form_btn">
            <input type="id" name="user_id" placeholder="UserID" required="required"><br>
            <input type="password" name="password" placeholder="Password" required="required">
            <p class="red">{{$output}}</p>
            <input type="submit" id="btn_in" name="botton" value="LOGIN">
            <div class="center">
                <span id="b_color">or</span>
                <button type="button" onclick="clickTextChange()" id="c_color">CREATE ACCOUNT</button>
            </div>
            <!-- <button type="button" onclick="location.href='./index.php'" id="r_color">BACK</button> -->
        </div>
        <!-- 2重処理にならないようにトークン設定 -->
        <input name="chkno" type="hidden" value="{{$chkno}}">
    </form>

    <script>
        $(document).ready(function() {});

        //HTMLの読み込みが終わった後、処理開始
        $(window).on('load', function() {


        });

        function clickTextChange() {
            if ($("#c_color").text() === "CREATE ACCOUNT") {
                $("#c_color").text("LOGIN to your account")
                $("#btn_in").val("CREATE ACCOUNT")
            } else if ($("#c_color").text() === "LOGIN to your account") {
                $("#c_color").text("CREATE ACCOUNT")
                $("#btn_in").val("LOGIN")
            }
        };
    </script>


</body>

</html>