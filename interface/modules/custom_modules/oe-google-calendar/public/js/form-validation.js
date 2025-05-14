/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */



document.getElementById('myForm').addEventListener('submit', function(event) {
    var clientId = document.getElementById('client_id').value.trim();
    var clientSecret = document.getElementById('client_secret').value.trim();
    var redirectUri = document.getElementById('redirect_uri').value.trim();

    if (!clientId || !clientSecret || !redirectUri) {
        alert('All fields must be filled out');
        event.preventDefault(); // Prevent form submission
    }
});
