# 65. 保存処理

``` php
Models/Event.php

Event:create()で保存できるようにするためにモデルに追記
(DBテーブルの列名)
app/Models/Event.php
protected $filable = [
'name',
'information',
'max_people',
'start_date',
'end_date',
'is_visible'
];


EventControler@store
use Carbon\Carbon;
// formは event_date, start_time, end_time 
// modelはstart_date, end_date
// event_dateとstart_timeを繋げる

$start = $request['event_date'] . " " . $request['start_time'];
// Carbonで指定した日付の方に変換
$start_date = Carbon::createFromFormat('Y-m-d H:i', $start);

Event:create([
  'name' => $request['event_name'],
  'information' => $request['information'],
  'start_date' => $start_date,
  'end_date' => $end_date,
  'max_people' => $request['max_people'],
  'is_visible' => $request['is_visible'],
]);
session()->flash(‘status’, ‘登録okです’);
return to_route(‘events.index’); //名前付きルート

```

# 68、69. 保存時の注意(重複チェック)、クエリ

``` php
重複チェックのクエリ

$check = DB::table('events')
->whereDate('start_date', $request['event_date']) // 日にち
->whereTime('end_date' ,'>',$request['start_time'])
->whereTime('start_date', '<', $request['end_time'])
->exists(); // 存在確認

// dd($check);
if($check){ // 存在したら
session()->flash('status', 'この時間帯は既に他の予約が存在します。');
return view('manager.events.create');
}

```

# 68. サービスへの切り離し
``` php

ファットコントローラを防ぐため
app/Services/EventServices.php
<?php

namespace App\Services;

use Iluminate\Support\Facades\DB;
use Carbon\Carbon;
class EventServices{}


日付+時間
app/Services/EventService.php
public static function joinDateAndTime($date, $time)
{
$join = $date . "" . $time;
return Carbon::createFromFormat('Y-m-d H:i',$join);
}


EventControler@store修正
use app/Services/EventService;

$check = EventServices::checkEventDuplication(
  $request['event_date'],$request['start_time'],$request['end_time']
);
略
$startDate = EventServices::joinDateAndTime($request['event_date'],$request['start_time']);
$endDate = EventServices::joinDateAndTime($request['event_date'],$request['end_time']);

Event:create([
略
'start_date' => $startDate,
'end_date' => $endDate,
```


