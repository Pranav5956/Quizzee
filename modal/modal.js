$(document).ready(function() {
  $('.modal-trigger').click(fadeInModal);
  $('.modal-close').click(fadeOutModal);

  $(window).click(function(event) {
    if ($(event.target).hasClass('active')) {
      fadeOutModal();
    }
  });
})

function set_modal(data) {
  let modal = $(".modal");
  let header_pos = modal.find(".modal-close");
  let body_pos = modal.find(".modal-body");
  let footer_pos = modal.find(".modal-footer");

  console.log(data['modal']);

  if (data["modal"] == "attempt-quiz") {
    var uqid = data['uqid'];
    var quiz_info;
    $.post("../Quiz/quiz_details.parse.php", {"uqid": uqid}, function(response) {
      quiz_info = JSON.parse(response)[0];

      $('<h2>')
      .attr("class", "modal-extra")
      .text("Attempt Quiz")
      .insertBefore(header_pos);

      $('<h3>')
      .attr("class", "modal-extra")
      .text("Quiz Name: " + quiz_info['qname'])
      .appendTo(body_pos);

      let date = new Date(quiz_info['create_time'] * 1000);
      $('<h5>')
      .attr("class", "modal-extra")
      .text("Created on: " + date)
      .appendTo(body_pos);

      if (quiz_info['type'] == 'C') {
        $('<h5>')
        .attr("class", "modal-extra")
        .css("paddingTop", "24px")
        .text("This quiz is CODE-PROTECTED. Enter the Quiz code to attempt the quiz.")
        .appendTo(body_pos);
      } else if (quiz_info['type'] == 'G') {
        $('<h5>')
        .attr("class", "modal-extra")
        .css("paddingTop", "24px")
        .text("This quiz is restricted to members of this Group. Would you like to attempt this quiz?")
        .appendTo(body_pos);
      } else {
        $('<h5>')
        .attr("class", "modal-extra")
        .css("paddingTop", "24px")
        .text("This quiz is OPEN. Would you like to attempt this quiz?")
        .appendTo(body_pos);
      }

      $('<form>')
      .attr("id", "attempt-form")
      .attr("class", "modal-extra")
      .attr("action", "../my/quizzes/attempt/" + data['uqid'])
      .attr("method", "post")
      .submit(function(event) {
        if (quiz_info['type'] == 'C') {
          let code = $("#code-input").val();
          if (code != quiz_info['code']) {
            event.preventDefault();

            if (code == "")
              $("#modal-exception").text("Please provide a Quiz Code");
            else
              $("#modal-exception").text("Incorrect Quiz Code");

            $("#modal-exception").slideDown(function() {
              setTimeout(function() {
                $("#modal-exception").slideUp();
              }, 5000);
            });
          } else {
            return;
          }
        } else if (quiz_info['type'] == 'O') {
          return;
        }
      })
      .appendTo(body_pos);

      if (quiz_info['type'] == 'C') {
        $('<div>')
        .attr("id", 'code-input-container')
        .addClass("modal-extra form-group")
        .appendTo("#attempt-form")

        $('<input>')
        .attr("id", "code-input")
        .attr("type", "password")
        .attr("name", "code")
        .addClass('modal-extra form-control')
        .attr("placeholder", "Enter Quiz code")
        .appendTo("#code-input-container");

        $('<p>')
        .attr('id', 'modal-exception')
        .addClass('modal-extra')
        .css('color', 'red')
        .css('display', 'none')
        .appendTo("#code-input-container");
      }

      $('<input>')
      .attr("type", "submit")
      .addClass('modal-extra btn btn-primary')
      .attr("name", "attempt")
      .attr("value", "Attempt")
      .appendTo("#attempt-form");

      $('<p>')
      .attr("id", "modal-footer")
      .addClass("modal-extra")
      .text("Quizzes should be attended under the own discretion of the candidate. Quizzee will not be held responsible for any issues.")
      .appendTo(footer_pos);
    })
  } else if (data["modal"] == "delete-quiz") {
    let uqid = data['uqid'];
    $.post("../Quiz/quiz_details.parse.php", {"uqid": uqid}, function(response) {
      quiz_info = JSON.parse(response)[0];

      $('<h2>')
      .attr("class", "modal-extra")
      .text("Delete Quiz")
      .insertBefore(header_pos);

      $('<h3>')
      .attr("class", "modal-extra")
      .text("Quiz Name: " + quiz_info['qname'])
      .appendTo(body_pos);

      let date = new Date(quiz_info['create_time'] * 1000);
      $('<h5>')
      .attr("class", "modal-extra")
      .text("Created on: " + date)
      .appendTo(body_pos);

      $('<h5>')
      .attr("class", "modal-extra")
      .text("Type: " + ((quiz_info['type'] == "C") ? "Code-Protected" : "Open"))
      .appendTo(body_pos);

      $('<h5>')
      .attr("class", "modal-extra")
      .css("paddingTop", "24px")
      .text("Are you sure you want to delete " + quiz_info['qname'] + "?")
      .appendTo(body_pos);

      $('<a>')
      .attr("href", "../my/quizzes/delete/" + data['uqid'])
      .addClass('modal-extra btn btn-success')
      .text("Yes")
      .appendTo(body_pos);

      $('<a>')
      .addClass('modal-extra btn btn-danger modal-close')
      .text("No")
      .click(fadeOutModal)
      .appendTo(body_pos);

      $('<p>')
      .attr("id", "modal-footer")
      .addClass("modal-extra")
      .text("Deletion of a Quiz is non-revertable. Proceed under own consent. Quizzee will not be held responsible for any issues.")
      .appendTo(footer_pos);
    });
  } else if (data["modal"] == "create-group") {
    $('<h2>')
    .attr("class", "modal-extra")
    .text("Create New Group")
    .insertBefore(header_pos);

    $('<h3>')
    .attr("class", "modal-extra")
    .text("Customize your new group")
    .appendTo(body_pos);

    $('<form>')
    .attr("id", "create-group-form")
    .attr("class", "modal-extra")
    .attr("action", "../Groups/group_create.inc.php")
    .attr("method", "post")
    .submit(function(event) {

    })
    .appendTo(body_pos);

    $('<div>')
    .attr("id", 'name-input-container')
    .addClass("modal-extra form-group")
    .appendTo("#create-group-form")

    $('<input>')
    .attr("id", "name-input")
    .attr("name", "group-name")
    .addClass('modal-extra form-control')
    .attr("placeholder", "Enter Group name")
    .appendTo("#name-input-container");

    $('<div>')
    .attr("id", 'desc-input-container')
    .addClass("modal-extra form-group")
    .appendTo("#create-group-form")

    $('<input>')
    .attr("id", "desc-input")
    .attr("type", "textarea")
    .attr("name", "group-desc")
    .addClass('modal-extra form-control')
    .attr("placeholder", "Enter Group description")
    .appendTo("#desc-input-container");

    $('<input>')
    .attr("type", "submit")
    .addClass('modal-extra btn btn-primary')
    .attr("name", "create-group-submit")
    .attr("value", "Create Group")
    .appendTo("#create-group-form");

    $('<p>')
    .attr("id", "modal-footer")
    .addClass("modal-extra")
    .text("You will be made the Admin of any group you create. You can always pass it on to someone else.")
    .appendTo(footer_pos);
  } else if (data["modal"] == "delete-group") {
    let ugid = data['ugid'];
    $.get("../Groups/group_info.parse.php", {"ugid": ugid}, function(response) {
      group_info = JSON.parse(response);

      $('<h2>')
      .attr("class", "modal-extra")
      .text("Delete Group")
      .insertBefore(header_pos);

      $('<h3>')
      .attr("class", "modal-extra")
      .text("Group Name: " + group_info['gname'])
      .appendTo(body_pos);

      $('<h4>')
      .attr("class", "modal-extra")
      .text("Group Description: " + group_info['gdesc'])
      .appendTo(body_pos);

      let date = new Date(group_info['create_time'] * 1000);
      $('<h5>')
      .attr("class", "modal-extra")
      .text("Created on: " + date)
      .appendTo(body_pos);

      $('<h5>')
      .attr("class", "modal-extra")
      .css("paddingTop", "24px")
      .text("Are you sure you want to delete " + group_info['gname'] + "?")
      .appendTo(body_pos);

      $('<form>')
      .attr("id", "form-group-delete")
      .attr("action", "../Groups/group_delete.inc.php?ugid=" + ugid)
      .attr("method", "post")
      .appendTo(body_pos);

      $('<input>')
      .attr("type", "submit")
      .attr("name", "delete-group-submit")
      .addClass('modal-extra btn btn-success')
      .attr("Value", "Yes")
      .appendTo("#form-group-delete");

      $('<a>')
      .addClass('modal-extra btn btn-danger modal-close')
      .text("No")
      .click(fadeOutModal)
      .appendTo(body_pos);

      $('<p>')
      .attr("id", "modal-footer")
      .addClass("modal-extra")
      .text("Deletion of a Group is non-revertable. Proceed under own consent. Quizzee will not be held responsible for any issues.")
      .appendTo(footer_pos);
    });
  }
}

function fadeOutModal() {
  let modal = $(".modal.active");
  modal.removeClass("active");

  modal.animate({opacity: 0}, 300, "swing");
  modal.children().animate({
    top: "-300px",
    opacity: 0
  }, {
    duration: 300,
    easing: "swing",
    complete: function() {
      modal.css("display", "none");
      $('.modal-extra').remove();
    }
  })
}

function fadeInModal(text, uqid) {
  let modal = $(".modal");
  let data = $(this).data();
  if (jQuery.isEmptyObject(data))
    data = {'modal': text, 'uqid': uqid};

  set_modal(data);
  modal.css("display", "block");

  modal.animate({opacity: 1}, 300, "swing");
  modal.children().animate({
    opacity: 1,
    top: "0px"
  }, {
    duration: 300,
    easing: "swing",
    complete: function() {
      modal.addClass("active");
    }
  });
}
