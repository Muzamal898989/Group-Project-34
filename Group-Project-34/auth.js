document.addEventListener("DOMContentLoaded", () => 
{
    const registerForm = document.getElementById("registerForm");
    if (registerForm) 
    {
        registerForm.addEventListener("submit", (event) =>
        {
            event.preventDefault();

            const firstName = document.getElementById("firstName").ariaValueMax.trim();
            const lastName = document.getElementById("lastName").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const confirmPassword = document.getElementById("confirmPassword").value.trim();
            const birthday = document.getElementById("birthday").value;

            if (password !== confirmPassword)
            {
                alert("Passwords do not match!");
                return;
            }
        const existingUsers = JSON.parse(localStorage.getItem("users")) || [];

        const userExists = existingUsers.some((user) => user.email === email);
        if (userExists) 
            {
                alert("An account with this email already exists!");
                return;
            }

        const newUser = {
            firstName,
            lastName,
            email,
            password,
            birthday,
        };

        existingUsers.push(newUser);
        localStorage.setItem("users", JSON.stringify(existingUsers));
        alert("Account created successfully! You can now log in.");
        registerForm.reset();
        window.location.href = "login.html";

        }
        );
    }

    const loginForm = document.querySelector("#login form");
        if (loginForm) 
            {
                loginForm.addEventListener("submit", (event) => {
                event.preventDefault();

            const email = loginForm.querySelector('input[type="email"]').value.trim();
            const password = loginForm.querySelector('input[type="password"]').value.trim();

            const users = JSON.parse(localStorage.getItem("users")) || [];

            const validUser = users.find(
             (user) => user.email === email && user.password === password
            );

      if (validUser) { 
        alert(`Welcome back, ${validUser.firstName}!`);
        localStorage.setItem("currentUser", JSON.stringify(validUser));
        window.location.href = "homepage.html"; 
      } else {
        alert("Invalid email or password. Please try again.");
      }
    });
  }
});

















