<?php
require_once("config.php");
if (!empty($_SESSION['user_id'])){
  header("location: profil.php");
}
$errors = [];
if (!empty($_POST)){
  if (empty($_POST['email'])){
    $errors[] = 'Introduceti emailul';
  }
  if (empty($_POST['parola'])){
    $errors[] = 'Introduceti parola';
  }
  if (empty($errors)){
    $stmt = $dbConn->prepare("SELECT id_elev FROM elevi WHERE email = :email and parola = :parola");
    $stmt -> execute (array('email' => $_POST['email'], 'parola' => sha1($_POST['parola'].SALT)));
    $id_elev = $stmt -> fetchColumn();
    if (!empty($id_elev)) {
      $_SESSION['user_id'] = $id_elev;
      echo 'utilizator_id:' . $_SESSION['user_id'];
      header("location: profil.php");

    } else {
      $errors[] = 'Va rog Introduceti datele corecte';
    }
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    
    <title>Pagina administrare burse</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/sign-in/">

    <!-- Bootstrap core CSS -->
<link href="assets/dist/css/bootstrap.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">
  </head>
  <body class="text-center">
    <form class="form-signin" method="POST">
  <img class="mb-4" src="assets/img/book" alt="" width="72" height="72">
  <h1 class="h3 mb-3 font-weight-normal">Pagina de autentificare</h1>
  <label for="inputEmail" class="sr-only">Adresa de email</label>
  <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Adresa de email"
   required=required autofocus value="<?php echo (!empty($_POST['email']) ? $_POST['email'] : '');?>"/>

  <label for="inputPassword" class="sr-only">Parola</label>

  <input type="password" name="parola" id="inputPassword" class="form-control" placeholder="Parola" required>

  <a href="registration.php">Nu ai cont?</a>
  <div style = "color:red;">
      <?php foreach ($errors as $error) :?>
        <p><?php echo $error;?></p>
      <?php endforeach; ?>
    </div>
    <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Acces</button>
  <p class="mt-5 mb-3 text-muted">&copy; Catalin Binzari 2020</p>
</form>

<!-- Web Widget -->
<div id="pb-widget"></div>
<script>
  var bot_config = {
    PB_HOST: "home",
    PB_BOTKEY: "yIjx1p9cee0XMQgf3phR1XK1EXQIpuWq553kvbNPOWgg3Ab9DjazQKZa9xJhw0sRqYqgO8b8fE1FJ4zeixXoDA~~",
    title: "Ajutor",
    subtitle: "Ai vreo intrebare? Apasa aici!",
    colors: { theme: "#4da3ff", text: "#FFFFFF" },
    conversationOpener: "Buna, cu ce te pot ajuta?",
    descriptionTitle: "Ajutor",
    placeholderText: "Scrie un mesaj...",
    botAvatar: "https://cdn3.iconfinder.com/data/icons/chat-bot-emoji-blue-filled-color/300/14134081Untitled-3-512.png",
  }
</script>
<script src="https://widget.pandorabots.com/prod/pb-widget.js" type="text/javascript"></script>

</body>
</html>
