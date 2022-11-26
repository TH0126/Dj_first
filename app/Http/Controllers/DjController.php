<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Models\Dj;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Query\JoinClause;

class DjController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        //
        // phpinfo();


        $date = [
            'output'=>'',
            'chkno'=>0,
        ];

        return view('dj.login', $date);
    }

    public function home_user()
    {

        //ログインデータ（SESSION）
        //問い合わせID
        $id = $request->session()->get('inquiry_id');
        //ユーザー単位の履歴番号
        $email = $request->session()->get('email');


        //ユーザー情報取得
        $main = DB::table('inquiry')
                ->where('id', $id)
                ->where('mail', $email)
                ->where('del_f', '0')
                ->first();

        //ないはずだが、ユーザーマスタにはあって問い合わせデータにない場合
        if (!$main) {
            //エラーメッセージ（未実装）
            // header("Location:login_u.php");
            // exit();
        } else {
            //名前
            $name = $main->kana_sei . "　" . $main->kana_mei . "様";
            //次画面に渡す情報をセット
            $user_id = $main->user_id;
            $party_no = $main->id;
        }

        $date = [
            'name'=>$name,
            'user_id'=>$user_id,
            'party_no'=>$party_no
        ];
        

        return view('dj.home_user', $date);
    }



    //問い合わせ一覧画面表示
    public function inquiry_list()
    {

        //リストデータ取得
        $lists = DB::select("SELECT * FROM inquiry LEFT JOIN (
                                SELECT meeting.user_id AS userid,meeting.party_no,meeting.party_type,meeting.party_ymd,meeting.status,party_master.str as party_str,place_master.str as place_str,dj_master.str as dj_str 
                                FROM meeting, party_master,place_master,dj_master 
                                WHERE meeting.party_type = party_master.m_id AND meeting.place_no = place_master.m_id AND meeting.dj_no = dj_master.m_id AND meeting.del_f = ? AND party_master.del_f = ? AND place_master.del_f = ? AND dj_master.del_f = ?
                                ) AS meet
                            ON inquiry.user_id = meet.userid AND inquiry.id = meet.party_no 
                            WHERE inquiry.del_f = ?",['0','0','0','0','0']);


        return view('dj.inquiry_list', ['lists' => $lists]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //login画面のpost処理
     public function login_post(Request $request)
    {
        //
        if (
            !isset($request->user_id) || $request->user_id === "" ||
            !isset($request->password) || $request->password === ""
        ) {
            $output = "Valid userID required";
        } else {

            // データ受け取り
            $user_id = $request->user_id;
            $password = $request->password;

            //LOGINかCREATE ACCOUNT
            if ($request->botton === "LOGIN") {

                $user_master = DB::table('user_master')
                        ->where('user_id', $user_id)
                        ->where('password', $password)
                        ->where('power', '0')
                        ->where('del_f', '0')
                        ->first();

                if (!$user_master) {
                    $output = "Valid userID or password required";
                } else {
                    Session::getId();
                    $request->session()->put('inquiry_id',$user_master->inquiry_id);
                    $request->session()->put('user_id',$user_id);

                    return redirect('/Dj/inquiry_list');
                }

            } elseif ($_POST["botton"] === "CREATE ACCOUNT") {

                //同じemailでの登録が過去にあったかチェック。あった場合エラー（未実装）
                $user_master = DB::table('user_master')
                        ->where('user_id', $user_id)
                        ->where('del_f', '0')
                        ->first();

                if ($user_master) {
                    $output = "userID is alredy.";
                } else {
                    //ユーザーセット
                    $param = [
                        'user_id' => $user_id,
                        'password' => $password,
                        'inquiry_id' => 0,
                        'power' => 1,
                        'history_no' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'del_f' => '0',
                    ];
                    DB::table('user_master')->insert();

                    Session::getId();
                    $request->session()->put('power',0);
                    $request->session()->put('user_id',$user_master->user_id);
                    return redirect('/Dj/inquiry_list');
                }
            }
        }            
    }

    //問い合わせ一覧からのpost処理
    public function inquiry_post(Request $request)
    {

        if (
            !isset($request->user_id) || $request->user_id === "" ||
            !isset($request->party_no) || $request->party_no === ""
        ) {
            //問い合わせ一覧を再表示
            return redirect('/Dj/inquiry_list');
        } else {

            // データ受け取り
            $user_id = $request->user_id;
            $party_no = $request->party_no;

            //打ち合わせ画面に遷移
            if ($request->has('meeting')){

                return redirect('/Dj/meeting')->with([
                            'user_id' => $user_id,
                            'party_no' => $party_no
                        ]);

            //スケジュール入力画面に遷移
            } elseif ($request->has('sc_input')) {

                return redirect('/Dj/schedule_input')->with([
                            'user_id' => $user_id,
                            'party_no' => $party_no
                        ]);

            //スケジュール表示画面に遷移
            } elseif ($request->has('schedule')) {

                return redirect('/Dj/schedule')->with([
                            'user_id' => $user_id,
                            'party_no' => $party_no
                        ]);
            //削除処理
            } elseif ($request->has('delete')) {

                $del = explode(",", $party_no);

                //削除フラグを立てる
                $inq_del = DB::table('inquiry')
                ->whereIn('id', $del)
                ->update([
                    'del_f' => '1',
                    'updated_at' => now()
                    ]);

                //問い合わせ一覧を再表示
                return redirect('/Dj/inquiry_list');

            }
        }
    }
    

    //問い合わせ一覧から打ち合わせ画面表示
    public function meeting()
    {

        // データ受け取り
        $user_id = session('user_id');
        $party_no = session('party_no');

        //ミーティングデータ取得
        $main = DB::select("SELECT meet.*,party_master.str AS party_str 
                            FROM (SELECT meeting.* FROM meeting WHERE user_id = ? AND party_no = ? AND del_f = ?) AS meet,party_master 
                            WHERE meet.party_type = party_master.m_id AND party_master.del_f = ?",[$user_id,$party_no,'0','0']);
        
        //ミーティングデータがある場合
        if ($main) {
            //パーティー種別（文字列用）
            $party_str = $main[0]->party_str;
            //議事録
            $memory = $main[0]->memory;
            //写真OK有無
            if ($main[0]->photo_flg === 1) {
                $pho_flg = "checked";
            } else {
                $pho_flg = "";
            }
            //開催日
            $par_ymd = $main[0]->party_ymd;
            //料金
            $pri = $main[0]->price;
            //オプション１
            $opt1_txt = $main[0]->option_txt1;
            //ステータス
            switch ($main[0]->status) {
                case "0":
                    $status_str = "";
                    break;
                case "1":
                    $status_str = "予約";
                    break;
                case "2":
                    $status_str = "保存";
                    break;
                default:
                    $status_str = "";
                    break;
            }

            $party_type = $main[0]->party_type;
            $place_no = $main[0]->place_no;
            $dj_no = $main[0]->dj_no;
            $option_no1 = $main[0]->option_no1;

        //ミーティングデータがない場合
        } else {

            $party_str = "";
            //議事録
            $memory = "";
            //写真OK有無
            $pho_flg = "";
            //開催日
            $par_ymd = "";
            //料金
            $pri = "";
            //オプション１
            $opt1_txt = "";
            //ステータス
            $status_str = "";

            $party_type = "";
            $place_no = "";
            $dj_no = "";
            $option_no1 = "";
        
        }

        //各セレクト要素セット
        //パーティー種別取得
        $party_masters = DB::table('party_master')
                ->where('del_f', '0')
                ->orderBy('m_id')
                ->get();

        //会場
        $place_masters = DB::table('place_master')
                ->where('del_f', '0')
                ->orderBy('m_id')
                 ->get();

        //ＤＪ
        $dj_masters = DB::table('dj_master')
                ->where('del_f', '0')
                ->orderBy('m_id')
                 ->get();

        $djArray = array();
        $dj_detail = "";

        foreach ($dj_masters as $dj_master) {
            if ($dj_master->m_id !== 0) {    
                //詳細情報を配列にセット
                array_push($djArray, $dj_master->detail);

                if ($main) {
                    if ($dj_master->m_id === $main[0]->dj_no) {
                        $dj_detail = $dj_master->detail;
                    }    
                }
    
            }
        }
        
        //オプション
        $option_masters = DB::table('option_master')
                ->where('del_f', '0')
                ->orderBy('m_id')
                 ->get();

        //問い合わせ情報取得
        $question_inf = DB::table('inquiry')
                ->where('user_id', $user_id)
                ->where('id', $party_no)
                ->where('del_f', '0')
                ->first();

        $simei = "お名前：　" . $question_inf->kana_sei . "　" . $question_inf->kana_mei . "様";
        $inquiry_com = $question_inf->inquiry_comment;

        $date = [
            'user_id' => $user_id,
            'party_no' => $party_no,
            'party_str' => $party_str,
            'memory' => $memory,
            'pho_flg' => $pho_flg,
            'par_ymd' => $par_ymd,
            'pri' => $pri,
            'opt1_txt' => $opt1_txt,
            'status_str' => $status_str,
            'party_type' => $party_type,
            'place_no' => $place_no,
            'dj_no' => $dj_no,
            'option_no1' => $option_no1,
            'simei' => $simei,
            'inquiry_com' => $inquiry_com,
            'party_masters' => $party_masters,
            'place_masters' => $place_masters,
            'dj_masters' => $dj_masters,
            'option_masters' => $option_masters,
            'dj_out' => $djArray,
            'dj_detail' => $dj_detail,
        ];

        return view('dj.meeting', $date);
    }

    //スケジュール入力画面のpost処理
    public function meeting_post(Request $request)
    {

        if (
            !isset($request->user_id) || $request->user_id === "" ||
            !isset($request->party_no) || $request->party_no === ""
        ) {
            exit("ParamError");
        }
        
        //まず１行目をセット（データがなければinsertあればupdate）
        
        //ユーザーID
        $user_id = $request->user_id;
        //ユーザー単位の履歴番号
        $party_no = $request->party_no;

        //データがあるかチェック
        $meet_cnt = DB::table('meeting')
                ->where('user_id', $user_id)
                ->where('party_no', $party_no)
                ->where('del_f', '0')
                ->count();


        //打ち合わせ画面で入力されたデータをセット
        $memory = $request->memory;

        if (!isset($request->plan_sel) || $request->plan_sel === "") {
            $party_type = 0;
        } else {
            $party_type = $request->plan_sel;
        }
        if (!isset($request->place_sel) || $request->place_sel === "") {
            $place = 0;
        } else {
            $place = $request->place_sel;
        }
        if (!isset($request->dj_sel) || $request->dj_sel === "") {
            $dj = 0;
        } else {
            $dj = $request->dj_sel;
        }
        if (!isset($request->party_ymd) || $request->party_ymd === "") {
            $party_ymd = "";
        } else {
            $party_ymd = date('Y/m/d', strtotime($request->party_ymd));
        }
        if (!isset($request->price) || $request->price === "") {
            $price = 0;
        } else {
            $price = $request->price;
        }
        if (!isset($request->option1_sel) || $request->option1_sel === "") {
            $op1_no = 0;
        } else {
            $op1_no = $request->option1_sel;
        }
        if (!isset($request->option1_txt) || $request->option1_txt === "") {
            $op1_txt = "";
        } else {
            $op1_txt = $request->option1_txt;
        }
        //写真チェックを数字に変換
        if ($request->photo_chk === "on") {
            $ph_chk = 1;
        } else {
            $ph_chk = 0;
        }
        //ステータス
        $status_int = $request->status;

        if ($meet_cnt != 0) {
            //データがあるのでupdate
            $sch_update = DB::table('meeting')
                ->where('user_id', $user_id)
                ->where('party_no', $party_no)
                ->where('del_f', '0')
                ->update([
                    'memory' => $memory,
                    'party_type' => $party_type,
                    'place_no' => $place,
                    'dj_no' => $dj,
                    'photo_flg' => $ph_chk,
                    'party_ymd' => $party_ymd,
                    'price' => $price,
                    'status' => $status_int,
                    'option_no1' => $op1_no,
                    'option_txt1' => $op1_txt,
                    'updated_at' => now()
                    ]);

        } else {
            //データがないのでinsert
            $sch_update = DB::table('meeting')
                ->insert([
                    'user_id' => $user_id,
                    'party_no' => $party_no,
                    'memory' => $memory,
                    'party_type' => $party_type,
                    'place_no' => $place,
                    'dj_no' => $dj,
                    'photo_flg' => $ph_chk,
                    'party_ymd' => $party_ymd,
                    'price' => $price,
                    'status' => $status_int,
                    'option_no1' => $op1_no,
                    'option_txt1' => $op1_txt,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'del_f' => '0',
                    ]);
        }

        //問い合わせ一覧に戻る
        return redirect('/Dj/inquiry_list');


    }


    //問い合わせ一覧からスケジュール入力画面の表示
    public function schedule_input()
    {
        // データ受け取り
        $user_id = session('user_id');
        $party_no = session('party_no');


        //ユーザー情報取得
        $main = DB::select("SELECT * FROM (SELECT * FROM inquiry WHERE user_id = ? AND id = ? AND del_f = ?) AS inquiry LEFT JOIN (
            SELECT meeting.user_id AS userid,meeting.party_no,meeting.party_type,meeting.party_ymd,meeting.status,party_master.str as party_str 
            FROM meeting, party_master 
            WHERE meeting.party_type = party_master.m_id AND meeting.del_f = '0' AND party_master.del_f = ?
            ) AS meet
            ON inquiry.user_id = meet.userid AND inquiry.id = meet.party_no",[$user_id,$party_no,'0','0']);

        //名前
        $name = $main[0]->kana_sei . "　" . $main[0]->kana_mei . "様";

        //パーティー種別（1:二次会、2:学生パーティー、3:ホームパーティー）
        $party_str = $main[0]->party_str;

        //スケジュールデータ取得
        $schedules = DB::table('schedule')
                ->where('user_id', $user_id)
                ->where('party_no', $party_no)
                ->where('del_f', '0')
                ->orderBy('start_ymd')
                ->get();

        // $sch_cnt = $schedules->count();

        //変数初期化セット
        for ($i = 1; $i < 6; $i++) {
            ${"k_name" . $i} = "";
            ${"s_ymd" . $i} = "";
            ${"days" . $i} = "";
            ${"progress" . $i} = "";
        }
        
        $cnt = 1;

        foreach ($schedules as $schedule) {
            ${"k_name" . $cnt} = $schedule->name;
            ${"s_ymd" . $cnt} = $schedule->start_ymd;
            ${"days" . $cnt} = $schedule->days;
            ${"progress" . $cnt} = $schedule->progress;
        
            $cnt++;
        }

        $date = [
            'user_id' => $user_id,
            'party_no' => $party_no,
            'party_str' => $party_str,
            'name' => $name,
            'k_name1' => $k_name1,
            's_ymd1' => $s_ymd1,
            'days1' => $days1,
            'progress1' => $progress1,
            'k_name2' => $k_name2,
            's_ymd2' => $s_ymd2,
            'days2' => $days2,
            'progress2' => $progress2,
            'k_name3' => $k_name3,
            's_ymd3' => $s_ymd3,
            'days3' => $days3,
            'progress3' => $progress3,
            'k_name4' => $k_name4,
            's_ymd4' => $s_ymd4,
            'days4' => $days4,
            'progress4' => $progress4,
            'k_name5' => $k_name5,
            's_ymd5' => $s_ymd5,
            'days5' => $days5,
            'progress5' => $progress5,
        ];

        return view('dj.schedule_input', $date);

    }

    //問い合わせ一覧からスケジュール表示画面へのpost処理
    public function schedule()
    {

        // データ受け取り
        $user_id = session('user_id');
        $party_no = session('party_no');

        //ユーザー用：１、発注先用：２、運営用：３
        $type = '1';

        //ユーザー情報取得
        $main = DB::select("SELECT * FROM (SELECT * FROM inquiry WHERE user_id = ? AND id = ? AND del_f = ?) AS inquiry LEFT JOIN (
                        SELECT meeting.user_id AS userid,meeting.party_no,meeting.party_type,meeting.party_ymd,meeting.status,party_master.str as party_str
                        FROM meeting, party_master
                        WHERE meeting.party_type = party_master.m_id AND meeting.del_f = ? AND party_master.del_f = ?
                        ) AS meet
                        ON inquiry.user_id = meet.userid AND inquiry.id = meet.party_no",[$user_id,$party_no,'0','0','0']);

        //名前
        $name = $main[0]->kana_sei . "　" . $main[0]->kana_mei . "様";

        //パーティー種別（1:二次会、2:学生パーティー、3:ホームパーティー）
        $party_str = $main[0]->party_str;

        //スケジュールデータ取得（問い合わせ一覧から条件はもらう）
        $schedules = DB::table('schedule')
                ->where('user_id', $user_id)
                ->where('party_no', $party_no)
                ->where('del_f', '0')
                ->select('schedule_no','name','start_ymd','days','progress')
                ->get();

        $output = array();

        //スケジュール表にセットするようにjsonデータ作成
        foreach ($schedules as $schedule) {
        
          array_push($output, array(
            "schedule_no" => substr("00" . $schedule->schedule_no, -3),
            "name" => $schedule->name,
            "sYYYY" => date('Y', strtotime($schedule->start_ymd)),
            "sMM" => date('m', strtotime($schedule->start_ymd)),
            "sDD" => date('d', strtotime($schedule->start_ymd)),
            "eYYYY" => date('Y', strtotime($schedule->start_ymd . " " . $schedule->days . " day")),
            "eMM" => date('m', strtotime($schedule->start_ymd . " " . $schedule->days . " day")),
            "eDD" => date('d', strtotime($schedule->start_ymd . " " . $schedule->days . " day")),
            "progress" => intval($schedule->progress)
          ));
        }
        
        $date = [
            'user_id' => $user_id,
            'party_no' => $party_no,
            'party_str' => $party_str,
            'name' => $name,
            'output' => $output,
        ];

        return view('dj.schedule', $date);        
        
    }

    //スケジュール入力画面のpost処理
    public function schedule_post(Request $request)
    {

        if (
            !isset($request->k_name1) || $request->k_name1 === "" ||
            !isset($request->s_ymd1) || $request->s_ymd1 === "" ||
            !isset($request->days1) || $request->days1 === "" ||
            !isset($request->party_type) || $request->party_type === ""
        ) {
            exit("ParamError");
        }
        
        //まず１行目をセット（データがなければinsertあればupdate）
        $iname = $request->k_name1;
        $ymd = date('Y/m/d', strtotime($request->s_ymd1));
        $days = $request->days1;

        //進捗率については未入力（0％）の可能性があるので
        if (!isset($request->progress1) || $request->progress1 === "") {
            $progress = 0;
        } else {
            $progress = $request->progress1;
        }

        $user_id = $request->user_id;
        $party_no = $request->party_no;
        $type = $request->type_sel;
        $party_type = $request->party_type;

        //スケジュールデータがあるかチェック
        $sch_cnt = DB::table('schedule')
                ->where('user_id', $user_id)
                ->where('party_no', $party_no)
                ->where('schedule_no', 1)
                ->where('del_f', '0')
                ->count();

        if ($sch_cnt != 0) {
            //データがあるのでupdate
            $sch_update = DB::table('schedule')
                ->where('user_id', $user_id)
                ->where('party_no', $party_no)
                ->where('schedule_no', 1)
                ->where('type', $type)
                ->where('del_f', '0')
                ->update([
                    'name' => $iname,
                    'start_ymd' => $ymd,
                    'days' => $days,
                    'progress' => $progress,
                    'updated_at' => now()
                    ]);
        
        } else {
            //データがないのでinsert
            $sch_update = DB::table('schedule')
                ->insert([
                    'user_id' => $user_id,
                    'party_no' => $party_no,
                    'schedule_no' => '1',
                    'type' => $type,
                    'name' => $iname,
                    'party_type' => $party_type,
                    'start_ymd' => $ymd,
                    'days' => $days,
                    'progress' => $progress,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'del_f' => '0',
                    ]);
        
        }

        //2行目以降に入力があるか？
        $cnt = 1;

        for ($i = 2; $i < 6; $i++) {
            $name_str = "k_name" . $i;

            if (isset($request->$name_str) && $request->$name_str !== "") {
                $ymd_str = "s_ymd" . $i;
                $day_str = "days" . $i;
                $progress_str = "progress" . $i;

                $iname = $request->$name_str;
                $ymd = date('Y/m/d', strtotime($request->$ymd_str));
                $days = $request->$day_str;

                //進捗率については未入力（0％）の可能性があるので
                if (!isset($request->$progress_str) || $request->$progress_str === "") {
                    $progress = 0;
                } else {
                    $progress = $request->$progress_str;
                }

                //スケジュールデータがあるかチェック
                $sch_cnt2 = DB::table('schedule')
                        ->where('user_id', $user_id)
                        ->where('party_no', $party_no)
                        ->where('schedule_no', $i)
                        ->where('del_f', '0')
                        ->count();

                if ($sch_cnt2 != 0) {
                    //データがあるのでupdate
                    $sch_update = DB::table('schedule')
                        ->where('user_id', $user_id)
                        ->where('party_no', $party_no)
                        ->where('schedule_no', $i)
                        ->where('type', $type)
                        ->where('del_f', '0')
                        ->update([
                            'name' => $iname,
                            'start_ymd' => $ymd,
                            'days' => $days,
                            'progress' => $progress,
                            'updated_at' => now()
                            ]);
                
                } else {
                    //データがないのでinsert
                    $sch_update = DB::table('schedule')
                        ->insert([
                            'user_id' => $user_id,
                            'party_no' => $party_no,
                            'schedule_no' => $i,
                            'type' => $type,
                            'name' => $iname,
                            'party_type' => $party_type,
                            'start_ymd' => $ymd,
                            'days' => $days,
                            'progress' => $progress,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'del_f' => '0',
                            ]);
                }
            }
        }
        //問い合わせ一覧に戻る
        return redirect('/Dj/inquiry_list');
    }
}
