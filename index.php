<?php
	require_once "header.php";
	include_once "includes/utilities.inc.php";

	// Display Flash Messages
	//flash_message();

	// Retrieve cookie information
	if (isset($_COOKIE['USERID'])) {
		require_once "includes/db.inc.php";

		$_SESSION['USERID'] = $_COOKIE['USERID'];

		$login_query = $conn->prepare("SELECT * FROM Users
																	 WHERE uid=:uid");
		$login_query->execute(array(
			':uid' => $_SESSION['USERID']
		));
		$result = $login_query->fetch(PDO::FETCH_ASSOC);

		$_SESSION['NAME'] = $result['fname'].' '.$result['lname'];
		$_SESSION['PROFILE-PICTURE'] = $result['profile_pic'];
		$_SESSION['TYPE'] = $result['login'];
	}

	// Reroute based on user's login status
	if (!isset($_SESSION['USERID'])) {
		if (strpos($_SERVER['REQUEST_URI'], 'my/dashboard') !== false) {
			header("Location: ".str_replace("my/dashboard", "quizzee", $_SERVER['REQUEST_URI']));
			return;
		}
	} else {
		if (strpos($_SERVER['REQUEST_URI'], 'quizzee') !== false) {
			header("Location: ".str_replace("quizzee", "my/dashboard", $_SERVER['REQUEST_URI']));
			return;
		}

		// Fetch user's created quizzes
		require_once "includes/db.inc.php";
		$selectQuizQuery = $conn->prepare("SELECT uqid, qname
																			 FROM quizzes
																			 WHERE uid = :uid");
		$selectQuizQuery->execute(array(
			":uid" => $_SESSION['USERID']
		));
		$created_quizzes = $selectQuizQuery->fetchAll(PDO::FETCH_ASSOC);

		// Fetch available quizzes
		$selectQuizQuery = $conn->prepare("SELECT uqid, qname
																			 FROM quizzes
																			 WHERE uid <> :uid");
		$selectQuizQuery->execute(array(
			":uid" => $_SESSION['USERID']
		));
		$available_quizzes = $selectQuizQuery->fetchAll(PDO::FETCH_ASSOC);
	}
?>

<style>
.dropdown-menu li{
	margin: 10px;
}
img{
	margin:10px;
}
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand" href=<?php echo $_SERVER['REQUEST_URI'] ?>>Quizzee</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav ml-auto">
			<?php if (isset($_SESSION['USERID'])): ?>
				<div class="dropdown">
				  <button class="btn btn-dark dropdown-toggle" type="button" data-toggle="dropdown">Signed In as <?php echo htmlentities($_SESSION['NAME'], ENT_QUOTES, 'utf-8'); ?>
				  <span class="caret"></span></button>
				  <ul class="dropdown-menu bg-dark">
						<?php if (isset($_SESSION['PROFILE-PICTURE'])): ?>
							<img src=<?php echo $_SESSION['PROFILE-PICTURE'] ?> alt="Profile Image" class="rounded-circle img-fluid" style="width: 110px; height: 110px;">
						<?php else: ?>
							<img src="https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png" alt="No Profile Image" class="rounded-circle" style="width:110px;height:110px;">
						<?php endif; ?>
						<form>
							<?php if ($_SESSION['TYPE'] == 'LOGIN'): ?>
								<li><button type="submit" name="action" value="change-profile-pic"
											      title="Click to update Profile Picture" class="btn btn-dark" formaction="profile">Change Profile Picture</button></li>
							<?php endif; ?>
							<li><button type="submit" id="create" title="Create Quiz" class="btn btn-dark" onclick="return createQuiz();"> Create Quiz </button></li>
							<li><input type="submit" name="logout-submit" value="Logout"
										     title="Click to Logout" class="btn btn-dark" formaction="../includes/logout.inc.php" formmethod="post"></li>
							</form>
				  </ul>
				</div>

			<?php else: ?>
				<a class="nav-link btn btn-dark" href="login"> Login </a><br>
				<a class="nav-link btn btn-dark" href="signup"> Signup </a>
			<?php endif; ?>
		</ul>
	</div>
</nav>

<script type="text/javascript">
	function createQuiz() {
		console.log(1);
		window.location.assign("quizzes/create");
		return false;
	}
</script>

<?php if (isset($created_quizzes) && count($created_quizzes)): ?>
	<p>Created Quizzes:</p>
	<?php foreach ($created_quizzes as $quiz_index => $quiz_attributes): ?>
		<div class="quiz-link-container">
			<a href=<?php echo 'quizzes/view/'.urlencode($quiz_attributes['uqid']) ?>> <?php echo htmlentities($quiz_attributes['qname'], ENT_QUOTES, 'utf-8'); ?> </a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href=<?php echo 'quizzes/edit/'.urlencode($quiz_attributes['uqid']) ?>> Edit </a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href=<?php echo 'quizzes/delete/'.urlencode($quiz_attributes['uqid']) ?>> Delete </a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href=<?php echo 'quizzes/responses/'.urlencode($quiz_attributes['uqid']) ?>> View responses </a>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<?php if (isset($available_quizzes) && count($available_quizzes)): ?>
	<p>Available Quizzes:</p>
	<?php foreach ($available_quizzes as $quiz_index => $quiz_attributes): ?>
		<div class="quiz-link-container">
			<a href=<?php echo 'quizzes/authenticate/'.urlencode($quiz_attributes['uqid']) ?>> <?php echo htmlentities($quiz_attributes['qname'], ENT_QUOTES, 'utf-8'); ?> </a>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
