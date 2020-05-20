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
	<title> Burse scolari </title>
	<meta charset="UTF-8">
</head>
<body>
<h1>Pagina de autentificare</h1>
<div>
	<form method="POST">
		<div style = "color:red;">
			<?php foreach ($errors as $error) :?>
				<p><?php echo $error;?></p>
			<?php endforeach; ?>
		</div>
		<div>
			<label>Email:</label>
			<div>
				<input type="text" name="email" required="" value="<?php echo (!empty($_POST['email']) ? $_POST['email'] : '');?>"/>
			</div>
		</div>
		<div>
			<label>Parola:</label>
			<div>
				<input type="password" name="parola" required="" value=""/>
			</div>
		</div>
		<div>
			<br/>
			<input type="submit" name="submit" value="Login">
		</div>
		</form>
</div>
</body>
</html>