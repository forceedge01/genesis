/* Javascript for users Bundle */

jQuery(document).ready(function(){

    $('html').gear('BindKeyEvents', {
        '27': {'url': '/logout/', 'message': 'Are you sure you want to logout?'}, // Esc Button
        '36': {'url': '/users/List/', 'message': 'Do you want to go to the home page?'}, // Homt Button
    });

    $('.paginate').gear('paginate');
    $('div').gear('hideAlert');
    $('body').gear('hideFormOnSubmit', {'loading': '#loading'});
    $('ul#gearMenu').gear('menu', {'orientation':'vertical'});
    $('input').gear('defaultValues');
    $('body').gear('focusFirst');
});
