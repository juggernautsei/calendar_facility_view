$(document).ready(function (){
    let errorMessage = $("#error-message").text();
    if(errorMessage.length > 0){
        setTimeout(() => {
            $("#error-message").text('');
            window.location.href = "index.php";
        }, 6500);
    }
});
