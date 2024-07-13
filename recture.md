# 69. Event@show

``` php
events/Index.blade.php

クリック時にパラメータを渡す
<td class="text-blue-500">
<a href="{{ route(‘events.show’, ['event' => $event->id]) }}">{{ $event->name }}
</a>
</td>

EventControler@show

public function show(Event $event)
{
// dd($event); イベントモデルを取得
$event = Event:findOrFail($event->id);
return view(‘manager.events.show’, compact(‘event’));
}

events/show.blade.php

create.blade.phpをコピペ
form getメソッド時は@csrf不要なので削除
テキストエリア(改行の変換)
{!! nl2br(e($event->information)) !!}
e() ・・エスケープする(サニタイズ)
nl2br・・改行を<br />に変換
{!! !!} ・・<br>だけエスケープしない

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


