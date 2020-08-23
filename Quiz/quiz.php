<body>
  <script
  src="https://code.jquery.com/jquery-3.5.1.js"
  integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
  crossorigin="anonymous"></script>
  <script type="text/javascript" src="quiz_create.js"></script>

  <!-- Quiz Editable -->
	<form id="form-editable" class="quiz-form" style="float: left; width: 50%">
		<p id="form-editable-prompt" class="prompt">
      Start adding Questions by Clicking the 'Add Question' button
    </p>
    <div id="form-editable-buttons">
      <button type="button" id="add-question"> Add Question </button>
    </div>
	</form>

  <!-- Quiz Preview -->
	<form id="form-preview" class="quiz-form" style="float: left; width: 50%">
		<p id="form-preview-prompt" class="prompt">Add questions to see preview</p>
	</form>
</body>
