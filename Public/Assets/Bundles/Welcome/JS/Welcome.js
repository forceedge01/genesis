/* Javascript for users Bundle */

jQuery(document).ready(function(){

    $('.paginate').gear('paginate');
    $('div').gear('hideAlert');
    $('body').gear('hideFormOnSubmit');
    $('ul#gearMenu').gear('menu', {'orientation':'vertical'});
    $('input').gear('defaultValues');
    $('body').gear('focusFirstElement');
});

