// Developed by A. Wahhab Qureshi.

(function($){
    
    var methods = {
        
        init: function( options ){
            
            alert(options.newww);
        },
        
        form: function( options ){
            
            $('body').on('submit', 'form', function(e) {

                $('input[type=submit]').parents('tr').fadeOut(200);

                $(options.loadingDiv).show();

                if ($(this).hasClass('confirm')) {

                    var answer = confirm($(this).attr('message'));

                    if (answer) {

                        return true
                    }
                    else {

                        $(options.loadingDiv).hide();
                        $('input[type=submit]').parents('tr').fadeIn(200);

                        return false
                    }
                }
            });
        },
        
        accordian: function (){
            
            alert('accordian');
        },
        
        sections: function(){
            
            alert('sections');
        },
        
        menu: function(){
            
            $(this).slideToggle('fast');
        },
        
        tip: function(){
            
            $(this).on('focus', function() {
                $(this).next('.tip').show();
            }).on('blur', function() {
                $(this).next('.tip').hide();
            });
        },
        
        paginate: function (){
            
            alert('paginate');
        },
        
        confirm: function ( options ){
            
            $(this).click(function() {

                var answer = confirm($(this).next().val());

                if (answer) {

                    $(options.loadingIconDiv).show();

                    $.post($(this).prev().val(), $(this).parent('form').serializeArray(), function(data) {

                        var chunk = data.split(':');
                        var Assignclass = null;

                        if (chunk[0] == 'error')
                            Assignclass = 'alert error';
                        else if (chunk[0] == 'success')
                            Assignclass = 'alert message';
                        else
                            alert('Unhandled Exception:' + chunk[0] + chunk[1]);

                        $(options.responseDiv).removeClass();

                        $(options.responseDiv).addClass(Assignclass).html(chunk[1]);

                        if (chunk[0] == 'success') {

                            if ($(this).hasClass('remove'))
                                $(this).parent().parent().parent().remove();
                        }

                        $(options.loadingIconDiv).hide();

                    });
                }

            });
        },
        
        defaultValues: function (){
            
            $('input[type=text]').on('focus', function() {

                if ($(this).prop("defaultValue") == $(this).val()) {

                    CurrentLabel = $(this).val();

                    $(this).addClass('active');
                    $(this).val('');

                }
            });
            
            $('input[type=text]').on('blur', function() {

                if ($(this).val() == '') {

                    $(this).val(CurrentLabel);
                    $(this).removeClass('active');

                }
            });

            $('table').attr('cellspacing', '0').attr('cellpadding', '0');
        }
    };
    
    $.fn.gear = function( method ){
        
        // Method calling logic
        if ( methods[method] ) {
            
          return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
          
        } else if ( typeof method === 'object' || ! method ) {
            
          return methods.init.apply( this, arguments );
          
        } else {
            
          $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
          
          return false;
        }
        
    };
})(jQuery);