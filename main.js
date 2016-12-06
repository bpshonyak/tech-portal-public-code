$(function() {

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "3000",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "5000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $("#reset-form").on('submit', function(e) {
        e.preventDefault();

        //Server call to submit password reset
        $.ajax({
            url: '/api/email.php',
            type: 'POST',
            data: {
                username: $('#username').val(),
                studentID: $('#studentID').val(),
                email: $('#email').val()
            },
            success: function(result) {
                console.log(result);
                if(result.error ===  401) {
                  toastr.error(result.msg);
              } else {
                  // Display an info toast with no title
                  toastr.success('Success! You will get an email with your new password shortly!');

                  $('#username').val("");
                  $('#studentID').val("");
                  $('#email').val("");
              } //end else statement
          } //end success function(result)
        });
    });

});
