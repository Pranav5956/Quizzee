// Global Variables
var question_count = 0;

// Macros
var QUESTION_TYPES = {
  'N': '--Please Choose a Question Type--',
  'MCQ': 'Multiple-Choice Single-Correct',
  'MCMQ': 'Multiple-Choice Multiple-Correct',
  'D': 'Descriptive',
  'TF': 'True/False'
};
var DEFAULT_OPTION_CONTEXT = (option_number) => "Option " + option_number;

/*
  DOM Class Structure (class names - id without question_number/option_number)

  form-editable
    form-quiz-prompt
    editable-question-container
      editable-question-description-container
        editable-question-description-input
        editable-question-description-prompt
      editable-question-type-container
        editable-question-type-label
        editable-question-type-select
          editable-question-type-select-option
      editable-question-attributes-container
        editable-question-type-prompt
        editable-question-option-container
          editable-question-option-input-container
            editable-question-option-input
          editable-question-option-buttons-container
            editable-question-option-buttons-add
            editable-question-option-buttons-remove
            editable-question-option-buttons-up
            editable-question-option-buttons-down
          editable-question-option-isanswer-container
            editable-question-option-isanswer
            editable-question-option-isanswer-label
      editable-question-buttons-container
        editable-question-button-clear

  form-preview
    form-quiz-prompt
    preview-question-container
      preview-question-description-container
        preview-question-description
      preview-question-attributes-container
        preview-question-type-prompt
        preview-question-option-container
          preview-question-option-display
          preview-question-option-label
*/

$(document).ready(function() {
  // Receive GET Parameters
  let get = window.location.search.split('?');
  if (get.length > 1) {
    let uri_params = get[1].split('&');
    let params = {};
    $.each(uri_params, function(index) {
      let param = uri_params[index].split('=');
      params[param[0]] = param[1];
    });

    if ('action' in params) {
      $('#add-question').click(function() {
        if (question_count == 0) {
          $('#form-editable-prompt').remove();
          $('#form-preview-prompt').html('Preview:');
        }
        question_count++;
        if (question_count > 1)
          $("#remove-question").attr("hidden", false);

        createEditableQuestion(question_count);
        createPreviewQuestion(question_count);

        // Remove question button
        $("#remove-question")
        .click(function() {
          if (question_count > 1) {
            $("#editable-question-" + question_count + "-container").remove();
            $("#preview-question-" + question_count + "-container").remove();
            question_count--;
          }
          if (question_count == 1) {
            $(this).attr("hidden", true);
          }
        })
      });
    }
  }
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
  .attr("name", "editable-question-" + question_number + "-description")
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
  .attr("name", "editable-question-" + question_number + "-type")
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
      addMCOption("radio");
    } else if (question_type == "MCMQ") {
      addMCOption("checkbox");
    } else if (question_type == "D") {
      addDescriptiveOption();
      $("#editable-question-" + question_number + "-buttons-container").remove();
    } else if (question_type == "TF") {
      addMCOption("radio");
      addMCOption("radio");

      // True option
      $("#editable-question-" + question_number + "-option-1-input")
      .val("True")
      .attr("readonly", true);
      $("#preview-question-" + question_number + "-option-1-label")
      .text("True");
      $("#editable-question-" + question_number + "-option-1-buttons-container").remove();

      // False option
      $("#editable-question-" + question_number + "-option-2-input")
      .val("False")
      .attr("readonly", true);
      $("#preview-question-" + question_number + "-option-2-label")
      .text("False");
      $("#editable-question-" + question_number + "-option-2-buttons-container").remove();
    }

    $("#editable-question-" + question_number + "-buttons-container").remove();
    if (question_type == 'TF' || question_type == 'MCQ' || question_type == 'MCMQ') {
      // Create buttons for question
      $('<div></div>')
      .attr("id", "editable-question-" + question_number + "-buttons-container")
      .addClass("editable-question-buttons-container")
      .appendTo($("#editable-question-" + question_number + "-container"));

      $('<button></button>')
      .attr("type", "button")
      .attr("id", "editable-question-" + question_number + "-button-clear")
      .addClass("editable-question-button-clear")
      .text("Clear Selection")
      .click(function() {
        for (let i = 1; i <= option_count; i++) {
          $("#editable-question-" + question_number + "-option-" + i + "-isanswer-input").prop("checked", false);
          $("#preview-question-" + question_number + "-option-" + i + "-display").prop("checked", false);
        }
      })
      .appendTo("#editable-question-" + question_number + "-buttons-container");
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

  function addMCOption(option_type) {
    option_count++;
    let option_number = option_count;
    addOptionContainers();

    // Adding containers
    $('<div></div>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-buttons-container")
    .addClass("editable-question-option-buttons-container")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-container"));

    $('<div></div>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-container")
    .addClass("editable-question-option-isanswer-container")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-container"));

    // Create editable option Input
    $('<input></input>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-input")
    .attr("name", "editable-question-" + question_number + "-option-" + option_number)
    .addClass("editable-question-option-input")
    .attr("placeholder", DEFAULT_OPTION_CONTEXT(option_number) + " Description")
    .change(function() {
      if ($(this).val() == "")
        $("#preview-question-" + question_number + "-option-" + option_number + "-label")
        .text(DEFAULT_OPTION_CONTEXT(option_number));
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
    .click(function() {
      addMCOption(option_type);
      // Shift the options from the creation point to the end
      for (let i = option_count-1; i > option_number; i--) {
        // Change the editable and preview text
        $("#editable-question-" + question_number + "-option-" + (i + 1) + "-input")
        .val($("#editable-question-" + question_number + "-option-" + i + "-input").val());
        if ($("#editable-question-" + question_number + "-option-" + i + "-input").val() == "")
          $("#preview-question-" + question_number + "-option-" + (i + 1) + "-label")
          .text("Option " + (i + 1));
        else
          $("#preview-question-" + question_number + "-option-" + (i + 1) + "-label")
          .text($("#preview-question-" + question_number + "-option-" + i + "-label").text());

        // Change selected option
        $("#editable-question-" + question_number + "-option-" + (i + 1) + "-isanswer-input")
        .prop("checked", $("#editable-question-" + question_number + "-option-" + i + "-isanswer-input").prop("checked"));
        $("#preview-question-" + question_number + "-option-" + (i + 1) + "-display")
        .prop("checked", $("#preview-question-" + question_number + "-option-" + i + "-display").prop("checked"));
      }

      // Reset attributes in new option
      $("#editable-question-" + question_number + "-option-" + (option_number + 1) + "-input")
      .val("");
      $("#preview-question-" + question_number + "-option-" + (option_number + 1) + "-label")
      .text("Option " + (option_number + 1));
      $("#editable-question-" + question_number + "-option-" + (option_number + 1) + "-isanswer-input")
      .prop("checked", false);
      $("#preview-question-" + question_number + "-option-" + (option_number + 1) + "-display")
      .prop("checked", false);

      // Update buttons
      if (option_count > 1) {
        $("#editable-question-" + question_number + "-option-" + (option_count - 1) + "-button-down").attr("hidden", false);
      }
      $("#editable-question-" + question_number + "-option-" + option_count + "-button-up").attr("hidden", false);
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-buttons-container"));

    $('<button></button>')
    .attr("type", "button")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-button-remove")
    .addClass("editable-question-option-buttons-remove")
    .text("-")
    .click(function() {
      if (option_count > 1) {
        // Shift the options from the last to the creation point
        for (let i = option_number; i < option_count; i++) {
          // Change the editable and preview text
          $("#editable-question-" + question_number + "-option-" + i + "-input")
          .val($("#editable-question-" + question_number + "-option-" + (i + 1) + "-input").val());
          if ($("#editable-question-" + question_number + "-option-" + (i + 1) + "-input").val() == "")
            $("#preview-question-" + question_number + "-option-" + i + "-label")
            .text("Option " + i);
          else
            $("#preview-question-" + question_number + "-option-" + i + "-label")
            .text($("#preview-question-" + question_number + "-option-" + (i + 1) + "-label").text());

          // Change selected option
          $("#editable-question-" + question_number + "-option-" + i + "-isanswer-input")
          .prop("checked", $("#editable-question-" + question_number + "-option-" + (i + 1) + "-isanswer-input").prop("checked"));
          $("#preview-question-" + question_number + "-option-" + i + "-display")
          .prop("checked", $("#preview-question-" + question_number + "-option-" + (i + 1) + "-display").prop("checked"));
        }

        // Remove the last option
        $("#editable-question-" + question_number + "-option-" + option_count + "-container").remove();
        $("#preview-question-" + question_number + "-option-" + option_count + "-container").remove();
        option_count--;

        // Update buttons
        $("#editable-question-" + question_number + "-option-" + option_count + "-button-down").attr("hidden", true);
      }
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-buttons-container"));

    // Movement buttons
    $('<button></button>')
    .attr("type", "button")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-button-up")
    .attr("hidden", true)
    .addClass("editable-question-option-buttons-up")
    .html("&#x25B2;")
    .click(function() {
      swapOptions(option_number, option_number - 1);
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-buttons-container"));

    $('<button></button>')
    .attr("type", "button")
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-button-down")
    .attr("hidden", true)
    .addClass("editable-question-option-buttons-down")
    .html("&#x25BC;")
    .click(function() {
      swapOptions(option_number, option_number + 1);
    })
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-buttons-container"));

    // Create the correct answer? buttons
    $('<input></input>')
    .attr("type", option_type)
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-isanswer-input")
    .attr("name", "editable-question-" + question_number + "-option-isanswer")
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
    .attr("type", option_type)
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-display")
    .attr("disabled", true)
    .attr("name", "preview-question-" + question_number + "-options")
    .addClass("preview-question-option-display")
    .appendTo($("#preview-question-" + question_number + "-option-" + option_number + "-container"));

    $('<label></label>')
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-label")
    .attr("for", "preview-question-" + question_number + "-option-" + option_number + "-display")
    .addClass("preview-question-option-label")
    .text(DEFAULT_OPTION_CONTEXT(option_number))
    .appendTo($("#preview-question-" + question_number + "-option-" + option_number + "-container"));
  }

  function addDescriptiveOption() {
    option_count++;
    let option_number = option_count;
    addOptionContainers();

    // Add the input
    $('<textarea></textarea>')
    .attr("id", "editable-question-" + question_number + "-option-" + option_number + "-input")
    .attr("name", "editable-question-" + question_number + "-option-" + option_number)
    .attr("placeholder", "Answer goes here")
    .attr("readonly", true)
    .width(390)
    .height(75)
    .addClass("editable-question-option-descriptive-input")
    .appendTo($("#editable-question-" + question_number + "-option-" + option_number + "-input-container"));

    // preview
    $('<textarea></textarea>')
    .attr("id", "preview-question-" + question_number + "-option-" + option_number + "-display")
    .attr("disabled", true)
    .attr("name", "preview-question-" + question_number + "-options")
    .width(390)
    .height(75)
    .addClass("preview-question-option-display")
    .appendTo($("#preview-question-" + question_number + "-option-" + option_number + "-container"));
  }

  function swapOptions(swap_from_option, swap_to_option) {
    if (swap_to_option <= option_count && swap_to_option >= 1) {
      let swap_to_input = $("#editable-question-" + question_number + "-option-" + swap_to_option + "-input").val();
      let swap_to_label = $("#preview-question-" + question_number + "-option-" + swap_to_option + "-label").text();
      let swap_to_edit_checked = $("#editable-question-" + question_number + "-option-" + swap_to_option + "-isanswer-input").prop("checked");
      let swap_to_preview_checked = $("#preview-question-" + question_number + "-option-" + swap_to_option + "-display").prop("checked");

      // Swap option-to changes
      $("#editable-question-" + question_number + "-option-" + swap_to_option + "-input")
      .val($("#editable-question-" + question_number + "-option-" + swap_from_option + "-input").val());
      if ($("#editable-question-" + question_number + "-option-" + swap_from_option + "-input").val() == "")
        $("#preview-question-" + question_number + "-option-" + swap_to_option + "-label")
        .text("Option " + swap_to_option);
      else
        $("#preview-question-" + question_number + "-option-" + swap_to_option + "-label")
        .text($("#preview-question-" + question_number + "-option-" + swap_from_option + "-label").text());

      $("#editable-question-" + question_number + "-option-" + swap_to_option + "-isanswer-input")
      .prop("checked", $("#editable-question-" + question_number + "-option-" + swap_from_option + "-isanswer-input").prop("checked"));
      $("#preview-question-" + question_number + "-option-" + swap_to_option + "-display")
      .prop("checked", $("#preview-question-" + question_number + "-option-" + swap_from_option + "-display").prop("checked"));

      // Swap option-from changes
      $("#editable-question-" + question_number + "-option-" + swap_from_option + "-input")
      .val(swap_to_input);
      if ($("#editable-question-" + question_number + "-option-" + swap_from_option + "-input").val() == "")
        $("#preview-question-" + question_number + "-option-" + swap_from_option + "-label")
        .text("Option " + swap_from_option);
      else
        $("#preview-question-" + question_number + "-option-" + swap_from_option + "-label")
        .text(swap_to_label);

      $("#editable-question-" + question_number + "-option-" + swap_from_option + "-isanswer-input")
      .prop("checked", swap_to_edit_checked);
      $("#preview-question-" + question_number + "-option-" + swap_from_option + "-display")
      .prop("checked", swap_to_preview_checked);
    }
  }
}

function createPreviewQuestion(question_number) {
  // Create the question container
  $('<div></div>')
  .attr("id", "preview-question-" + question_number + "-container")
  .addClass("preview-question-container")
  .appendTo($('#form-preview'));

  // Attach description container
  $('<div></div>')
  .attr("id", "preview-question-" + question_number + "-description-container")
  .addClass("preview-question-description-container")
  .appendTo($("#preview-question-" + question_number + "-container"));

  $('<p></p>')
  .attr("id", "preview-question-" + question_number + "-description")
  .text(question_number + ". Question Description")
  .addClass("preview-question-description")
  .appendTo($("#preview-question-" + question_number + "-description-container"));

  // Attach attributes container
  $('<div></div>')
  .attr("id", "preview-question-" + question_number + "-attributes-container")
  .addClass("preview-question-attributes-container")
  .appendTo($("#preview-question-" + question_number + "-container"));
}

// VALIDATION
function validateQuiz() {
  let isValid = true;
  $(".editable-question-description-input").each(function() {
    if ($(this).val() == "") {
      alert("Fill all the Question Descriptions");
      isValid = false;
      return false;
    }
  })

  $(".editable-question-type-select").each(function() {
    if ($(this).children("option:selected").val() == "N") {
      alert("Choose a question type");
      isValid = false;
      return false;
    }
  })

  $(".editable-question-option-input").each(function() {
    if ($(this).val() == "") {
      alert("Fill all the Option Descriptions");
      isValid = false;
      return false;
    }
  })

  return isValid;
}
