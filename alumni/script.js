const hidden_details_btn = document.getElementById("hidden_details");
const displayed_details = document.getElementById("displayed_details");

// hidden_details_btn.addEventListener("click", (e) => {
//   const fetched_details = document.getElementById("socialdetails");
//   const show_posts = document.getElementById("post");
//   fetched_details.style.display = "none";
//   show_posts.style.display = "block";
// });

displayed_details.addEventListener("click", (e) => {
  const fetched_details = document.getElementById("socialdetails");
  const show_posts = document.getElementById("post");
  fetched_details.style.display = "block";
  show_posts.style.display = "none";
});

// check the password pass the some checks
document
  .getElementById("registerForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    console.log("Form is submitted");
    const password = document.getElementById("password").value;
    const message = document.getElementById("message");

    const hasUpperCase = /[A-Z]/.test(password);
    const hasNumbers = /[0-9]/.test(password);
    const hasSpecialSymbol = /[^a-zA-Z0-9]/.test(password);
    const isLength = password.trim().length >= 8;

    if (password.trim() == "") {
      message.textContent = "Please Enter a password";
      message.style.color = "red";
      return;
    }

    if (!hasUpperCase || !hasNumbers || !hasSpecialSymbol || !isLength) {
      message.textContent =
        "Password contains one upperCase and one special symbol and one number with 8 characters";
      message.style.color = "red";
      return;
    }

    e.target.submit();
  });
