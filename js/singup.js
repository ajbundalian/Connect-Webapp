document.getElementById("signupForm").addEventListener("submit", function(event) {
    const email = document.querySelector("input[name='email']").value;
    const password = document.querySelector("input[name='password']").value;

    if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(email)) {
        alert("Please enter a valid email address!");
        event.preventDefault();
    }

    if (password.length < 8) {
        alert("Password should be at least 8 characters long!");
        event.preventDefault();
    }
});
