# 61. Event 新規登録

``` php
コンポーネントはcomponentsフォルダを作成し  
components/textarea.blade.php を作成  
<x-textarea>で使用できる  

index.blade.phpにボタン追加  
<button onclick="location.href='{{ route('events.create')}}'" class="flex mb-4 ml-auto text-white bg-pink-500 border-0 py-2 px-6 focus:outline-none hover:bg-pink-600 rounded">新規登録</button>

```

# 62, 63. 新規登録フォーム調整

``` php
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      イベント新規登録
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

        <div class="max-w-2xl py-4 mx-auto">
          <x-jet-validation-errors class="mb-4" />
          <form method="POST" action="{{ route('events.store') }}">
            @csrf

            <div>
              <x-jet-label for="event_name" value="イベント名" />
              <x-jet-input id="event_name" class="block mt-1 w-full" type="text" name="event_name" :value="old('event_name')" required autofocus />
            </div>
            <div class="mt-4">
              <x-jet-label for="information" value="イベント詳細" />
              <x-textarea row="3" id="information" name="information" class="block mt-1 w-full">{{ old('information')}}</x-textarea>
            </div>
            <div class="md:flex justify-between">
              <div class="mt-4">
                <x-jet-label for="event_date" value="イベント日付" />
                <x-jet-input id="event_date" class="block mt-1 w-full" type="text" name="event_date" required />
              </div>

              <div class="mt-4">
                <x-jet-label for="start_time" value="開始時間" />
                <x-jet-input id="start_time" class="block mt-1 w-full" type="text" name="start_time" required />
              </div>

              <div class="mt-4">
                <x-jet-label for="end_time" value="終了時間" />
                <x-jet-input id="end_time" class="block mt-1 w-full" type="text" name="end_time" required />
              </div>
            </div>
            <div class="md:flex justify-between items-end">
              <div class="mt-4">
                <x-jet-label for="max_people" value="定員数" />
                <x-jet-input id="max_people" class="block mt-1 w-full" type="number" name="max_people" required />
              </div>
              <div class="flex space-x-4 justify-around">
                <input type="radio" name="is_visible" value="1" checked />表示
                <input type="radio" name="is_visible" value="0" />非表示
              </div>
              <x-jet-button class="ml-4">
                新規登録
              </x-jet-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ mix('js/flatpickr.js')}}"></script>
</x-app-layout>
```

# 64. バリデーション

``` php

バリデーション 日本語化
lang/ja/validation.php

'attributes' => [
'email' => 'メールアドレス',
'password' => 'パスワード',
'name' => '名前',
'event_name' => 'イベント名',
'information' => 'イベント詳細',
'event_date' =>'イベントの日付',
'end_time' => '終了時間',
'start_time' => '開始時間',
'max_people' => '定員',
],

lang/ja.json
{"Whoops! Something went wrong.":"問題が発生しました。"}


フォームリクエスト
app/Http/Requests/StoreEventRequest.php

public function authorize()
{ return true; }

public function rules()
{ return [
'event_name' => ['required', 'max:50'],
'information' => ['required', 'max:200'],
'event_date' => ['required', 'date'],
'start_time' => ['required'],
'end_time' => ['required', 'after:start_time'],
'max_people' => ['required', 'numeric', 'between:1,20'],
'is_visible' => ['required', 'boolean']
];}
```
