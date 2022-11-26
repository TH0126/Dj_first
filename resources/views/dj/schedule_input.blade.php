<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <link rel="stylesheet" href="/css/style_si.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
  <title>スケジュール設定</title>
</head>

<body>
  <div class="ga_main">スケジュール入力</div>
  <form action="" method="POST">
    @csrf
    <div class="daimoku">
      <div class="dai_name">お客様：</div>
      <div id="user_name">{{$name}}</div>
      <input type="hidden" name="user_id" value={{$user_id}}>
      <input type="hidden" name="party_no" value={{$party_no}}>
      <button type="submit" class="update_btn">更新</button>
      <button type="button" onclick="history.back()" class="back_btn">戻る</button>
    </div>
    <div class="daimoku">
      <div class="dai_name">パーティー種別：</div>
      <div id="party_name">{{$party_str}}</div>
      <input id="party_type" type="hidden" name="party_type" value=1>
    </div>
    <div class="cp_ipselect cp_sl02">
      <select class="plan_select" name="type_sel">
        <option value="1">お客様用</option>
        <option value="2">発注先用</option>
        <option value="3">運営用</option>
      </select>
    </div>
    <table>
      <tr>
        <th>工程名</th>
        <th>開始日付</th>
        <th>予定日数</th>
        <th>進捗率</th>
      </tr>
      <tr>
        <td><input class="k_name" type="text" name="k_name1" value={{$k_name1}}></td>
        <td><input class="s_ymd" type="text" name="s_ymd1" value={{$s_ymd1}}></td>
        <td><input class="days" type="text" name="days1" value={{$days1}}></td>
        <td><input class="progress" type="text" name="progress1" value={{$progress1}}><label>％</label></td>
      </tr>
      <tr>
        <td><input class="k_name" type="text" name="k_name2" value={{$k_name2}}></td>
        <td><input class="s_ymd" type="text" name="s_ymd2" value={{$s_ymd2}}></td>
        <td><input class="days" type="text" name="days2" value={{$days2}}></td>
        <td><input class="progress" type="text" name="progress2" value={{$progress2}}><label>％</label></td>
      </tr>
      <tr>
        <td><input class="k_name" type="text" name="k_name3" value={{$k_name3}}></td>
        <td><input class="s_ymd" type="text" name="s_ymd3" value={{$s_ymd3}}></td>
        <td><input class="days" type="text" name="days3" value={{$days3}}></td>
        <td><input class="progress" type="text" name="progress3" value={{$progress3}}><label>％</label></td>
      </tr>
      <tr>
        <td><input class="k_name" type="text" name="k_name4" value={{$k_name4}}></td>
        <td><input class="s_ymd" type="text" name="s_ymd4" value={{$s_ymd4}}></td>
        <td><input class="days" type="text" name="days4" value={{$days4}}></td>
        <td><input class="progress" type="text" name="progress4" value={{$progress4}}><label>％</label></td>
      </tr>
      <tr>
        <td><input class="k_name" type="text" name="k_name5" value={{$k_name5}}></td>
        <td><input class="s_ymd" type="text" name="s_ymd5" value={{$s_ymd5}}></td>
        <td><input class="days" type="text" name="days5" value={{$days5}}></td>
        <td><input class="progress" type="text" name="progress5" value={{$progress5}}><label>％</label></td>
      </tr>
    </table>
    <div class="bottom"></div>
  </form>
</body>

</html>