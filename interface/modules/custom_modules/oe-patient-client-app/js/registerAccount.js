/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */




    // Ensure your script runs after the DOM is fully loaded
$(document).ready(function() {
    // Function to handle the button click
    function myFunction() {
        // Get the value from the input element
        const fullName = $('input[name="fullname"]').val();
        const email = $('input[name="email"]').val();
        const password = $('input[name="password"]').val();
        const confirmPassword = $('input[name="confirm_password"]').val();

        // Validation patterns
        const namePattern = /^[A-Za-z]+(?: [A-Za-z]+)+$/;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/;

        // Validate name
        if (!fullName || !namePattern.test(fullName)) {
            alert('Please enter both first and last name.');
            return;
        }
        // Validate email
        if (!email || !emailPattern.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        if (!password || !passwordPattern.test(password)) {
            alert('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.');
            return;
        }

        // Confirm password
        if (password !== confirmPassword) {
            alert('Password and confirm password do not match.');
            return;
        }

        // Call the function to process the registration
        registerAccount(fullName, email, password, confirmPassword);
    }

    // Bind the function to the button click event
    $('#create_account').click(myFunction);
});

function registerAccount(fullName, email, password, confirmPassword) {
    // Log the registration attempt (for debug purposes, consider removing in production)
    // Create request headers
    const headers = new Headers({
        "Content-Type": "application/json",
        "Accept": "application/json"
    });
    console.log(fullName, email, password, confirmPassword);
    // Prepare the request body
    const body = JSON.stringify({
        name: fullName,
        email: email,
        password: password,
        password_confirmation: confirmPassword
    });

    // Define the request options
    const requestOptions = {
        method: "POST",
        mode: "cors",
        cache: "no-cache",
        headers: headers,
        redirect: "follow",
        referrerPolicy: "no-referrer",
        body: body
    };

    // Execute the fetch request locally
    fetch("public/fetchApiKey.php", requestOptions)
        .then(response => response.text()) // Parsing JSON here instead of just text
        .then(data => {
            if (data) {
                // Hide registration form on success
                document.getElementById("step_one").hidden = true;

                // Setup API Key display
                displayApiKey();
            } else {
                //TODO: Add more detailed error handling to show the real error message
                console.error("Registration failed:", data.email);
                alert("Registration failed. Please check the console for more information.");
            }
        })
        .catch(error => {
            console.error("Network error:", error);
            alert("Network error occurred. Please check the console.");
        });
}

function displayApiKey() {
    // get rid of these form elements
    //they are no longer needed. Data will be stored in another process.
    const keyDisplay = document.getElementById("api-key");
    keyDisplay.innerHTML = ''; // Clear previous entries

    // Add inputs to display
    keyDisplay.appendChild(document.createTextNode("Your API Key is saved."));
    document.getElementById("step_two").hidden = false;

}





