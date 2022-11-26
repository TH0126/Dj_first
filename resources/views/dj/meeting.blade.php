<!DOCTYPE html>
<html lang='ja'>

<head>
    <meta charset='utf-8' />
    <script src='/fullcalendar/lib/main.js'></script>
    <script src="/fullcalendar/lib/locales/ja.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <link href='/fullcalendar/lib/main.css' rel='stylesheet' />
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP" rel="stylesheet">
    <link rel="stylesheet" href="/css/style_m.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
    <title>打ち合わせ管理</title>

</head>

<body>
    <form action="" method="POST">
        @csrf
        <header>
            <img id="logo" src="/img/headerlogo.png" alt="">
            <div id="party_name">パーティー種別：　{{$party_str}}</div>
            <div class="name">{{$simei}}</div>
            <div class="btn_group">
                <button type="submit" id="save_btn">保存</button>
                <button type="button" id="clear_btn">クリア</button>
                <button type="submit" id="reserve_btn">予約</button>
                <button type="button" onclick="history.back()" class="back_btn">戻る</button>
            </div>
        </header>

        <div class="main">
            <!-- 打ち合わせ画面の左側（お客様問い合わせ情報、議事録、プラン、カレンダー、会場、DJ -->
            <div class="left">
                <!-- お客様問い合わせ情報、議事録、プラン -->
                <div class="customer">
                    <input type="hidden" name="user_id" value={{$user_id}}>
                    <input type="hidden" name="party_no" value={{$party_no}}>
                    <div class="logo_p">
                        <img class="logo" src="/img/meeting-preview.png" alt="">
                        <div>お問い合わせ内容：</div>
                    </div>
                    <div class="inquiry">
                        <div class="in_rg">
                            <div id="free_meg">{{$inquiry_com}}</div>
                            <div>メモ：</div>
                            <textarea name="memory" id="memory" cols="60" rows="6">{{$memory}}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right">
                <!-- プラン、カレンダー、会場、DJ -->
                <div class="plan_main">
                    <!-- プラン、カレンダー -->
                    <div class="plan">
                        <div class="logo_p">
                            <img class="logo" src="/img/clender.png" alt="">
                            <div>スケジュールカレンダー：</div>
                        </div>
                        <div class="cal_st" id="cal"></div>
                    </div>
                    <div class="min_right">
                        <div class="logo_p">
                            <div class="ppp">パーティー種別：</div>
                            <div class="cp_ipselect cp_sl02">
                                <select id="plan_sel" class="plan_select" name="plan_sel">
                                    <option value=''>--パーティー種別--</option>
                                    @if ($party_masters != null)
                                        @foreach($party_masters as $party_master)
                                            @if ($party_master->m_id != 0)
                                                @if ($party_master->m_id === $party_type)
                                                    <option value='{{$party_master->m_id}}' selected>{{$party_master->str}}</option>
                                                @else
                                                    <option value='{{$party_master->m_id}}'>{{$party_master->str}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input id="photo_chk" type="checkbox" name="photo_chk" {{$pho_flg}}><label>写真および動画の使用許可</label>
                        <div class="open_day">
                            <label>開催日：</label>
                            <input id="party_ymd" type="text" name="party_ymd" value="{{$par_ymd}}">
                        </div>
                        <div class="p_val">
                            <label>料金：</label>
                            <input id="price" type="text" name="price" value="{{$pri}}">
                        </div>
                        <div class="sta">
                            <label>ステータス：</label>
                            <input id="status" type="text" readonly="readonly" disabled="disabled" value="{{$status_str}}">
                            <input type="hidden" id="status_int" name="status" value="">
                        </div>
                    </div>
                </div>
                <div class="right_down">
                    <!-- 会場、DJ -->
                    <div class="place_dj">
                        <div class="place_main">
                            <div class="logo_p">
                                <img class="logo" src="/img/shop.png" alt="">
                                <div>会場選択：</div>
                            </div>
                            <div class="place">
                                <div class="cp_ipselect cp_sl02">
                                    <select id="place_sel" class="plan_select" name="place_sel">
                                        <option value=''>--会場--</option>
                                        @if ($place_masters != null)
                                            @foreach($place_masters as $place_master)
                                                @if ($place_master->m_id != 0)
                                                    @if ($place_master->m_id === $place_no)
                                                        <option value='{{$place_master->m_id}}' selected>{{$place_master->str}}</option>
                                                    @else
                                                        <option value='{{$place_master->m_id}}'>{{$place_master->str}}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <a id="p_url" href="">https://www.xxxx.com</a>
                            </div>
                            <div id="map"></div>
                        </div>
                    </div>
                    <div class="dj_main">
                        <div class="logo_p">
                            <img class="logo" src="/img/DJ3.png" alt="">
                            <div>ＤＪ選択：</div>
                        </div>
                        <div class="dj">
                            <div class="cp_ipselect cp_sl02">
                                <select id="dj_sel" class="plan_select" name="dj_sel">
                                    <option value=''>--DJ--</option>
                                    @if ($dj_masters != null)
                                        @foreach($dj_masters as $dj_master)
                                            @if ($dj_master->m_id != 0)
                                                @if ($dj_master->m_id === $dj_no)
                                                    <option value='{{$dj_master->m_id}}' selected>{{$dj_master->str}}</option>
                                                @else
                                                    <option value='{{$dj_master->m_id}}'>{{$dj_master->str}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <button class="detail_btn">詳細</button>
                        </div>
                        <div id="dj_detail">{{$dj_detail}}</div>
                    </div>
                    <div class="supplier_group">
                        <!-- <button id="add_btn">追加</button> -->
                        <div class="logo_p">
                            <img class="logo" src="/img/mitumori-preview.png" alt="">
                            <div>オプション選択：</div>
                        </div>
                        <div class="supplier">
                            <div class="cp_ipselect cp_sl02">
                                <select id="supplier_sel" class="plan_select" name="option1_sel">
                                    <option value=''>--オプション--</option>
                                    @if ($option_masters != null)
                                        @foreach($option_masters as $option_master)
                                            @if ($option_master->m_id != 0)
                                                @if ($option_master->m_id === $option_no1)
                                                    <option value='{{$option_master->m_id}}' selected>{{$option_master->str}}</option>
                                                @else
                                                    <option value='{{$option_master->m_id}}'>{{$option_master->str}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <button class="detail_btn">詳細</button>
                            <!-- <button class="delete_btn">削除</button> -->
                        </div>
                        <textarea name="option1_txt" class="sup_coment" cols="30" rows="4">{{$opt1_txt}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).on("change", "#plan_sel", function() {
            switch ($("#plan_sel").val()) {
                case "1":
                    $("#party_name").text("1.5次会、2次会");
                    break;
                case "2":
                    $("#party_name").text("学生パーティー");
                    break;
                case "3":
                    $("#party_name").text("ラウンジパーティー");
                    break;
                case "4":
                    $("#party_name").text("企業パーティー");
                    break;
                case "5":
                    $("#party_name").text("キャンプパーティー");
                    break;
                case "6":
                    $("#party_name").text("学校行事");
                    break;
                default:
                    $("#party_name").text("");
                    break;
            }
        });

        $(document).on("change", "#dj_sel", function() {
            const dj_detail = @json($dj_out);
            $("#dj_detail").text(dj_detail[$("#dj_sel").val() - 1]);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const calendar = new FullCalendar.Calendar($("#cal").get(0), {
                initialView: 'dayGridMonth',
                locale: 'ja',
                events: [{
                    title: "DJ",
                    start: "2022-04-08"
                }, ],

                //日付を選択可能に
                selectable: true,
                //日付を選択したときの処理
                dateClick: function(info) {
                    $("#party_ymd").val(info.dateStr);
                    // alert('Clicked on: ' + info.dateStr);
                }

                // height: 'auto',
            });
            calendar.render();
        });

        let map;
        let marker;

        function initMap() {
            map = new google.maps.Map($("#map").get(0), {
                center: {
                    lat: 33.590184,
                    lng: 130.401689
                },
                zoom: 15,
            });

            marker = new google.maps.Marker({
                position: {
                    lat: 33.590184,
                    lng: 130.401689
                },
                map: map
            });
        }
        //予約ボタン
        $("#reserve_btn").on("click", function() {
            $("#status_int").val(1);
        });
        //保存ボタン
        $("#save_btn").on("click", function() {
            $("#status_int").val(2);
        });
        //クリアボタン
        $("#clear_btn").on("click", function() {
            $("#memory").val("");
            $("#plan_sel option[value='']").prop('selected', true);
            $("#place_sel option[value='']").prop('selected', true);
            $("#dj_sel option[value='']").prop('selected', true);
            $("#supplier_sel option[value='']").prop('selected', true);
            $("#party_name").text("種別：　");
            $("#party_ymd").val("");
            $("#price").val("");
            $("#photo_chk").removeAttr('checked').prop('checked', false).change()
        });
    </script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBaGFiCXKiZmeI_lVL9u7A5Tlqhe5G3xoA&callback=initMap&v=weekly" async></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAanbIwFlVoNeWfX5hPR7EqvhIHmdrd6vA&callback=initMap&v=weekly" async></script>

</body>

</html>