# 76. 過去のイベント
``` php
ルーティング

現在、全てのイベントが表示されている
今日以降のイベントと
昨日以前のイベントで画面を切り替える
ルーティングは上から処理される
リソースの下に書くと /past部分がパラメータと勘違いされるので
リソースの上に書く

routes/web.php
Route:prefix('manager')
->middleware('can:manager-higher')->group(function(){
Route:get('events/past', [EventControler:class, 'past'])->name('events.past');
Route:resource('events', EventControler:class);
});

コントローラとビュー
コントローラ
public function past()
{
$today = Carbon:today();
$events = DB:table('events')
->whereDate('start_date', '<', $today )
->orderBy('start_date', 'desc')
->paginate(10);
return view('manager.events.past', compact('events'));
}

ビュー側 manager/events/past.blade.php
index.blade.phpを参考

```
# 77. indexイベントを本日以降のみ表示など
``` php
今日より前なら編集ボタンを消す

/manager/events/show.blade.php
// ddやvar_dumpで見るとわかりますが型が違うのでformatをかける
@if($event->eventDate >= \Carbon\Carbon:today()->format('Y年m月d日'))
<x-jet-button class="ml-4">
編集する
</x-jet-button>
@endif

EventControler@index

$today = Carbon:today();
$events = DB:table('events')
->whereDate('start_date', '>=' , $today) // 追加
->orderBy('start_date', 'asc')
->paginate(10);

過去イベントへのリンク

manager/events/index.blade.php
<div class="flex justify-between">
<button onclick="location.href='{{ route(‘events.past')}}'">
過去のイベント</button>
<button onclick="location.href='{{ route(‘events.create')}}'"">
新規登録</button>
</div>

```

# 78. 過去イベントはurl直接変更しても編集不可にする
``` php

EventControler@edit
public function edit(Event $event)
{
$event = Event:findOrFail($event->id);
$today = Carbon:today()->format('Y年m月d日');
if($event->eventDate < $today ){
return abort(404);
}
