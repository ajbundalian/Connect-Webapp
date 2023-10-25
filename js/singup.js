document.addEventListener("DOMContentLoaded", function() {
  const form = document.querySelector("form");
  
  form.addEventListener("submit", function(event) {
      const email = document.getElementById("email").value;
      const password = document.getElementById("password").value;
      
      if (!isValidEmail(email)) {
          alert("Please enter a valid email address.");
          event.preventDefault();
      } else if (password.trim() === "") {
          alert("Password cannot be blank.");
          event.preventDefault();
      }
  });
  
  function isValidEmail(email) {
      const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
      return re.test(email);
  }
});
