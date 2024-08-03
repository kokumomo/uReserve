# 87. 予約カレンダーの準備
``` php

予約カレンダー

ログインなしで表示可能
予約時はログイン(会員登録)必要
週間カレンダー
選択日を含む7日間を表示
10時～20時 30分単位(Flatpickr設定)
Livewireで作成
calendar.blade.php

ルートのwelcomeをcalendarに変更
layouts/app.blade.phpからlivewire, mix() などをコピー
flatpickrはevents/create.blade.phpからコピー

resources/js/flatpickr.js

latpickr("#calendar", {
  "locale": Japanese,
  minDate: "today",
  maxDate: new Date().fp_incr(30) 
});

const setting = {
  minuteIncrement: 30 // 追記
}
```

# 88. livewier Calendar作成

``` php
Livewireでカレンダー

php artisan make:livewire Calendar
app/Http/Livewire/Calendar.php
resources/views/livewire/calender.blade.phpが生成

app/Http/Livewire/Calendar.php

use Carbon\Carbon;
class Calendar extends Component
{
  public $currentDate; 
  public $day; 
  public $currentWeek;

  public function mount()
  {
    $this->currentDate = Carbon::today();
    $this->currentWeek = [];
    for($i = 0; $i < 7; $i++ )
    {
    $this->day = Carbon::today()->addDays($i)->format('m月d日');
    array_push($this->currentWeek, $this->day );
    }
  // dd($this->currentWeek);
  }
}

livewire/calendar.blade.php

<div>
  <x-jet-input id="calendar" class="block mt-1 w-ful" type="text" name="calendar" />
  {{ $currentDate }}
  <div class="flex">
    @for ($day = 0; $day < 7; $day++)
    {{ $currentWeek[$day] }}
    @endfor
  </div>
</div>

views/calendar.blade.php

@livewire('calendar') // コンポーネント読み込み
```

# 89. wire:changeで日付を更新

``` php

datepickerを変更したら値も変える

views/livewire/calendar.blade.php

<input id="calendar" class="block mt-1 w-ful"　type="text" name="calendar"　value="{{ $currentDate }}"　wire:change="getDate($event.target.value)"/>

app/Http/Livewire/Calendar.php
public function getDate($date)
{
$this->currentDate = $date; //文字列
$this->currentWeek = [];
for($i = 0; $i < 7; $i++ )
{
$this->day = Carbon::parse($this->currentDate)->addDays($i)-
>format('m月d日'); // parseでCarbonインスタンスに変換後 日付を加算
array_push($this->currentWeek, $this->day );
}
}

```

# 90. whereBetweenで指定期間のイベントを取得

``` php
選んだ日から7日分のイベント取得

ダミーデータが過去の日付が多い関係で、
一旦カレンダーを過去日も選択できるようにします。

resources/js/flatpickr.js
flatpickr("#calendar", {
"locale": Japanese,
// minDate: “today", //コメントアウト
maxDate: new Date().fp_incr(30)
});

イベント情報の取得
コードが長くなるので、Serviceに切り離すことにします。

App/Services/EventService.php
public static function getWeekEvents($startDate, $endDate)
{
$reservedPeople = DB::table('reservations')
->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
->groupBy('event_id');

return DB:table('events')
->leftJoinSub($reservedPeople, 'reservedPeople', function($join){
$join->on('events.id', '=', 'reservedPeople.event_id');
})
->whereBetween('start_date', [$startDate, $endDate])
->orderBy('start_date', 'asc')
->get();
}

Livewire/Calendar.php

use App/Services/EventService;
public $sevenDaysLater; public $events; // 追加
public function mount()
{
$this->currentDate = Carbon::today();
$this->sevenDaysLater = $this->currentDate->addDays(7);

$this->events = EventService::getWeekEvents(
$this->currentDate->format('Y-m-d'),
$this->sevenDaysLater->format('Y-m-d')
);

dd($this->events);

```

# 91. CarbonImmutable

``` php
初期表示で7日増えていた問題

Carbonはミュータブル(可変)とイミュータブル(不変)がある
デフォルトはミュータブル。
$this->currentDate = Carbon:today(); // こっちも変わってしまう
$this->sevenDaysLater = $this->currentDate->addDays(7);

対策1 ->copy()を使ってコピーしてから処理する
$this->currentDate = Carbon:today();
$this->sevenDaysLater = $this->currentDate->copy()->addDays(7);

対策2 イミュータブル版を使う
use Carbon\CarbonImmutable;
Carbonの箇所を CarbonImmutable に変更する

```

# 93. ダミーデータの修正

``` php

10時～20時 30分単位
$availableHour = $this->faker->numberBetween(10, 18); //10時～18時
$minutes = [0, 30]; // 00分か 30分
$mKey = array_rand($minutes); //ランダムにキーを取得
$addHour = $this->faker->numberBetween(1, 3); // イベント時間 1時間～3時間
$dummyDate = $this->faker->dateTimeThisMonth; // 今月分をランダムに取得
$startDate = $dummyDate->setTime($availableHour, $minutes[$mKey]);
$clone = clone $startDate; // そのままmodifyするとstartDateも変わるためコピー
$endDate = $clone->modify('+'.$addHour.'hour');
return [
略
'start_date' => $startDate,
'end_date' => $endDate,
];

```
