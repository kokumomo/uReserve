<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      イベント新規登録
    </h2>
  </x-slot>

  <form>
    <div class="mt-4">
      日付<input type="text" id="event_date" name="event_date">
    </div>

    <div class="mt-4">
      開始時間<input type="text" id="start_time" name="start_time">
    </div>
    <div class="mt-4">
      終了時間<input type="text" id="end_time" name="end_time">
    </div>
</form>
</x-app-layout>