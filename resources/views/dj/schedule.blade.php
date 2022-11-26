<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <link rel="stylesheet" href="/css/style_s.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
  <title>スケジュール管理</title>
</head>

<body>
  <div class="ga_main">スケジュール</div>
  <div class="daimoku">
    <div class="dai_name">お客様：</div>
    <div id="user_name">{{$name}}</div>
    <button onclick="history.back()" class="back_btn">戻る</button>
  </div>
  <div class="daimoku">
    <div class="dai_name">パーティー種別：</div>
    <div id="party_name">{{$party_str}}</div>
    <input id="party_type" type="hidden" name="party_type" value=1>
  </div>
  <div class="cp_ipselect cp_sl02">
    <select class="plan_select" name="dj_sel">
      <option value="1">お客様用</option>
      <option value="2">発注先用</option>
      <option value="3">運営用</option>
    </select>
  </div>
  <div id="chart_div"></div>

  <script>
    google.charts.load('current', {
      'packages': ['gantt']
    });
    google.charts.setOnLoadCallback(drawChart);

    function daysToMilliseconds(days) {
      return days * 24 * 60 * 60 * 1000;
    }

    function drawChart() {

      let data = new google.visualization.DataTable();
      data.addColumn('string', 'Task ID');
      data.addColumn('string', 'Task Name');
      data.addColumn('string', 'Resource');
      data.addColumn('date', 'Start Date');
      data.addColumn('date', 'End Date');
      data.addColumn('number', 'Duration');
      data.addColumn('number', 'Percent Complete');
      data.addColumn('string', 'Dependencies');

      //phpからスケジュールデータ取得
      const scData = @json($output);

      const sc_length = scData.length;

      //google chartsに合わせた形にスケジュールデータを変更
      for (let i = 0; i < sc_length; i++) {
        let tempArray = [];

        tempArray.push(scData[i].schedule_no);
        tempArray.push(scData[i].name);
        tempArray.push('{{$party_str}}');
        tempArray.push(new Date(scData[i].sYYYY, scData[i].sMM, scData[i].sDD));
        tempArray.push(new Date(scData[i].eYYYY, scData[i].eMM, scData[i].eDD));
        tempArray.push(null);
        tempArray.push(scData[i].progress);
        tempArray.push(null);

        data.addRows([tempArray]);
      }


      // data.addRows([
      //   ['001', 'パーティー打ち合わせ', '二次会',
      //     new Date(2022, 3, 22), new Date(2022, 3, 28), null, 100, null
      //   ],
      //   ['002', 'ＤＪ打ち合わせ', '二次会',
      //     new Date(2022, 3, 28), new Date(2022, 4, 2), null, 80, null
      //   ],
      //   ['003', '会場下見', '二次会',
      //     new Date(2022, 4, 2), new Date(2022, 4, 10), null, 10, null
      //   ],
      // ]);

      const options = {
        height: 400,
        gantt: {
          trackHeight: 30
        }
      };
      const chart = new google.visualization.Gantt($('#chart_div').get(0));

      chart.draw(data, options);
    }
  </script>

</body>

</html>