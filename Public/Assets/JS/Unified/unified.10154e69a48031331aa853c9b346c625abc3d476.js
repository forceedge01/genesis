// Developed by A. Wahhab Qureshi. 

(function($){

    var methods = {

        init: function( options ){


        },

        hideFormOnSubmit: function( options ){

            $(this).on('submit', 'form', function(e) {

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

        sections: function( options ){

            var $currentSection = 1;
            var $index = 0;

            $('.Sections .section').each(function() {

                if($index >0)
                    $(this).hide(0);

                $index += 1;

            });

            $('.Sections .prev').attr('disabled', 'disabled');

            // Next Sections
            $('body').delegate('.Sections .next', 'click', function() {

                $('div #section' + $currentSection).hide(0);

                $currentSection += 1;

                $('div #section' + $currentSection).fadeIn(200);

                $(this).parents('.Sections').find('.SectionsButtons .prev').removeAttr('disabled');

                if ($currentSection >= $index) {

                    $(this).attr('disabled', 'disabled');
                }

                $(this).parents('.Sections').find('.SectionStats span#Section').html($currentSection);

            });

            // Previous Sections
            $('body').delegate('.Sections .prev', 'click', function() {

                $('div #section' + $currentSection).hide(0);

                $currentSection -= 1;

                $('div #section' + $currentSection).fadeIn(200);

                $(this).parents('.Sections').find('.SectionsButtons .next').removeAttr('disabled');

                if ($currentSection === 1) {

                    $(this).attr('disabled', 'disabled');
                }

                $(this).parents('.Sections').find('.SectionStats span#Section').html($currentSection);

            });
        },

        blink: function(options){

            for(var $i=50; $i<options.time; $i = $i+100){

                setTimeout(function(){
                    resetCss($(this))
                    }, $i);
                setTimeout(function(){
                    changeCss($(this))
                    }, $i+50);
            }
        },

        menu: function(options){

            // Set gear class
            if(options.orientation == 'horizontal')
                $(this).addClass('gearMenuHorizontal');
            else
                $(this).addClass('gearMenu');

            // Set css for container
            if(options.background)
                $(this).css('background', 'url(' + options.background + ')');

            if(options.backgroundColor)
                $(this).css('background-color', options.backgroundColor);

            if(options.hoverSound)
                $('body').append('<audio id="gearHoverSound"><source src="'+options.hoverSound+'" ></source></audio>');

            if(options.clickSound)
                $('body').append('<audio id="gearClickSound"><source src="'+options.clickSound+'" ></source></audio>');

            $(this).children('ol').prepend('<li id="gearMenuBack">Back</li>');

            $(this).children('li').each(function(){

                $(this).click(function(){

                    $this = $(this);

                    $('audio#gearClickSound')[0].play();

                    setTimeout(function(){

                        $($this).hide();
                        $($this).siblings('li').hide();
                        $($this).next('ol').show(400);

                    }, 400);
                });
            });

            $('ul#gearMenu').delegate('li','mouseenter',function(){

                $('audio#gearHoverSound')[0].play();
            });

            $('ul#gearMenu').delegate('li','click',function(event){

                blink(this, 400);

                $('audio#gearClickSound')[0].play();

                if($(this).next('div'))
                    setTimeout(function(){

                       $(event.target).next('div').slideToggle(400);

                    }, 400);

                    $init = 400;



                        $(event.target).next('div').children('div').each(function(){

                            setTimeout(function(){

                                $(this).show(300);

    //                       $this = this;
    //
    //                       setTimeout(function(){
    //
                              $($this).show(300);
                              $('audio#gearClickSound')[0].play();
    //

//                        $init += 400;

                            }, 400);

                        }).show(400);


            });

            $('ul#gearMenu').delegate('#gearMenuBack','click',function(){

                $(this).parent('ol').hide();
                $(this).parent('ol').siblings('li').show(400);
            });

            // Set minimum height for not spazzing effects
            var height = 0;

            if($(this).height() == 0)
                height = 50;
            else
                height = $(this).height();
            $(this).css('min-height', height);

        },

        tip: function(){

            $(this).on('focus', function() {
                $(this).next('.tip').show();
            }).on('blur', function() {
                $(this).next('.tip').hide();
            });
        },

        paginate: function (options){

            if($(this).children('tbody').length < 1)
                return false;

            // change value to set pagination limit, Global to all
            // rowsPerPage
            var $paginate = 5;

            if(typeof(options) != 'undefined')
                $paginate = options.rowsPerPage;

            // end of custom value

            var $currentpage = 1;
            var $index = 1;
            var $visibleRows = [];

            //            $(this).(function() {

            $(this).addClass('paginate');

            var $cols = ($('tbody tr td').length);

            $('tbody tr').each(function(item) {

                $(this).attr('rowId', $index);

                if ($index > $paginate) {

                    $(this).hide();
                } else
                    $(this).attr('visible', '1');

                $index++;

            });

            var $rows = ($('tbody tr').length);

            if ($rows == 0)
                $rows = 1;

            var $colspan = $cols / $rows;

            $(this).append('<tfoot>' +
                '<tr class="pagination">' +
                '<td colspan="' + $colspan + '">' +
                '<span>Showing ' + $paginate + ' Records per page, Total: ' + ($index - 1) + ' Current Page:  <span id="currentPage">' + $currentpage + '</span></span>' +
                '<span id="navButtons">' +
                ' <input type="text" id="searchTable" value="Search...">' +
                ' <input type="button" value="Prev" class="prevResults">' +
                ' <input type="button" value="Next" class="nextResults">' +
                '</span>' +
                '</td>' +
                '</tr>' +
                '</tfoot>');

            $('tfoot .prevResults').attr('disabled', 'disabled');

            if ($paginate >= $index - 1)
                $('tfoot .nextResults').attr('disabled', 'disabled');

            // Search in table function
            $(this).delegate('#searchTable', 'focus', function(e) {

                if ($(this).val() == $(this).prop('defaultValue'))
                    $(this).val('');
            });

            $(this).delegate('#searchTable', 'blur', function(e) {

                if ($(this).val() == ''){
                    $(this).val($(this).prop('defaultValue'));
                }
            });

            $searchTag = '';
            $(this).delegate('#searchTable', 'keyup', function() {

                $searchTag = $(this).val();

                $('.paginate tbody tr').each(function() {

                    $(this).removeClass('searchResult').css('display', 'none');

                });

                if ($searchTag !== '') {

                    $('.paginate tbody tr td').each(function() {

                        var $string = $(this).text();

                        var $regex = new RegExp("^(" + $searchTag + ")(.|( ))*$", 'i');

                        if ($regex.test($string)) {

                            if(!$(this).parents('tr').hasClass('searchResult'))
                                $(this).parents('tr').addClass('searchResult').css('display', 'table-row');

                        }

                    });
                }
                else {

                    $('.paginate tbody tr.searchResult').removeClass('searchResult');

                    $('.paginate tbody tr[visible="1"]').show(0);
                }

            });

            // Next Button Function
            $('tfoot').delegate('.nextResults', 'click', function() {

                $currentpage += 1;

                $startIndex = ($currentpage * $paginate) - $paginate + 1;

                $endIndex = $startIndex + $paginate;

                $hideStartIndex = $startIndex - $paginate;

                $hideEndIndex = $endIndex - $paginate;

                for ($hideStartIndex; $hideStartIndex < $hideEndIndex; $hideStartIndex++) {
                    $(this).parents('table').find('tr[rowid="' + ($hideStartIndex) + '"]').hide(0);
                    $(this).parents('table').find('tr[rowid="' + ($hideStartIndex) + '"]').removeAttr('visible');
                }

                for ($startIndex; $startIndex < $endIndex; $startIndex++) {

                    $(this).parents('table').find('tr[rowid="' + $startIndex + '"]').fadeIn(300);
                    $(this).parents('table').find('tr[rowid="' + ($startIndex) + '"]').attr('visible', '1');
                }

                $('#currentPage').html($currentpage);

                $('.prevResults').removeAttr('disabled');

                if ($endIndex >= $index)
                    $(this).attr('disabled', 'disabled');

            });

            // Prev Button function

            $('tfoot').delegate('.prevResults', 'click', function() {

                $currentpage -= 1;

                $startIndex = ($currentpage * $paginate) - $paginate + 1;

                $endIndex = $startIndex + $paginate;

                $hideStartIndex = $startIndex + $paginate;

                $hideEndIndex = $endIndex + $paginate;

                for ($hideStartIndex; $hideStartIndex < $hideEndIndex; $hideStartIndex++) {

                    $(this).parents('table').find('tr[rowid="' + ($hideStartIndex) + '"]').hide(0);
                    $(this).parents('table').find('tr[rowid="' + ($hideStartIndex) + '"]').removeAttr('visible');
                }

                for ($startIndex; $startIndex < $endIndex; $startIndex++) {

                    $(this).parents('table').find('tr[rowid="' + $startIndex + '"]').fadeIn(300);
                    $(this).parents('table').find('tr[rowid="' + ($startIndex) + '"]').attr('visible', '1');
                }

                $('#currentPage').html($currentpage);

                $('.nextResults').removeAttr('disabled');

                if ($startIndex == 1 + $paginate)
                    $(this).attr('disabled', 'disabled');

            });
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
        },
        
        hideAlert: function()
        {
            $(this).delegate('.alert', 'click', function() {

                $(this).hide(200);
        
            });
        }
    };

    $.fn.gear = function( method ){

        // Method calling logic
        if ( methods[method] ) {

            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));

        } else if ( typeof method === 'object' || ! method ) {

            return methods.init.apply( this, arguments );

        } else {

            $.error( 'Method ' +  method + ' does not exist on jQuery' );

            return false;
        }

    };
})(jQuery);

function changeCss($this)
{
    $($this).css('visibility','visible');
}

function resetCss($this)
{
    $($this).css('visibility','hidden');
}

function blink($this, $time){

    for(var $i=50; $i<$time; $i = $i+100){

        setTimeout(function(){
            resetCss($this)
            }, $i);
        setTimeout(function(){
            changeCss($this)
            }, $i+50);
    }
}//
//$(document).ready(function() {
//
//    $('.confirmAction').gear('confirm',{
//
//        loadingDiv: 'div#loading',
//        messageDiv: 'div#JSEvent'
//    });
//
//    $('#gearMenu').gear('menu', {
//
//        hoverSound: 'http://localhost/genesis/public/Assets/Media/gear/Sounds/hoverSound.wav',
//        clickSound: 'http://localhost/genesis/public/Assets/Media/gear/Sounds/clickSound.wav',
//        backgroundColor: 'black',
//        orientation: 'vertical'
//    });
//
//    //-------------------------------------------------------------------------------------------------//Confirm action for all forms-------------------------------------------------------------------------------------------------//
////    $('.confirmAction').click(function() {
////
////        var answer = confirm($(this).next().val());
////
////        var $this = $(this);
////
////        if (answer) {
////
////            $('div#loading').show();
////
////            $.post($(this).prev().val(), $('form').serializeArray(), function(data) {
////
////                var chunk = data.split(':');
////                var Assignclass = null;
////
////                if (chunk[0] == 'error')
////                    Assignclass = 'alert error';
////                else if (chunk[0] == 'success')
////                    Assignclass = 'alert message';
////                else
////                    alert('Unhandled Exception:' + chunk[0] + chunk[1]);
////
////                $('#JSEvent').removeClass();
////
////                $('#JSEvent').addClass(Assignclass).html(chunk[1]);
////
////                if (chunk[0] == 'success') {
////
////                    if ($($this).hasClass('remove'))
////                        $($this).parent().parent().parent().remove();
////                }
////
////                $('div#loading').hide();
////
////            });
////        }
////
////    });
//
//    //-------------------------------------------------------------------------------------------------//build menu item-------------------------------------------------------------------------------------------------//
//    $('#Menu,#menuList').on('click', function() {
//
//        $('#menuList').slideToggle('fast');
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//enable tips for inputs-------------------------------------------------------------------------------------------------//
//    $('input[type=text]').gear('tip');
//
//
//    //one create domain button is clicked, the button is disabled untill the process is finished.-------------------------------------------------------------------------------------------------//
////    $.gear('form');
//
//    $('div').delegate('.alert', 'click', function() {
//
//        $(this).hide(200);
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//on text focus, set color and remove default message.-------------------------------------------------------------------------------------------------//
//    var CurrentLabel = null;
//
//    $('input[type=text]').on('focus', function() {
//
//        if ($(this).prop("defaultValue") == $(this).val()) {
//
//            CurrentLabel = $(this).val();
//
//            $(this).addClass('active');
//            $(this).val('');
//
//        }
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//on text blur, set color and default message if applicable.//-------------------------------------------------------------------------------------------------//
//    $('input[type=text]').on('blur', function() {
//
//        if ($(this).val() == '') {
//
//            $(this).val(CurrentLabel);
//            $(this).removeClass('active');
//
//        }
//
//    });
//
//    $('table').attr('cellspacing', '0').attr('cellpadding', '0');
//
//    //-------------------------------------------------------------------------------------------------//show hide actions menu in tables.-------------------------------------------------------------------------------------------------//
//    var $previous = null;
//
//    $('.settings').each(function() {
//
//        $(this).click(function() {
//
//            if ($previous == null) {
//
//                $previous = this;
//
//            }
//
//            if ($previous != this) {
//
//                $($previous).next().hide();
//            }
//
//            $previous = this;
//            $(this).next().slideToggle(200);
//
//        });
//
//    });
//
//    $('.ShowMessage').click(function() {
//
//        alert($(this).next().val());
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//enable first element of the form-------------------------------------------------------------------------------------------------//
//    $('form:not(.filter) :input:visible:enabled:first').focus();
//
//
//    //------------------------------------------------------------------------------------------------pagination if tables-------------------------------------------------------------------------------------------------//
//
//    //---------------- change value to set pagination limit, Global to all --------------------//
//
//    var $paginate = 2;
//
//    //----------------------------end of custom value---------------------------//
//
//    var $currentpage = 1;
//    var $index = 1;
//    var $visibleRows = [];
//
//    $('table.paginate').each(function() {
//
//        if($(this).children('tbody').length < 1)
//            alert('Pagination cannot be rendered without a tbody element in table.');
//
//        var $cols = ($('tbody tr td').length);
//
//        $('tbody tr').each(function(item) {
//
//            $(this).attr('rowId', $index);
//
//            if ($index > $paginate) {
//
//                $(this).hide();
//            } else
//                $(this).attr('visible', '1');
//
//            $index++;
//
//        });
//
//        var $rows = ($('tbody tr').length);
//
//        if ($rows == 0)
//            $rows = 1;
//
//        var $colspan = $cols / $rows;
//
//        $(this).append('<tfoot>' +
//                '<tr class="pagination">' +
//                '<td colspan="' + $colspan + '">' +
//                '<span>Showing ' + $paginate + ' Records per page, Total: ' + ($index - 1) + ' Current Page:  <span id="currentPage">' + $currentpage + '</span></span>' +
//                '<span id="navButtons">' +
//                ' <input type="text" id="searchTable" value="Search...">' +
//                ' <input type="button" value="Prev" class="prevResults">' +
//                ' <input type="button" value="Next" class="nextResults">' +
//                '</span>' +
//                '</td>' +
//                '</tr>' +
//                '</tfoot>');
//
//        $('tfoot .prevResults').attr('disabled', 'disabled');
//
//        if ($paginate >= $index - 1)
//            $('tfoot .nextResults').attr('disabled', 'disabled');
//    });
//
//    //-------------------------------------------------------------------------------------------------//Search in table function//----------------------------------------------------------------------------------------------//
//    $('table').delegate('#searchTable', 'focus', function(e) {
//
//        if ($(this).val() == $(this).prop('defaultValue'))
//            $(this).val('');
//    });
//
//    $('table').delegate('#searchTable', 'blur', function(e) {
//
//        if ($(this).val() == ''){
//            $(this).val($(this).prop('defaultValue'));
//        }
//    });
//
//    $searchTag = '';
//    $('table').delegate('#searchTable', 'keyup', function() {
//
//        $searchTag = $(this).val();
//
//        $('.paginate tbody tr').each(function() {
//
//            $(this).removeClass('searchResult').css('display', 'none');
//
//        });
//
//        if ($searchTag !== '') {
//
//            $('.paginate tbody tr td').each(function() {
//
//                var $string = $(this).text();
//
//                var $regex = new RegExp("^(" + $searchTag + ")(.|( ))*$", 'i');
//
//                if ($regex.test($string)) {
//
//                    if(!$(this).parents('tr').hasClass('searchResult'))
//                        $(this).parents('tr').addClass('searchResult').css('display', 'table-row');
//
//                }
//
//            });
//        }
//        else {
//
//            $('.paginate tbody tr.searchResult').removeClass('searchResult');
//
//            $('.paginate tbody tr[visible="1"]').show(0);
//        }
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//Next Button Function//-------------------------------------------------------------------------------------------------//
//    $('tfoot').delegate('.nextResults', 'click', function() {
//
//        $currentpage += 1;
//
//        $startIndex = ($currentpage * $paginate) - $paginate + 1;
//
//        $endIndex = $startIndex + $paginate;
//
//        $hideStartIndex = $startIndex - $paginate;
//
//        $hideEndIndex = $endIndex - $paginate;
//
//        for ($hideStartIndex; $hideStartIndex < $hideEndIndex; $hideStartIndex++) {
//            $(this).parents('table').find('tr[rowid="' + ($hideStartIndex) + '"]').hide(0);
//            $(this).parents('table').find('tr[rowid="' + ($hideStartIndex) + '"]').removeAttr('visible');
//        }
//
//        for ($startIndex; $startIndex < $endIndex; $startIndex++) {
//
//            $(this).parents('table').find('tr[rowid="' + $startIndex + '"]').fadeIn(300);
//            $(this).parents('table').find('tr[rowid="' + ($startIndex) + '"]').attr('visible', '1');
//        }
//
//        $('#currentPage').html($currentpage);
//
//        $('.prevResults').removeAttr('disabled');
//
//        if ($endIndex >= $index)
//            $(this).attr('disabled', 'disabled');
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//Prev Button function//-------------------------------------------------------------------------------------------------//
//
//    $('tfoot').delegate('.prevResults', 'click', function() {
//
//        $currentpage -= 1;
//
//        $startIndex = ($currentpage * $paginate) - $paginate + 1;
//
//        $endIndex = $startIndex + $paginate;
//
//        $hideStartIndex = $startIndex + $paginate;
//
//        $hideEndIndex = $endIndex + $paginate;
//
//        for ($hideStartIndex; $hideStartIndex < $hideEndIndex; $hideStartIndex++) {
//
//            $(this).parents('table').find('tr[rowid="' + ($hideStartIndex) + '"]').hide(0);
//            $(this).parents('table').find('tr[rowid="' + ($hideStartIndex) + '"]').removeAttr('visible');
//        }
//
//        for ($startIndex; $startIndex < $endIndex; $startIndex++) {
//
//            $(this).parents('table').find('tr[rowid="' + $startIndex + '"]').fadeIn(300);
//            $(this).parents('table').find('tr[rowid="' + ($startIndex) + '"]').attr('visible', '1');
//        }
//
//        $('#currentPage').html($currentpage);
//
//        $('.nextResults').removeAttr('disabled');
//
//        if ($startIndex == 1 + $paginate)
//            $(this).attr('disabled', 'disabled');
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//End of pagination //-------------------------------------------------------------------------------------------------//
//
//    //-------------------------------------------------------------------------------------------------//Sections//-------------------------------------------------------------------------------------------------//
//
//    var $currentSection = 1;
//    var $index = 0;
//
//    $('.Sections .section').each(function() {
//
//        if($index >0)
//            $(this).hide(0);
//
//        $index += 1;
//
//    });
//
//    $('.Sections .prev').attr('disabled', 'disabled');
//
//    //-------------------------------------------------------------------------------------------------//Next Sections//-------------------------------------------------------------------------------------------------//
//    $('body').delegate('.Sections .next', 'click', function() {
//
//        $('div #section' + $currentSection).hide(0);
//
//        $currentSection += 1;
//
//        $('div #section' + $currentSection).fadeIn(200);
//
//        $(this).parents('.Sections').find('.SectionsButtons .prev').removeAttr('disabled');
//
//        if ($currentSection >= $index) {
//
//            $(this).attr('disabled', 'disabled');
//        }
//
//        $(this).parents('.Sections').find('.SectionStats span#Section').html($currentSection);
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//Previous Sections//-------------------------------------------------------------------------------------------------//
//    $('body').delegate('.Sections .prev', 'click', function() {
//
//        $('div #section' + $currentSection).hide(0);
//
//        $currentSection -= 1;
//
//        $('div #section' + $currentSection).fadeIn(200);
//
//        $(this).parents('.Sections').find('.SectionsButtons .next').removeAttr('disabled');
//
//        if ($currentSection === 1) {
//
//            $(this).attr('disabled', 'disabled');
//        }
//
//        $(this).parents('.Sections').find('.SectionStats span#Section').html($currentSection);
//
//    });
//
//    //-------------------------------------------------------------------------------------------------//End of Sections//-------------------------------------------------------------------------------------------------//
//
//});