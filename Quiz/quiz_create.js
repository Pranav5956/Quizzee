var QUESTION_TYPES = {
  'N': '--Please Choose a Question Type--',
  'MCQ': 'Multiple-Choice Single-Correct',
  'MCMQ': 'Multiple-Choice Multiple-Correct',
  'D': 'Descriptive',
  'TF': 'True/False'
};
var question_count = 0;

$(document).ready(function() {
  $('#add-question').click(function() {
    if (question_count == 0) {
      $('#form-editable-prompt').remove();
      $('#form-preview-prompt').html('Preview:');
    }
    question_count++;

    createEditableQuestion(question_count);
    createPreviewQuestion(question_count);
  });
});

function createEditableQuestion(question_number) {
  // Create the question container
  $('<div></div>')
  .attr("id", "editable-question-" + question_number + "-container")
  .addClass("editable-question-container")
  .insertBefore($('#form-editable-buttons'));

  // Attach description containers and attributes
  $('<div></div>')
  .attr("id", "editable-question-" + question_number + "-description-container")
  .addClass("editable-question-description-container")
  .appendTo($("#editable-question-" + question_number + "-container"));

  $('<p></p>')
  .attr("id", "editable-question-" + question_number + "-description-prompt")
  .text(question_number + ". Enter Question Description: ")
  .addClass("editable-question-description-prompt")
  .appendTo($("#editable-question-" + question_number + "-description-container"));

  $('<textarea></textarea>')
  .attr("id", "editable-question-" + question_number + "-description-input")
  .attr("placeholder", "Enter Question Description here")
  .width(390)
  .height(75)
  .addClass("editable-question-description-input")
  .appendTo($("#editable-question-" + question_number + "-description-container"))
  .on("change", function() {
    if ($(this).val() == "")
      $("#preview-question-" + question_number + "-description")
      .text(question_number + ". Question Description");
    else
      $("#preview-question-" + question_number + "-description")
      .text(question_number + ". " + $(this).val());
  });

  // Attach the Question Type selection
  var option_count = 0;
  $('<div></div>')
  .attr("id", "editable-question-" + question_number + "-type-container")
  .addClass("editable-question-type-container")
  .appendTo($("#editable-question-" + question_number + "-container"));

  $('<label></label>')
  .attr("id", "editable-question-" + question_number + "-type-label")
  .attr("for", "editable-question-" + question_number + "-type-select")
  .text("Select a Question Type: ")
  .addClass("editable-question-type-label")
  .appendTo("#editable-question-" + question_number + "-type-container");

  $('<select></select>')
  .attr("id", "editable-question-" + question_number + "-type-select")
  .addClass("editable-question-type-select")
  .change(function() {
    addAttributesToQuestion($(this).children("option:selected").val(), question_number);
  })
  .ready(function() {
    addAttributesToQuestion('N', question_number);
  })
  .appendTo($("#editable-question-" + question_number + "-type-container"));

  $.each(QUESTION_TYPES, function(type_key, type_value) {
    $('<option></option>')
    .addClass('editable-question-type-select-option')
    .text(type_value)
    .val(type_key)
    .appendTo($("#editable-question-" + question_number + "-type-select"));
  });

  // Add the question attributes
  $('<div></div>')
  .attr("id", "editable-question-" + question_number + "-attributes-container")
  .addClass("editable-question-attributes-container")
  .appendTo($("#editable-question-" + question_number + "-container"));

  // Add Attributes and increase option count
  function addAttributesToQuestion(question_type) {
    // Clear all existing attributes
    option_count = 0;
    $("#editable-question-" + question_number + "-attributes-container").empty()
    $("#preview-question-" + question_number + "-attributes-container").empty()

    // Modify to contain basic attributes
    if (question_type == 'N') {
      $('<p></p>')
      .attr("id", "editable-question-" + question_number + "-attributes-prompt")
      .text("Choose a Question Type to add Input Options")
      .addClass("editable-question-type-prompt")
      .appendTo($("#editable-question-" + question_number + "-attributes-container"));

      $('<p></p>')
      .attr("id", "preview-question-" + question_number + "-attributes-prompt")
      .text("Choose a Question Type to display Input Options")
      .addClass("preview-question-type-prompt")
      .appendTo($("#preview-question-" + question_number + "-attributes-container"));
    } else if (question_type == "MCQ") {
      addMCQOption();
    } else if (question_type == "MCMQ") {
      addMCMQOption();
    } else if (question_type == "D") {
      addDescriptiveOption();
    } else if (question_type == "TF") {
      addMCQOption();
      addMCQOption();

      // True option
      $("#editable-question-" + question_number + "-option-1-input")
      .val("True")
      .attr("disabled", true);
      $("#preview-question-" + question_number + "-option-1-label")
      .text("True");
      $("#editable-question-" + question_number + "-option-1-buttons-container").remove();

      // False option
      $("#editable-question-" + question_number + "-option-2-input")
      .val("False")
      .attr("disabled", true);
      $("#preview-question-" + question_number + "-option-2-label")
      .text("False");
      $("#editable-question-" + question_number + "-option-2-buttons-container").remove();
    }
  }

  function addOptionContainers() {
    let option_number = option_count;
    // Editable Question Attribute containers
    $('<div></div>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-container")
    .addClass("editable-question-option-container")
    .appendTo($("#editable-question-" + question_number + "-attributes-container"));

    $('<div></div>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-input-container")
    .addClass("editable-question-option-input-container")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-container"));

    // Preview Question Attribute container
    $('<div></div>')
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-container")
    .addClass("preview-question-option-container")
    .appendTo($("#preview-question-" + question_number + "-attributes-container"));
  }

  function addMCOptionContainers() {
    let option_number = option_count;
    addOptionContainers();

    $('<div></div>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-buttons-container")
    .addClass("editable-question-option-buttons-container")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-container"));

    $('<div></div>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-container")
    .addClass("editable-question-option-isanswer-container")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-container"));
  }

  function addMCQOption() {
    option_count++;
    let option_number = option_count;
    addMCOptionContainers();

    // Create editable option Input
    $('<input></input>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-input")
    .addClass("editable-question-option-input")
    .attr("placeholder", "Option " + option_number + " Description")
    .change(function() {
      if ($(this).val() == "")
        $("#preview-question-" + question_number + "-option-" + option_number + "-label")
        .text("Option " + option_number);
      else {
        $("#preview-question-" + question_number + "-option-" + option_number + "-label")
        .text($(this).val());
      }
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-input-container"));

    // Add the buttons
    $('<button></button>')
    .attr("type", "button")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-button-add")
    .addClass("editable-question-option-buttons-add")
    .text("+")
    .click(addMCQOption)
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-buttons-container"));

    $('<button></button>')
    .attr("type", "button")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-button-remove")
    .addClass("editable-question-option-buttons-remove")
    .text("-")
    .click(function() {
      if (option_count > 1) {
        console.log($("#editable-question-" + question_number + "-option-" + option_count + "-container").children());
        $("#editable-question-" + question_number + "-option-" + option_count + "-container").remove();
        $("#preview-question-" + question_number + "-option-" + option_count + "-container").remove();
        option_count--;
      }
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-buttons-container"));

    // Create the correct answer? buttons
    $('<input></input>')
    .attr("type", "radio")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-input")
    .attr("name", "editable-question-" + question_number + "-isanswer")
    .addClass("editable-question-option-isanswer")
    .change(function() {
      // Uncheck everything else and check the currently selected
      for (var i = 1; i <= option_count; i++) {
        $("#preview-question-" + question_number + "-option-" + i + "-display").attr("checked", false);
      }
      $("#preview-question-" + question_number + "-option-" + option_number + "-display").attr("checked", true);
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-isanswer-container"));

    $('<label></label>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-label")
    .attr("for", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-input")
    .addClass("editable-question-option-isanswer-label")
    .text("Correct answer?")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-isanswer-container"));

    // Create preview option
    $('<input></input>')
    .attr("type", "radio")
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-display")
    .attr("disabled", true)
    .attr("name", "preview-question-" + question_number + "-options")
    .addClass("preview-question-option-display")
    .appendTo($("#preview-question-" + question_number + "-option-" + option_number + "-container"));

    $('<label></label>')
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-label")
    .attr("for", "preview-question-" + question_number + "-option-" + option_number + "-display")
    .addClass("preview-question-option-label")
    .text("Option " + option_number)
    .appendTo($("#preview-question-" + question_number + "-option-" + option_number + "-container"));
  }

  function addMCMQOption() {
    option_count++;
    let option_number = option_count;
    addMCOptionContainers();

    // Create editable option Input
    $('<input></input>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-input")
    .addClass("editable-question-option-input")
    .attr("placeholder", "Option " + option_number + " Description")
    .change(function() {
      if ($(this).val() == "")
        $("#preview-question-" + question_number + "-option-" + option_number + "-label")
        .text("Option " + option_number);
      else {
        $("#preview-question-" + question_number + "-option-" + option_number + "-label")
        .text($(this).val());
      }
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-input-container"));

    // Add the buttons
    $('<button></button>')
    .attr("type", "button")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-button-add")
    .addClass("editable-question-option-buttons-add")
    .text("+")
    .click(addMCMQOption)
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-buttons-container"));

    $('<button></button>')
    .attr("type", "button")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-button-remove")
    .addClass("editable-question-option-buttons-remove")
    .text("-")
    .click(function() {
      if (option_count > 1) {
        console.log($("#editable-question-" + question_number + "-option-" + option_count + "-container").children());
        $("#editable-question-" + question_number + "-option-" + option_count + "-container").remove();
        $("#preview-question-" + question_number + "-option-" + option_count + "-container").remove();
        option_count--;
      }
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-buttons-container"));

    // Create the correct answer? buttons
    $('<input></input>')
    .attr("type", "checkbox")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-input")
    .attr("name", "editable-question-" + question_number + "-isanswer")
    .addClass("editable-question-option-isanswer")
    .change(function() {
      $("#preview-question-" + question_number + "-option-" + option_number + "-display").prop("checked", $(this).prop("checked"));
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-isanswer-container"));

    $('<label></label>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-label")
    .attr("for", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-input")
    .addClass("editable-question-option-isanswer-label")
    .text("Correct answer?")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-isanswer-container"));

    // Create preview option
    $('<input></input>')
    .attr("type", "checkbox")
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-display")
    .attr("disabled", true)
    .attr("name", "preview-question-" + question_number + "-options")
    .addClass("preview-question-option-display")
    .appendTo($("#preview-question-" + question_number + "-option-" + option_number + "-container"));

    $('<label></label>')
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-label")
    .attr("for", "preview-question-" + question_number + "-option-" + option_number + "-display")
    .addClass("preview-question-option-label")
    .text("Option " + option_number)
    .appendTo($("#preview-question-" + question_number + "-option-" + option_number + "-container"));
  }

  function addDescriptiveOption() {
    option_count++;
    let option_number = option_count;
    addOptionContainers();

    // Add the input
    $('<textarea></textarea>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-input")
    .attr("placeholder", "Answer goes here")
    .attr("disabled", true)
    .addClass("editable-question-option-input")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-input-container"));

    // preview
    $('<textarea></textarea>')
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-display")
    .attr("disabled", true)
    .attr("name", "preview-question-" + question_number + "-options")
    .addClass("preview-question-option-display")
    .appendTo($("#preview-question-" + question_number + "-option-" + option_number + "-container"));
  }
}

function createPreviewQuestion(question_number) {
  // Create the question container
  $('<div></div>', {
    "id": "preview-question-" + question_number + "-container"
  })
  .addClass("preview-question-container")
  .appendTo($('#form-preview'));

  // Attach description container
  $('<div></div>', {
    "id": "preview-question-" + question_number + "-description-container"
  })
  .addClass("preview-question-description-container")
  .appendTo($("#preview-question-" + question_number + "-container"));

  $('<p></p>', {
    "id": "preview-question-" + question_number + "-description",
    "text": question_number + ". Question Description"
  })
  .addClass("preview-question-description")
  .appendTo($("#preview-question-" + question_number + "-description-container"));

  // Attach attributes container
  $('<div></div>', {
    "id": "preview-question-" + question_number + "-attributes-container"
  })
  .addClass("preview-question-attributes-container")
  .appendTo($("#preview-question-" + question_number + "-container"));
}
