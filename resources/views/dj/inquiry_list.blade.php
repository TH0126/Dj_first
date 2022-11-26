<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>問い合わせ一覧</title>
    <!-- <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.0.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/style_il.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="dark">
            <div class="daimoku">
                <p class="w_col">問い合わせ一覧</p>
                <div class="btn_group">
                    <form action="" method="POST">
                        @csrf
                        <input type="hidden" class="party_no" name="party_no" value="">
                        <input type="hidden" class="user_id" name="user_id" value="">
                        <button type="submit" class="btn formbtn" id="btn_meeting" name="meeting">打ち合わせ</button>
                    </form>
                    <form action="" method="POST">
                        @csrf
                        <input type="hidden" class="party_no" name="party_no" value="">
                        <input type="hidden" class="user_id" name="user_id" value="">
                        <button type="submit" class="btn formbtn" id="btn_input" name="sc_input">スケジュール入力</button>
                    </form>
                    <form action="" method="POST">
                        @csrf
                        <input type="hidden" class="party_no" name="party_no" value="">
                        <input type="hidden" class="user_id" name="user_id" value="">
                        <button type="submit" class="btn formbtn" id="btn_open" name="schedule">スケジュール表示</button>
                    </form>
                    <form action="" method="POST">
                        @csrf
                        <input type="hidden" class="party_no" name="party_no" value="">
                        <input type="hidden" class="user_id" name="user_id" value="dummy">
                        <button type="submit" class="btn formbtn" id="btn_del" name="delete">削除</button>
                    </form>
                    <button type="button" onclick="location.href='/Dj/login'" class="btn formbtn2" id="btn_out">LOGOUT</button>
                </div>
            </div>
            <table class="col-12 table table-bordered">
                <thead class="header">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">No.</th>
                        <th scope="col">お名前（フリガナ）</th>
                        <th scope="col">お問い合わせ種別</th>
                        <th scope="col">パーティー種別</th>
                        <th scope="col">会場</th>
                        <th scope="col">開催日</th>
                        <th scope="col">ＤＪ</th>
                        <th scope="col">ステータス</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($lists != null)
                        @foreach($lists as $list)
                            <tr class='retu'>
                                <td>
                                    <div class='form-check'>
                                        <input class='form-check-input position-static' type='checkbox' value='{{$list->id}}_{{$list->user_id}}'>
                                    </div>
                                </td>
                                <td>{{$list->id}}</td>
                                <td>{{$list->kana_sei}}　{{$list->kana_mei}}</td>
                                @switch($list->inquiry_type)
                                    @case('1')
                                        <td>サービスについてのお問い合わせ</td>
                                        @break

                                    @case('2')
                                        <td>機材レンタルに関するお問い合わせ</td>
                                        @break

                                    @case('3')
                                        <td>その他</td>
                                        @break

                                    @default
                                        <td></td>
                                @endswitch
                                <td>{{$list->party_str}}</td>
                                <td>{{$list->place_str}}</td>
                                <td>{{$list->party_ymd}}</td>
                                <td>{{$list->dj_str}}</td>
                                @switch($list->status)
                                    @case('0')
                                        <td></td>
                                        @break
                                    @case('1')
                                        <td>予約</td>
                                        @break
                                    @case('2')
                                        <td>保存</td>
                                        @break
                                    @default
                                        <td></td>
                                @endswitch
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <!-- モーダルウィンドウ群 -->
        <div class="modal-container">
            <div class="modal-body">
                <p id="message"></p>
                <div class="modal-close">OK</div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {});

        //HTMLの読み込みが終わった後、処理開始
        $(window).on('load', function() {

            $("#btn_meeting").on("click", function() {
                if ($('[class="form-check-input position-static"]:checked').length > 1) {
                    $("#message").text("複数選択されています。１つだけ選択してください。");
                    $(".modal-container").toggleClass("active");
                    return false;
                } else if ($('[class="form-check-input position-static"]:checked').length === 0) {
                    $("#message").text("選択がされていません。１つ選択してください。");
                    $(".modal-container").toggleClass("active");
                    return false;
                } else if ($('[class="form-check-input position-static"]:checked').length === 1) {
                    //1件のみなのでダイレクトにセット
                    const ind = $('[class="form-check-input position-static"]:checked').val().indexOf("_");
                    const str_no = $('[class="form-check-input position-static"]:checked').val().slice(0, ind);
                    const str_id = $('[class="form-check-input position-static"]:checked').val().slice(ind + 1);

                    $(".party_no").val(str_no);
                    $(".user_id").val(str_id);
                }
            });

            $("#btn_input").on("click", function() {
                if ($('[class="form-check-input position-static"]:checked').length > 1) {
                    $("#message").text("複数選択されています。１つだけ選択してください。");
                    $(".modal-container").toggleClass("active");
                    return false;

                } else if ($('[class="form-check-input position-static"]:checked').length === 0) {
                    $("#message").text("選択がされていません。１つ選択してください。");
                    $(".modal-container").toggleClass("active");
                    return false;

                } else if ($('[class="form-check-input position-static"]:checked').length === 1) {
                    //1件のみなのでダイレクトにセット
                    const ind = $('[class="form-check-input position-static"]:checked').val().indexOf("_");
                    const str_no = $('[class="form-check-input position-static"]:checked').val().slice(0, ind);
                    const str_id = $('[class="form-check-input position-static"]:checked').val().slice(ind + 1);

                    $(".party_no").val(str_no);
                    $(".user_id").val(str_id);
                }
            });

            $("#btn_open").on("click", function() {
                if ($('[class="form-check-input position-static"]:checked').length > 1) {
                    $("#message").text("複数選択されています。１つだけ選択してください。");
                    $(".modal-container").toggleClass("active");
                    return false;

                } else if ($('[class="form-check-input position-static"]:checked').length === 0) {
                    $("#message").text("選択がされていません。１つ選択してください。");
                    $(".modal-container").toggleClass("active");
                    return false;

                } else if ($('[class="form-check-input position-static"]:checked').length === 1) {
                    //1件のみなのでダイレクトにセット
                    const ind = $('[class="form-check-input position-static"]:checked').val().indexOf("_");
                    const str_no = $('[class="form-check-input position-static"]:checked').val().slice(0, ind);
                    const str_id = $('[class="form-check-input position-static"]:checked').val().slice(ind + 1);

                    $(".party_no").val(str_no);
                    $(".user_id").val(str_id);
                }
            });


            //閉じるボタンをクリックしたらモーダルを閉じる
            $(".modal-close").on("click", function() {
                $(".modal-container").removeClass("active");
            });
            //モーダルの外側をクリックしたらモーダルを閉じる
            $(document).on("click", function(e) {
                if (!$(e.target).closest(".modal-body").length) {
                    $(".modal-container").removeClass("active");
                }
            });

            $("#btn_del").on("click", function() {
                if ($('[class="form-check-input position-static"]:checked').length === 0) {
                    $("#message").text("選択がされていません。１つ選択してください。");
                    $(".modal-container").toggleClass("active");
                    return false;
                }
                let arr = [];
                $('[class="form-check-input position-static"]:checked').each(function() {
                    //idだけを抽出
                    const ind = $(this).val().indexOf("_");
                    const str_no = $(this).val().slice(0, ind);

                    // チェックされている値を配列に格納
                    arr.push(str_no);
                });
                $(".party_no").val(arr);
            });

        });
    </script>
</body>

</html>