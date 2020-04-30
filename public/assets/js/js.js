
$(document).ready(function(){
    $('.js-datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });
});

$("#flip").click(function(){
    $("#panel").slideToggle("slow");
});

