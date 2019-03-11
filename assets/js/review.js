 // loads the jquery package from node_modules
var $ = require('jquery');

 // import the function from greet.js (the .js extension is optional)
var greet = require('./greet');

var ratingValue;
var saveReview = $("#saveReview");
 $(document).ready(function(){

     /* 1. Visualizing things on Hover - See next part for action on click */
     $('#stars li').on('mouseover', function(){
         var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

         // Now highlight all the stars that's not after the current hovered star
         $(this).parent().children('li.star').each(function(e){
             if (e < onStar) {
                 $(this).addClass('hover');
             }
             else {
                 $(this).removeClass('hover');
             }
         });

     }).on('mouseout', function(){
         $(this).parent().children('li.star').each(function(e){
             $(this).removeClass('hover');
         });
     });


     /* 2. Action to perform on click */
     $('#stars li').on('click', function(){
         var onStar = parseInt($(this).data('value'), 10); // The star currently selected
         var stars = $(this).parent().children('li.star');

         for (i = 0; i < stars.length; i++) {
             $(stars[i]).removeClass('selected');
         }

         for (i = 0; i < onStar; i++) {
             $(stars[i]).addClass('selected');
         }

         // JUST RESPONSE (Not needed)
         ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
         var msg = "";
         if (ratingValue > 1) {
             msg = "Thanks! You rated this " + ratingValue + " stars.";
         }
         else {
             msg = "We will improve ourselves. You rated this " + ratingValue + " stars.";
         }
         responseMessage(msg);

     });
 });


 function responseMessage(msg) {
     $('.success-box').fadeIn(200);
     $('.success-box div.text-message').html("<span>" + msg + "</span>");
 }

saveReview.on("click", function () {
alert("ohohoh");
    /*if ($client.val() != "") {
        $.ajax({
            "type": "GET",
            "url": "http://api.localhost/review/new",
            "data": "ratingValue="+ratingValue,
            "success": function (res) {
                alert(res);
            } // success
        }); // ajax}
    }*/
});

 //Read Review Element //
 var cke_1_contents = $("#cke_1_contents");
 cke_1_contents.on("change", function(){
     alert(cke_1_contents.val());
 });

 var review_rating = $("#review_rating");
 review_rating.on("change", function(){
     alert(review_rating.val());
 });
 // END READ //
