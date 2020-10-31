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
		$_SESSION['UUID'] = $result['uuid'];

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
																			 WHERE uid <> :uid AND type <> 'G'");
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


<?php if (isset($_SESSION['USERID'])): ?>
	<link rel="stylesheet" href="../modal/modal.css">
	<script type="text/javascript" src="../modal/modal.js"></script>

<nav class="navbar navbar-expand-lg">
	<a class="navbar-brand" href=<?php echo $_SERVER['REQUEST_URI'] ?>>
		<img src="../Resources/Images/logo.png" alt="Quizzee Logo" style="width:28%; margin-top:-20px; margin-bottom:-20px;">
	</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav ml-auto">
			<div class="dropdown">
			  <button class="signedIn dropdown-toggle" type="button" data-toggle="dropdown">Signed In as <?php echo htmlentities($_SESSION['NAME'], ENT_QUOTES, 'utf-8'); ?>
			  <span class="caret"></span></button>
			  <div class="dropdown-menu text-center" style="background:#01596f;">
					<?php if (isset($_SESSION['PROFILE-PICTURE'])): ?>
						<img src=<?php echo $_SESSION['PROFILE-PICTURE'] ?> alt="Profile Image" class="rounded-circle img-fluid" style="width: 110px; height: 110px;">
					<?php else: ?>
						<img src="https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png" alt="No Profile Image" class="rounded-circle" style="width:110px;height:110px;">
					<?php endif; ?>
					<div class="dropdown-header text-center">
						<p class="text-light text-center mb-1">Copy User-ID</p>
						<div class="input-group">
							<input id="uuidBox" type="text" value=<?php echo $_SESSION['UUID'] ?> style="border: 1px solid white;" class="form-control form-control-plaintext bg-light" readonly length="9">
							<div class="input-group-append" title="Copy User-ID">
								<div class="input-group-text">
									<span id="uuidCopy" class="fa fa-copy fa-fw" role="button"></span>
								</div>
							</div>
						</div>
					</div>
						<form>
							<?php if ($_SESSION['TYPE'] == 'LOGIN'): ?>
								<a title="Click to view your Profile" class="profileBtn btn dropdown-item" href="profile">My Profile</a>
							<?php endif; ?>
							<input type="submit" name="logout-submit" value="Logout"
										     title="Click to Logout" class="signedIn btn dropdown-item" formaction="../includes/logout.inc.php" formmethod="post">
						</form>
				  </div>
				</div>
			</ul>
		</div>
	</nav>
	<?php else: ?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="z-index:1;">
			<a class="navbar-brand" href=<?php echo $_SERVER['REQUEST_URI'] ?>>
				<img src="Resources/Images/logo.png" alt="Quizzee Logo" style="width:28%; margin-top:-20px; margin-bottom:-20px;">
			</a>
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

<script type="text/javascript">
	$("#uuidCopy").click(function() {
		$("#uuidBox").select();
		document.execCommand("copy");
	});

	function display_information(id) {
		if (id != undefined && id != "") {
			$("#searchedGroupMembers").text('');
			$("#searchedUserProfilePic").attr("src", "https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png");
			
			if (id[0] == 'U') {
				$("#searchedQuiz *").text('');
				$("#searchedQuizAttempt-trigger").data("uqid", "");
				$("#searchedUser").show();
				$("#searchedQuiz").hide();
				$("#searchedGroup").hide();
				$.get("../includes/userinfo.inc.php", {"uuid": id}, function(response) {
					$("#searchResults").show();
					let user = JSON.parse(response);
					$("#searchTitle").text("USER INFORMATION");
					$("#searchDesc").text(user['fname'] + " " + user['lname'] + " (" + user['uuid'] + ")");
					$("#searchedUserFirstName").text("First Name: " + user['fname']);
					$("#searchedUserLastName").text("Last Name: " + user['lname']);
					$("#searchedUserEmail").text("Email ID: " + user['email']);
					$("#searchedUserLogin").text("Login Type: " + user['login']);
					if (user['profile_pic'] != null) {
						$("#searchedUserProfilePic").attr("src", user['profile_pic']);
					}
				});
			} else if (id[0] == "Q") {
				$("#searchedUser *").text('');
				$("#searchedUserProfilePic").attr("src", "https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png");
				$("#searchedQuiz").show();
				$("#searchedUser").hide();
				$("#searchedGroup").hide();
				$.get("../includes/quizinfo.inc.php", {"uqid": id}, function(response) {
					$("#searchResults").show();
					let quiz = JSON.parse(response);
					$("#searchTitle").text("QUIZ INFORMATION");
					$("#searchDesc").text(quiz['qname'] + " (" + quiz['uqid'] + ")");
					$("#searchedQuizName").text("Quiz Name: " + quiz['qname']);
					$("#searchedQuizCreation").text("Created on: " + Date(quiz['create_time'] * 1000));
					$("#searchedQuizType").text("Quiz Type: " + ((quiz['type'] == "O")? "Open": "Code-Protected"));
					$("#searchedQuizCreator").text("Created by: " + quiz['fname'] + " " + quiz['lname'] + " (" + quiz['uuid'] + ")");
					$("#searchedQuizAttempt-trigger").data("uqid", quiz['uqid']).text("Attempt");
				});
			} else if (id[0] == "G") {
				$("#searchedUser").hide();
				$("#searchedQuiz").hide();
				$("#searchedGroup").show();

				$.get("../includes/groupinfo.inc.php", {"ugid": id}, function(response) {
					$("#searchResults").show();
					let group = JSON.parse(response);
					$("#searchTitle").text("GROUP INFORMATION");
					$("#searchDesc").text(group['gname'] + " (" + group['ugid'] + ")");
					$("#searchedGroupName").text("Quiz Name: " + group['gname']);
					$("#searchedGroupCreation").text("Created on: " + Date(group['create_time'] * 1000));
					$("#searchedGroupDesc").text("Quiz Description: " + group['gdesc']);
					$("#searchedGroupCreator").text("Created by: " + group['creator']);

					group['members'].forEach((member, i) => {
						if (member["is_admin"] == "Yes") {
							$("#searchedGroupMembers").append(
								$("<li>")
								.text(member["member"] + " ")
								.append(
									$("<span>").addClass("fa fa-user-cog fa-fw")
								)
							);
						} else {
							$("#searchedGroupMembers").append(
								$("<li>")
								.text(member["member"])
							);
						}
					});

				});
			}
		} else {
			$("#searchedUser *").text('');
			$("#searchedUserProfilePic").attr("src", "https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png");
			$("#searchedQuiz *").text('');
			$("#searchedQuizAttempt-trigger").data("uqid", "");
			$("#searchedGroup *").text('');
			$("#searchedGroupMembers").text('');
			$("#searchResults").hide();
		}
	};

	function openCity(evt, tabName) {
		$(".tabcontent").css("display", "none");
		$(".tablinks").removeClass("active");
		$("#" + tabName).css("display", "block");
		$(evt.currentTarget).addClass("active");

		if (tabName != "groups") {
			if (tabName == "home") {
				$("#default").prop("selected", "true");
				$("#searchSelect").trigger("chosen:updated");
				display_information();
			}

			$("#group-panel-collapse").collapse("hide");
			$("#group-name").text("");
			$("#group-desc").text("");
			$("#group-create-time").text("");
		} else {
			$.get("../Groups/group_info.parse.php", {"ugid": $(evt.currentTarget).data()['ugid']}, function(response) {
	      group_info = JSON.parse(response);
				$("#group-name").text(group_info['gname']);
				$("#group-desc").text(group_info['gdesc']);
				let date = new Date(group_info['create_time'] * 1000);
				$("#group-create-time").text(date);
				$("#group-members").empty();
				$.get("../Groups/group_members.parse.php", {"ugid": group_info['ugid']}, function(res) {
					let members = JSON.parse(res);
					$.each(members, function(index, member) {
						if (member['is_admin'] == "Yes") {
							$("#group-members").append(
								$('<p>').text(member['fname'] + " " + member['lname'] + " (" + member['uuid'] + ") ")
								.append(
									$('<span>').addClass("fa fa-user-cog ml-2")
								)
							)
						} else {
							if (group_info['is_admin'] == "Yes") {
								$("#group-members").append(
									$('<p>').text(member['fname'] + " " + member['lname'] + " (" + member['uuid'] + ") ").css("display", "inline")
								).append(
									$('<a>').addClass("ml-2 mr-3").html("<i class='fa fa-user-cog fa-fw'></i>").attr("title", "Make Admin")
									.attr("href", "../Groups/make_admin.php?ugid=" + group_info['ugid'] + "&uuid=" + member['uuid'])
								).append(
									$('<a>').html("<i class='fa fa-user-slash fa-fw'></i>").attr("title", "Remove User")
									.attr("href", "../Groups/remove_users_from_group.php?ugid=" + group_info['ugid'] + "&uuid=" + member['uuid'])
								).append('<br><br>')
							} else {
								$("#group-members").append(
									$('<p>').text(member['fname'] + " " + member['lname'] + " (" + member['uuid'] + ") ")
								)
							}
						}
					});
				});
				if (group_info['is_admin'] == "Yes") {
					$("#group-delete-button").removeAttr("disabled");
					$("#edit-gname").removeAttr("readonly");
					$("#edit-gdesc").removeAttr("readonly");
					$("#edit-group-btn").removeAttr("disabled");
					$("#edit-group-form").attr("action", "../Groups/edit_group.php?ugid=" + group_info['ugid']);
					$("#edit-group-label").hide();
					$("#add-users-form").show();
					$("#group-delete-button").data('ugid', group_info['ugid']);
					$("#add-users-form").attr("action", "../Groups/add_users_to_group.php?ugid=" + group_info['ugid']);

					$.get("../includes/users_parse.inc.php", {"ugid": group_info['ugid']}, function(res) {
						$("#select-user-for-group").empty().append($(res));
						$("#select-user-for-group").trigger("chosen:updated");
					});
				}
				else {
					$("#group-delete-button").data('ugid', "");
					$("#add-users-form").attr("action", "");
					$("#group-delete-button").attr("disabled", "true");
					$("#edit-group-form").removeAttr("action");
					$("#add-users-form").hide();
					$("#group-members").empty();
					$("#group-quizzes-container").empty();
					$("#edit-gname").attr("readonly", "true");
					$("#edit-gdesc").attr("readonly", "true");
					$("#edit-group-btn").attr("disabled", "true");
					$("#edit-group-label").show();
				}
				$.get("../Groups/group_quiz_list.php", {"ugid": group_info['ugid']}, function(res) {
					$("#group-quizzes-container").empty();
					let quizzes = JSON.parse(res);
					if (quizzes.length == 0) {
						$('<p>').addClass('text-light').text("There are no quizzes for this group! Quizzes will appear as soon as the Group Admin creates them.")
						.appendTo($("#group-quizzes-container"));
					}
					$.each(quizzes, function(index, quiz) {
						$("<div>").addClass("card float-left card-block d-flex quizTitle").append(
							$("<a>").addClass("card-body align-items-center d-flex justify-content-center modal-trigger").text(quiz['qname'])
							.attr("data-modal", "attempt-quiz").attr("data-uqid", quiz['uqid']).click(function() {
								if (quiz['can_attempt']) fadeInModal("attempt-quiz", quiz['uqid']);
							})
						).appendTo($("#group-quizzes-container"));
					});
				});
			});
		}
	}
</script>


<?php if (isset($_SESSION['USERID'])): ?>
	<link rel="stylesheet" href="../dashboard_style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
		<div class="tab">
		<button class="tablinks active" onclick="openCity(event, 'home')">Home</button>
	  <button class="tablinks" onclick="openCity(event, 'availableQuizzes')">
			Available Quizzes <span class="badge badge-info"><?php echo count($available_quizzes) ?></button>
	  <button class="tablinks" onclick="openCity(event, 'createdQuizzes')">
			Created Quizzes <span class="badge badge-info"><?php echo count($created_quizzes) ?></button>
	  <button class="tablinks" data-toggle="collapse" data-target="#group-panel-collapse">
			Groups <span class="badge badge-info"><?php echo count($groups) ?></span></button>
		<div class="collapse" id="group-panel-collapse" style="max-height: 380px;">
			<?php foreach ($groups as $index => $attr): ?>
				<button type="button" data-ugid=<?php echo $attr['ugid'] ?> onclick="openCity(event, 'groups')" class="tablinks"><?php echo htmlentities($attr['gname'], ENT_QUOTES, 'utf-8'); ?></button>
			<?php endforeach; ?>
			<button type="button" id="create-group-modal-trigger" class="modal-trigger" data-modal="create-group"
							onclick=>Create Group</button>
		</div>
	</div>

	<div id="home" class="tabcontent show">
		<select id="searchSelect" class="chosen" data-placeholder="Search for users, quizzes or groups">
			<option id="default"></option>
			<optgroup id="usersOptGroup" label="Users">
			</optgroup>
			<optgroup id="quizzesOptGroup" label="Quizzes">
			</optgroup>
			<optgroup id="groupsOptGroup" label="Groups">
			</optgroup>
		</select>
		<div id="searchResults" style="display: none;">
			<h2 id="searchTitle"></h2>
			<h5 id="searchDesc"></h5>
			<div id="searchedUser">
				<img id="searchedUserProfilePic" style="width: 150px;" src="https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png" class="rounded-circle">
				<p id="searchedUserFirstName"></p>
				<p id="searchedUserLastName"></p>
				<p id="searchedUserEmail"></p>
				<p id="searchedUserLogin"></p>
			</div>
			<div id="searchedQuiz">
				<p id="searchedQuizName"></p>
				<p id="searchedQuizCreation"></p>
				<p id="searchedQuizType"></p>
				<p id="searchedQuizCreator"></p>
				<button id="searchedQuizAttempt-trigger" class="btn btn-primary modal-trigger"
				data-modal="attempt-quiz" data-uqid="">Attempt Quiz</button>
			</div>
			<div id="searchedGroup">
				<p id="searchedGroupName"></p>
				<p id="searchedGroupDesc"></p>
				<p id="searchedGroupCreator"></p>
				<p id="searchedGroupCreation"></p>
				<h4>Members:</h4>
				<ul id="searchedGroupMembers" class="list-unstyled">
				</ul>
			</div>
		</div>
	</div>

	<script>
		$.post("../includes/searchlist.inc.php", {"token": "123456789"}, function(response) {
			let res = JSON.parse(response);
			res.forEach((item, index) => {
				if (item["id"][0] == "U") {
					$("#usersOptGroup").append(
						$("<option>")
						.val(item['id'])
						.text(item['name'] + " (" + item['id'] + ")")
					);
				} else if (item["id"][0] == "Q") {
					$("#quizzesOptGroup").append(
						$("<option>")
						.val(item['id'])
						.text(item['name'] + " (" + item['id'] + ") - " + item["type"])
					);
				} else if (item["id"][0] == "G") {
					$("#groupsOptGroup").append(
						$("<option>")
						.val(item['id'])
						.text(item['name'] + " (" + item['id'] + ")")
					);
				}
			});
			$("#searchSelect").trigger("chosen:updated");
		});

		$("#searchSelect").change(function() {
			let selected = $(this).find(":selected");
			display_information(selected.val());
		});
	</script>

	<div id="availableQuizzes" class="tabcontent" style="display:none">

		<div class="row">
			<!-- <div class="col-3"></div> -->
			<div class="col-12">
				<?php if (isset($available_quizzes) && count($available_quizzes)): ?>
					<?php foreach ($available_quizzes as $quiz_index => $quiz_attributes): ?>
						<div class="card float-left card-block d-flex quizTitle">
						<a id="attempt-quiz-modal-trigger" class="card-body align-items-center d-flex justify-content-center modal-trigger"
						data-modal="attempt-quiz" data-uqid=<?php echo $quiz_attributes['uqid'] ?>>
							<?php echo htmlentities($quiz_attributes['qname'], ENT_QUOTES, 'utf-8'); ?>
						</a>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div id="createdQuizzes" class="tabcontent" style="display:none">
		<div class="row">
			<!-- <div class="col-3"></div> -->
			<div class="col-12">
				<?php if (isset($created_quizzes) && count($created_quizzes)): ?>
					<?php foreach ($created_quizzes as $quiz_index => $quiz_attributes): ?>
						<div class="card float-left created-quiz">
							<div class="card-header"><a href=<?php echo 'quizzes/view/'.urlencode($quiz_attributes['uqid']) ?>> <?php echo htmlentities($quiz_attributes['qname'], ENT_QUOTES, 'utf-8'); ?> </a>&nbsp;&nbsp;&nbsp;&nbsp;</div>
							<div class="card-body">

							<a class="card-link btn btn-outline-success" href=<?php echo 'quizzes/responses/'.urlencode($quiz_attributes['uqid']) ?>> View Responses </a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a class="card-link btn btn-outline-warning" href=<?php echo 'quizzes/edit/'.urlencode($quiz_attributes['uqid']) ?>> Edit </a>&nbsp;&nbsp;&nbsp;&nbsp;
								<a class="card-link btn btn-outline-info" href=<?php echo 'quizzes/export/'.urlencode($quiz_attributes['uqid']) ?>> PDF View/Export </a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a data-modal="delete-quiz" data-uqid=<?php echo $quiz_attributes['uqid'] ?> class="btn btn-outline-danger modal-trigger">Delete</a>


							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
				<div class="card card-block d-flex quizTitle" onclick="return createQuiz();">
					<div class="card-body align-items-center d-flex justify-content-center" style="color:#007bff;">Create Quiz</div>
				</div>
			</div>
		</div>
	</div>
	<div id="groups" class="tabcontent" style="display:none;">
		<div class="row">
			<!-- <div class="col-3"></div> -->
			<div class="col-12 pr-0">
				<div class="jumbotron pb-3 mr-0 mb-0">
				<h1 id="group-name" class="d-flex justify-content-center"></h1>
				<h4 id="group-desc"></h4>
				<p id="group-create-time"></p>
				<ul class="nav nav-tabs">
					<li class="nav-item mr-5 mt-3"><a data-toggle="pill" href="#menu1" class="nav-link group-links">Quizzes</a></li>
					<li class="nav-item mr-5 mt-3"><a data-toggle="pill" href="#menu2" class="nav-link group-links">Members</a></li>
					<li class="nav-item mt-3"><a data-toggle="pill" href="#menu3" class="nav-link group-links">Group</a></li>
				</ul>
				</div>
				<div class="container pl-4" style="height: 100vh;">
					<div>
					<div class="tab-content">
						<div id="menu1" class="tab-pane fade">
							<div id="group-quizzes-container"></div>
						</div>
						<div id="menu2" class="tab-pane fade">
							<form method="post" id="add-users-form" class="mb-4 ml-3">
								<h4>Add Members</h4>
								<select class="chosen" id="select-user-for-group" name="users[]" multiple data-placeholder="Enter Username or UserID"></select>
								<button type="submit" name="add-users-to-group" value="Add Users" class="btn btn-primary">
									<span class="fa fa-plus"></span>
								</button>
							</form>
							<h4 class="ml-3 mb-4">Group Members</h4>
							<div id="group-members" class="ml-3"></div>
						</div>
						<div id="menu3" class="tab-pane fade">
							<h4 class="ml-3">Group Information</h4>
							<p id="edit-group-label" class="text-light text-center col-6">
								(Only Group Administrator can change the Group Information. Contact the Group Administrator for any changes.)
							</p>
							<div id="group-edit-container">
								<form id="edit-group-form" method="post">
									<div class="form-group col-6">
										<input id="edit-gname" type="text" name="gname" placeholder="Enter new Group Name" class="form-control" required>
									</div>
									<div class="form-group col-6">
										<input id="edit-gdesc" type="text" name="gdesc" placeholder="Enter new Group Description" class="form-control" required>
									</div>
									<div class="btn-group offset-1 ml-3">
										<button type="submit" id="edit-group-btn" name="group-edit" value="group-update" class="btn btn-primary">
											<i class="fa fa-cog"></i> Update Group Details
										</button>
										<button type="button" id="group-delete-button" data-modal="delete-group" data-ugid="" class="modal-trigger btn btn-danger ml-4" style="display: block;">
											<i class="fa fa-trash"></i> Delete Group
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

<script type="text/javascript">
	function createQuiz() {
		console.log(1);
		window.location.assign("quizzes/create");
		return false;
	}
	$(".chosen").chosen({ width:"50%" }).addClass("ml-3");
</script>
