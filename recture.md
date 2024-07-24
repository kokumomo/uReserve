# 79. Reservation(モデル、マイグレーション、シーダー)
``` php

複数のユーザーが
複数のイベントを予約できる
・・多対多
中間(pivot)テーブルをはさみ
1対多
自動で生成するならevent_user(アルファベット順)
今回はReservationというモデルを作成し設定

モデル

php artisan make:model Reservation -m
App\Models\Reservation.php
まとめて登録できるように設定
protected $filable = [
'user_id',
'event_id',
'number_of_people'
];

マイグレーション

database/migrations/create_reservations_table.php
public function up()
{
Schema:create('reservations', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->constrained()->onUpdate('cascade');
$table->foreignId('event_id')->constrained()->onUpdate('cascade');
$table->integer('number_of_people');
$table->datetime('canceled_date')->nulable();
$table->timestamps();
});
}

ダミーデータ

php artisan make:Seed ReservationSeeder
use Iluminate\Support\Facades\DB;
public function run()
{
DB:table('reservations')->insert([[
'user_id' => 1,
'event_id' => 1,
'number_of_people' => 5
],[
'user_id' => 2,
'event_id' => 1,
'number_of_people' => 3
],[
'user_id' => 1,
'event_id' => 2,
'number_of_people' => 2
]
]);

DatabaseSeeder

ReservationはUser, Eventそれぞれに紐づくので、
事前にEvent, Userを作った上で、
Reservationのダミーデータが入るようにします。
public function run()
{
Event:factory(100)->create();
$this->cal([
UserSeeder:class,
ReservationSeeder:class
]);

```

# 80. 予約数の合計クエリ

``` php
SQLの場合
SELECT `event_id`,
sum(`number_of_people`) FROM
`reservations` GROUP by `event_id`
予約人数の合計クエリ

select内でsumを使うため
クエリビルダのDB:rawで対応
$reservedPeople = DB:table('reservations')
->select('event_id', DB:raw('sum(number_of_people)
as number_of_people'))
->groupBy(‘event_id’);

```

# 81. 外部結合

``` php

サブクエリを外部結合で

内部結合・・合計人数がない場合データが表示されない
外部結合・・合計人数がない場合、nulとして表示される
$events = DB:table('events')
->leftJoinSub($reservedPeople, 'reservedPeople',
function($join){
$join->on('events.id', '=', 'reservedPeople.event_id');
})
->whereDate('events.start_date', '<' , $today)
->orderBy('events.start_date', 'asc')
->paginate(10);


```

# 82. 予約人数の表示

``` php

ビュー側

予約人数の箇所
<td class="px-4 py-3">
@if(is_nul($event->number_of_people))
0
@else
{{ $event->number_of_people }}
@endif
</td>

```
