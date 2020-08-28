<script
src="https://code.jquery.com/jquery-3.5.1.js"
integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
crossorigin="anonymous"></script>
<script type="text/javascript" src="quiz_creation.js"></script>

<!-- Quiz Editable -->
<div class="form-name-container">
  <input type="text" form="form-editable" id="quiz-name" name="quiz-name" placeholder="Enter the Quiz Name" form="form-editable" required>
</div>

<form id="form-editable" action="../includes/quiz.inc.php" method="post" class="quiz-form" style="float: left; width: 50%"
onsubmit="return validateQuiz();">
  <p id="form-editable-prompt" class="form-quiz-prompt">
    Get started by Clicking the 'Add Question' button
  </p>
  <div id="form-editable-buttons">
    <button type="button" id="add-question"> Add New Question </button>
    <button type="button" id="remove-question" hidden> Remove Last Question </button>
  </div>
  <div id="form-buttons">
    <input type="submit" name="save" value="Save">
  </div>
</form>

<!-- Quiz Preview -->
<form id="form-preview" class="quiz-form" style="float: left; width: 50%">
  <p id="form-preview-prompt" class="form-quiz-prompt">Add questions to see preview</p>
</form>
