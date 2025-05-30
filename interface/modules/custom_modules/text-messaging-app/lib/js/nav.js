/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

    /* Code for changing active
    link on clicking */
    var btns =
    $("#navigation .navbar-nav .nav-link");

    for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click",
        function () {
            var current = document
                .getElementsByClassName("active");

            current[0].className = current[0]
                .className.replace(" active", "");

            this.className += " active";
        });
}

    /* Code for changing active
    link on Scrolling */
    $(window).scroll(function () {
    var distance = $(window).scrollTop();
    $('.page-section').each(function (i) {

    if ($(this).position().top
    <= distance + 250) {

    $('.navbar-nav a.active')
    .removeClass('active');

    $('.navbar-nav a').eq(i)
    .addClass('active');
    }
    });
    }).scroll();
