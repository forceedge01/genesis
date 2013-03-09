
$(document).ready(function() {

    //Confirm action for all forms
    $('.confirmAction').click(function() {

        var answer = confirm($(this).next().val());

        var $this = $(this);

        if (answer) {

            $('div#loading').show();

            $.post($(this).prev().val(), $('form').serializeArray(), function(data) {

                var chunk = data.split(':');
                var Assignclass = null;

                if (chunk[0] == 'error')
                    Assignclass = 'alert error';
                else if (chunk[0] == 'success')
                    Assignclass = 'alert message';
                else
                    alert('Unhandled Exception:' + chunk[0] + chunk[1]);

                $('#JSEvent').removeClass();

                $('#JSEvent').addClass(Assignclass).html(chunk[1]);

                if (chunk[0] == 'success') {

                    if ($($this).hasClass('remove'))
                        $($this).parent().parent().parent().remove();
                }

                $('div#loading').hide();

            });
        }

    });

    //build menu item
    $('#Menu,#menuList').on('click', function() {

        $('#menuList').slideToggle('fast');

    });

    //enable tips for inputs
    $('input[type=text]').on('focus', function() {
        $(this).next('.tip').show();
    }).on('blur', function() {
        $(this).next('.tip').hide();
    });


    //one create domain button is clicked, the button is disabled untill the process is finished.
    $('body').on('submit', 'form', function(e) {

        $('input[type=submit]').parent().parent().fadeOut(200);

        $('div#loading').show();

        if($(this).hasClass('confirm')){

            var answer = confirm($(this).attr('message'));

            if (answer) {

                $error =  true;

            }
            else{

                $('div#loading').hide();
                $('input[type=submit]').parent().parent().fadeIn(200);

                $error =  false;

            }

        }

        return true;

    });

    $('div').delegate('.alert', 'click', function() {

        $(this).hide(200);

    });

    //on text focus, set color and remove default message.
    var CurrentLabel = null;

    $('input[type=text]').on('focus', function() {

        if ($(this).prop("defaultValue") == $(this).val()) {

            CurrentLabel = $(this).val();

            $(this).addClass('active');
            $(this).val('');

        }

    });

    //on text blur, set color and default message if applicable.
    $('input[type=text]').on('blur', function() {

        if ($(this).val() == '') {

            $(this).val(CurrentLabel);
            $(this).removeClass('active');

        }

    });

    $('table').attr('cellspacing', '0').attr('cellpadding', '0');

    //show hide actions menu in tables.
    var $previous = null;

    $('.settings').each(function() {

        $(this).click(function(){

            if($previous == null){

                $previous = this;

            }

            if($previous != this){

                $($previous).next().hide();
            }

            $previous = this;
            $(this).next().slideToggle(200);

        });

    });

    $('.ShowMessage').click(function() {

        alert($(this).next().val());

    });

    //enable first element of the form
    $('form:not(.filter) :input:visible:enabled:first').focus();

});