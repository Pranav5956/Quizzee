window.onload = function() {
  (new URL(window.location.href)).searchParams.forEach((x, y) =>
    document.getElementById(y).value = x);


  var email_regex = /$[\w\d\.]*@[\w\d]*\.com^/;

  var email = document.getElementById("email");
  email.addEventListener("change", function() {
    if (!email_regex.test(email.value)) {
      console.log("Invalid Email");
    }
  });

  var password = document.getElementById("pwd");
  var confirm_password = document.getElementById("cpwd");
  confirm_password.addEventListener("change", function() {
    if (confirm_password.value != password.value) {
      console.log("Type the correct password");
    }
  });
};
