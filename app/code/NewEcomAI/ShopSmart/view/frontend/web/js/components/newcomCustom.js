define([
    'jquery'
], function ($) {
    "use strict";

    return function () {
        $(document).ready(function () {
           //  JS for clickable text
            $('.newcomClickableText').click(function() {
               var newEcomClickedText = $(this).text().trim(); 
               $('#NewEcomAi-question').val(newEcomClickedText);
            });
            //  JS condition for discover template postion
            if ($('.newSmartShopDiscover').hasClass('newcomLeftSide')) {
                $('#newcomLeftBtn').on('click', function() {
                  $('#newcom-popup').css('display', 'block');
                });
            } else if ($('.newSmartShopDiscover').hasClass('newcomRightSide')) {
              $('#newcomRightBtn').on('click', function() {
                $('#newcom-popup').css('display', 'block');
              });
            } else if ($('.newSmartShopDiscover').hasClass('newcomProductGrid')) {
              $('#newcom-popup').css('display', 'block');
            } else {
              $('#newcom-popup').css('display', 'block');
            }

            $('#newComclose').click(function() {
              $('#newcom-popup').css('display', 'none');
            });
        });
    }
});