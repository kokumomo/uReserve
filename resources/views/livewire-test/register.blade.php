<html>

<head>
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Styles -->
  @livewireStyles
</head>

<body>
  livewire <span class="text-blue-600">register_test</span>
  @livewire('register')
  @livewireScripts
</body>

</html>