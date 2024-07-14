# 73. edit
``` php
EventControler@edit は
ほぼshowと同じ(return viewでeditに渡す)

events/edit.blade.phpは
create(inputタグ)と
show(アクセサで取得した値など)を混ぜてつくる

(form method=“post” でupdateに渡す,
@csrf @method(‘put’)をつけるなど)

表示非表示
<input type="radio" name="is_visible" value="1"
@if($event->is_visible === 1 ){ checked } @endif />表示
<input type="radio" name="is_visible" value="0"
@if($event->is_visible === 0 ){ checked } @endif/>非表示

```

# 74. update
``` php
EventControler@update はstoreから流用する
(Event:createではなく、
$event = Event:findOrFail($id)で指定して
$event->name = $request[‘name’] とする
$event->save(); で保存

UpdateEventRequestがあるので
StoreEventRequestをコピーする
日付表示の変更


```

# 75. updateの修正
``` php
Models/Event.php アクセサで日付表示を変更する
protected function editEventDate(): Attribute
{ return new Attribute(
  get: fn () => Carbon:parse($this->start_date)->format('Y-m-d'),
  );
}

EventControler@edit

public function edit(Event $event)
{
  $event = Event::findOrFail($event->id);
  $eventDate = $event->editEventDate;

EventService

既にイベントが存在しているので、
重複しているのが1件なら問題なく、1件より多ければエラー
public static function countEventDuplication($eventDate, $startTime, $endTime)
{
return DB:table('events')
->whereDate('start_date', $eventDate)
->whereTime('end_date' ,'>',$startTime)
->whereTime('start_date', '<', $endTime)
->count();
}
EventControler@update
22
$check = EventService:countEventDuplication(
$request['event_date'],$request['start_time'],$request['end_time']);
if($check > 1){
session()->flash('status', 'この時間帯は既に他の予約が存在します。');
$event = Event:findOrFail($event->id);
$eventDate = $event->editEventDate;
$startTime = $event->startTime;
$endTime = $event->endTime;
return view('manager.events.edit',
compact('event', 'eventDate', 'startTime', 'endTime'));
}
削除処理について
23
後程、予約情報とリレーションを組むため、
やや複雑になるということと、
Laravel第２弾(マルチログインでECサイト)で
リレーション込みの削除方法、
ソフトデリートなどを詳しく解説していますので
今回は割愛させていただきます。

```

# 70. アクセサ・ミューテタ
``` php

データベース
保存(set) ミューテタ
取得(get) アクセサ
DBに情報保存時やDBから情報取得時に
データを加工する機能

Laravel9
モデル内に記載
use Iluminate\Database\Eloquent\Casts\Attribute;
protected function firstName(): Attribute // 戻り値の型
{
return new Attribute(
get: fn ($value) => ucfirst($value), // アクセサ
set: fn ($value) => strtolower($value), // ミューテタ
);
}
$user->first_name = ‘Saly’; //使う時はモデル->メソッド名

PHP7.4 アロー関数

無名関数を簡単に書ける文法
(PHP8.0時点では1行でしかかけない)
fn($x) => $x + $y;
https://www.php.net/manual/ja/functions.arrow.php

```

# 71. アクセサの実装
``` php
Event.php

use Iluminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

protected function eventDate(): Attribute
{ 
  return new Attribute(
  get: fn () => Carbon:parse($this->start_date)->format('Y年m月d日')
  );
}

protected function startTime(): Attribute
{ 
  return new Attribute(
  get: fn () => Carbon:parse($this->start_date)->format('H時i分')
  );
}

protected function endTime(): Attribute
{ 
  return new Attribute(
  get: fn () => Carbon:parse($this->end_date)->format('H時i分')
  );
}

EventControler@show

$event = Event:findOrFail($event->id);
$eventDate = $event->eventDate;
$startTime = $event->startTime;
$endTime = $event->endTime;
// dd($eventDate, $startTime, $endTime);
return view('manager.events.show',
compact(‘event’, ‘eventDate’, ‘startTime’, ‘endTime’));

events/show.blade.php 残り

日付 {{ $event->eventDate }}
開始時間 {{ $event->startTime }}
終了時間 {{ $event->endTime }}
@if($event->is_visible)
表示中
@else
非表示
@endif
e
```


