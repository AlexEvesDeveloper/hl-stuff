$(window).scroll(function() {
    var height = $(window).scrollTop();
    var brand = $('.brand-name');
    var steps = $('.mobile-steps');

    // Set the visibility of the current step in the process on mobiles so it
    // persists when scrolling down the page.
    if (height > 100) {
        if(brand.hasClass('initial')) {
            steps.animate({ width: ['60%', 'linear'] });
            brand.removeClass('initial');
        }
        steps.fadeIn(50);
    } else {
        steps.fadeOut(50);
        steps.animate({ width: ['40%', 'linear'] }, 50);
        brand.addClass('initial');
    }
});