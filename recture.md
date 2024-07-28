# 83. 予約情報(リレーション設定)
``` php

リレーションの設定

belongsToMany・・多対多のリレーション、第２引数は中間テーブル名
withPivotで中間テーブル内の取得したい情報を指定
App\Models\Event.php
public function users()
{
return $this->belongsToMany(User::class, 'reservations')
->withPivot('id', 'number_of_people', 'canceled_date');
}

App\Models\User.php
use APP\Models\Event;
public function events()
  {
      return $this->belongsToMany(Event::class, 'reservations')
          ->withPivot('id', 'number_of_people', 'canceled_date');
  }

EventControler@show

public function show(Event $event)
{
$event = Event:findOrFail($event->id);
$users = $event->users;
// dd($event, $users);
略
return view('manager.events.show',
compact('event', 'users', 'eventDate', 'startTime', 'endTime'));
}

events/show.blade.php

<div class="py-4">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
<div class="max-w-2xl mx-auto">
@if (!$users->isEmpty())
予約情報
@endif
</div>
</div>
</div>
</div>
```

# 84. cancel分を除いて予約情報を表示
``` php
ReservationSeeder.php

キャンセルした分は表示しない事を確認するためダミーに追記
DB:table('reservations')->insert([[
'user_id' => 1,
'event_id' => 1,
'number_of_people' => 5,
'canceled_date' => nul
],
略
[
'user_id' => 2,
'event_id' => 2,
'number_of_people' => 2,
'canceled_date' => '2022-03-01 00:00:00'
]

EventControler@show

略
$reservations = []; // 連想配列を作成
foreach($users as $user)
{
$reservedInfo = [
'name' => $user->name,
'number_of_people' => $user->pivot->number_of_people,
'canceled_date' => $user->pivot->canceled_date
];
array_push($reservations, $reservedInfo); // 連想配列に追加
}
// dd($reservations);
略
return view('manager.events.show',
compact('event', 'reservations', 略));
events/show.blade.php

<div class="max-w-2xl mx-auto">
@if (!$users->isEmpty())
予約情報
@foreach($reservations as $reservation)
@if(is_nul($reservation['canceled_date']))
{{ $reservation['name'] }}
{{ $reservation['number_of_people']}}
@endif
@endforeach
@endif
</div>

```

# 84. cancel分を除いて予約情報を表示
``` php
予約人数の合計クエリ

キャンセル分は合計に含めないようにするため
whereNulを追加
index, pastそれぞれに追加
$reservedPeople = DB:table('reservations')
->select('event_id', DB:raw('sum(number_of_people) as
number_of_people’))
->whereNul(‘canceled_date’)
->groupBy(‘event_id’);
events/show.blade.php
49
予約状況の
レイアウトの調整は
index.blade.phpのtableを参考に

```
