
$(document).ready(function(){
    $('.js-datepicker').datepicker({
        format: 'dd/mm/yyyy'
    });
});

$("#flip").click(function(){
    $("#panel").slideToggle("slow");
});

