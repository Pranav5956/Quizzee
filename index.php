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

		$delete_modal_name = null;
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
		$selectQuizQuery = $conn->prepare("SELECT uqid, qname, type, create_time
																			 FROM quizzes
																			 WHERE uid <> :uid");
		$selectQuizQuery->execute(array(
			":uid" => $_SESSION['USERID']
		));
		$available_quizzes = $selectQuizQuery->fetchAll(PDO::FETCH_ASSOC);

		// Fetch groups
		$selectGroups = $conn->prepare("SELECT groups.gname, groups.gdesc, groups.ugid, user_group.is_admin
																		FROM user_group
																		JOIN groups
																		ON groups.gid = user_group.gid
																		WHERE user_group.uid = :uid");
		$selectGroups->execute(array(
			":uid" => $_SESSION['USERID']
		));
		$groups = $selectGroups->fetchAll(PDO::FETCH_ASSOC);
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
<?php if (isset($_SESSION['USERID'])): ?>
	<link rel="stylesheet" href="../modal/modal.css">
	<script type="text/javascript" src="../modal/modal.js"></script>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="z-index:1;">
	<a class="navbar-brand" href=<?php echo $_SERVER['REQUEST_URI'] ?>>Quizzee</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav ml-auto">
			<div class="dropdown">
			  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Signed In as <?php echo htmlentities($_SESSION['NAME'], ENT_QUOTES, 'utf-8'); ?>
			  <span class="caret"></span></button>
			  <ul class="dropdown-menu bg-primary">
					<?php if (isset($_SESSION['PROFILE-PICTURE'])): ?>
						<img src=<?php echo $_SESSION['PROFILE-PICTURE'] ?> alt="Profile Image" class="rounded-circle img-fluid" style="width: 110px; height: 110px;">
					<?php else: ?>
						<img src="https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png" alt="No Profile Image" class="rounded-circle" style="width:110px;height:110px;">
					<?php endif; ?>
						<form>
							<?php if ($_SESSION['TYPE'] == 'LOGIN'): ?>
								<li><button type="submit" name="action" value="change-profile-pic"
											      title="Click to update Profile Picture" class="btn btn-primary" formaction="profile">Change Profile Picture</button></li>
							<?php endif; ?>
							<li><input type="submit" name="logout-submit" value="Logout"
										     title="Click to Logout" class="btn btn-primary" formaction="../includes/logout.inc.php" formmethod="post"></li>
						</form>
				  </ul>
				</div>
			</ul>
		</div>
	</nav>
	<?php else: ?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="z-index:1;">
			<a class="navbar-brand" href=<?php echo $_SERVER['REQUEST_URI'] ?>>Quizzee</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav ml-auto">
					<a class="nav-link btn btn-dark" href="login"> Login </a><br>
					<a class="nav-link btn btn-dark" href="signup"> Signup </a>
					</ul>
				</div>
			</nav>

	<?php endif; ?>

<?php if (isset($_SESSION['USERID'])): ?>
		<link rel="stylesheet" href="../dashboard_style.css">
		<div class="row heading"><div class="col-3"></div><div class="col-6">Created Quizzes</div></div>
		<div class="row">
			<div class="col-3"></div>
			<div class="col-9">
				<?php if (isset($created_quizzes) && count($created_quizzes)): ?>
					<?php foreach ($created_quizzes as $quiz_index => $quiz_attributes): ?>
						<div class="card float-left created-quiz">
							<div class="card-header"><a href=<?php echo 'quizzes/view/'.urlencode($quiz_attributes['uqid']) ?>> <?php echo htmlentities($quiz_attributes['qname'], ENT_QUOTES, 'utf-8'); ?> </a>&nbsp;&nbsp;&nbsp;&nbsp;</div>
							<div class="card-body">
								<a class="card-link btn btn-outline-success" href=<?php echo 'quizzes/responses/'.urlencode($quiz_attributes['uqid']) ?>> View Responses </a>&nbsp;&nbsp;&nbsp;&nbsp;
								<a class="card-link btn btn-outline-warning" href=<?php echo 'quizzes/edit/'.urlencode($quiz_attributes['uqid']) ?>> Edit </a>&nbsp;&nbsp;&nbsp;&nbsp;
								<a data-modal="delete-quiz" data-uqid=<?php echo $quiz_attributes['uqid'] ?> class="btn btn-outline-danger modal-trigger">Delete</a>
								<a class="card-link btn btn-outline-primary" href=<?php echo 'quizzes/export/'.urlencode($quiz_attributes['uqid']) ?>> PDF View/Export </a>&nbsp;&nbsp;&nbsp;&nbsp;
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
				<div class="card card-block d-flex quizTitle" onclick="return createQuiz();">
					<div class="card-body align-items-center d-flex justify-content-center" style="color:#007bff;">Create Quiz</div>
				</div>
			</div>
		</div>
		<div class="row heading"><div class="col-3"></div><div class="col-6">Available Quizzes</div></div>
		<div class="row">
			<div class="col-3"></div>
			<div class="col-9">
				<?php if (isset($available_quizzes) && count($available_quizzes)): ?>
					<?php foreach ($available_quizzes as $quiz_index => $quiz_attributes): ?>
						<div class="card float-left card-block d-flex quizTitle">
						<!-- <a class="card-body align-items-center d-flex justify-content-center" href=<?php
						// echo 'quizzes/authenticate/'.urlencode($quiz_attributes['uqid']) ?>><?php
						// echo htmlentities($quiz_attributes['qname'], ENT_QUOTES, 'utf-8'); ?></a> -->
						<a id="attempt-quiz-modal-trigger" class="card-body align-items-center d-flex justify-content-center modal-trigger"
						data-modal="attempt-quiz" data-uqid=<?php echo $quiz_attributes['uqid'] ?>>
							<?php echo htmlentities($quiz_attributes['qname'], ENT_QUOTES, 'utf-8'); ?>
						</a>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="row heading"><div class="col-3"></div><div class="col-6">Groups</div></div>
		<div class="row">
			<div class="col-3"></div>
			<div class="col-9">
				<?php if (!empty($groups)): ?>
					<?php foreach ($groups as $index => $attr): ?>
						<div class="group-details">
							<p><?php echo htmlentities("Group Name: ".$attr['gname'], ENT_QUOTES, 'utf-8'); ?></p>
							<p><?php echo htmlentities("Group Description: ".$attr['gdesc'], ENT_QUOTES, 'utf-8'); ?></p>
							<p><?php echo htmlentities("Status: ".(($attr['is_admin'] == 'Yes')? "Admin" : "Member"), ENT_QUOTES, 'utf-8'); ?></p>
							<button type="button" data-modal="delete-group" data-ugid=<?php echo $attr['ugid'] ?> class="btn btn-outline-primary modal-trigger">Delete Group</button>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<p>You are not part of any groups yet.</p>
				<?php endif; ?>
				<button type="button" id="create-group-modal-trigger" class="btn btn-primary modal-trigger" data-modal="create-group">Create Group</button>
			</div>
		</div>

		<div class="modal">
		  <!-- Modal content -->
		  <div class="modal-content">
		    <div class="modal-header">
		      <span class="modal-close">&times;</span>
		    </div>
		    <div class="modal-body">
		    </div>
		    <div class="modal-footer">
		    </div>
		  </div>
		</div>

<?php else: ?>
	<link rel="stylesheet" href="landing.css">
	<ul class="slideshow">
	 <li></li>
	 <li></li>
	 <li></li>
	 <li></li>
	 <li></li>
 </ul>
 <div class="row">
	 <div class="left-slideshow col-6">
	 <h1 class="bg-dark">Welcome to Quizzee, an Online Quiz Management System!</h1>
	 <h1 class="bg-dark">Create, attend, and review quizzes with ease. View quiz performance, quiz history and much more!</h1>
	 <h1 class="bg-dark">Built with integrated group facilities. Create groups to share and conduct quizzes</h1>
 </div><div class="right-slideshow col-6">
	 <h1 class="text-right bg-dark">Enabled with a two-way feedback system. Interact with quiz organisers and quiz takers.</h1>
	 <h1 class="text-right bg-dark">Login/Signup to get started!</h1>
 </div>
 </div>
</div>
<?php endif; ?>



<script type="text/javascript">
	function createQuiz() {
		console.log(1);
		window.location.assign("quizzes/create");
		return false;
	}
</script>
