$( function() {
    $( ".datepicker" ).datepicker();
} );

$(document).ready(function(){
    $("#flip").click(function(){
        $("#panel").slideToggle("slow");
    });
});