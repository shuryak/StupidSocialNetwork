<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="shortcut icon" href="../application/assets/images/favicon.ico">
  <link rel="stylesheet" href="../application/assets/styles/universal.css">
  <link rel="stylesheet" href="../application/assets/styles/main.css">
  <link rel="stylesheet" href="../application/assets/styles/login.css">
  <link rel="stylesheet" href="../application/assets/styles/modals.css">
  <?php
  foreach($scripts as $i) {
    echo '<script defer src="../application/assets/javascripts/'.$i.'"></script>';
  }
  ?>
  <title><?php echo $title; ?></title>
</head>
<body>
  <div class="head">
    <h1 class="head__title">SSN</h1>
  </div>

  <div class="main-wrapper">
    <div class="page">
      <?php echo $content; ?>
    </div>
  </div>

  <div class="modals"></div>
</body>
</html>