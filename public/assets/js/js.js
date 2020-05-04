
$(document).ready(function(){
    $('.js-datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });
});

$("#flip").click(function(){
    $("#panel").slideToggle("slow");
});

$('.custom-file-input').on('change', function(event) {
    let inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);
});