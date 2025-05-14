/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

$(document).ready(function() {
    // Function to handle the button click
    function businessfunction() {
        // Get the value from the input element
        const apikey = $('input[name="api_key"]').val();
        const registration_url = $('input[name="registration_url"]').val();
        const site_id = $('input[name="site_id"]').val();
        const uuid = $('input[name="uuid"]').val();
        const clinic_name = $('input[name="clinic_name"]').val();
        const address = $('input[name="address"]').val();
        const city = $('input[name="city"]').val();
        const state = $('input[name="state"]').val();
        const zip = $('input[name="zip"]').val();
        const phone = $('input[name="phone"]').val();
        const foreign_id = $('input[name="foreign_id"]').val();
        registerBusiness(apikey, registration_url, site_id, uuid, clinic_name, address, city, state, zip, phone, foreign_id);
    }

    $('#registerBusiness').click(businessfunction);
});

function registerBusiness(apikey, registration_url, site_id, uuid, clinic_name, address, city, state, zip, phone, foreign_id) {
    //Capture the registration attempt
    // Create request headers
    const headers = new Headers({
        "Content-Type": "application/json",
    });
    // Create the request body
    const body = JSON.stringify({
        apikey: apikey,
        registration_url: registration_url,
        site_id: site_id,
        uuid: uuid,
        clinic_name: clinic_name,
        address: address,
        city: city,
        state: state,
        zip: zip,
        phone: phone,
        foreign_id: foreign_id,
    });
    //console.log(body);
    // Create the request object
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: body
    };
    // Execute the fetch request
    fetch('public/registerBusiness.php', requestOptions)
        .then(response => response.json())
        .then(data => {
            // Log the response (for debug purposes, consider removing in production)
            console.log(data);
            // Check if the registration was successful
            if (data.message) {
                // Display an error message
                alert(data.message);
                document.getElementById("step_two").hidden = true;
            }
        })
        .catch(error => {
            // Log and display any errors
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
}
