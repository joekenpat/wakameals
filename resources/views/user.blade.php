<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Wakameals</title>
  <meta name="theme-color" content="#010101" />

  <link rel="icon" href="/images/misc/favicon.png" />
  <link rel="apple-touch-icon" href="images/misc/favicon.png" />
  <link rel="manifest" href="manifest.json" />
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

<body>
  <div id="user_root"></div>
</body>
<script src="{{mix('js/manifest.js')}}"></script>
<script src="{{mix('js/vendor.js')}}"></script>
<script src="{{mix('js/user.min.js')}}"></script>
<!--Start of Tawk.to Script-->

<!--End of Tawk.to Script-->

</html>
