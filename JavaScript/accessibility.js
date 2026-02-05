accessibilityButton = $('#accessibility-btn');
accessibilityMenu = $('.accessibility-menu');
settings = {
    fontsize: 1,
    lineheight: 1.5,
    wordSpace: 0,
    letterSpace: 0,
    contrast: 'normal'
}
accessibilityButton.on('click', function() {
    accessibilityMenu.toggleClass('active');
});

// Toggle Dark Mode
$('#toggle-dark-mode').on('click', function() {
    $('body').toggleClass('dark-mode');
    $(this).attr('aria-pressed', $('body').hasClass('dark-mode'));
});

// Toggle Clear Mode
$('#toggle-clear-mode').on('click', function() {
    $('body').toggleClass('clear-mode');
    $(this).attr('aria-pressed', $('body').hasClass('clear-mode'));
});

// Toggle High Saturation
$('#toggle-saturation-high').on('click', function() {
    $('body').toggleClass('saturation-high');
    $(this).attr('aria-pressed', $('body').hasClass('saturation-high'));
    settings.saturation = $('body').hasClass('saturation-high') ? 'high' : 'normal';
    aplicarCambios();
});

// Toggle Low Saturation
$('#toggle-saturation-low').on('click', function() {
    $('body').toggleClass('saturation-low');
    $(this).attr('aria-pressed', $('body').hasClass('saturation-low'));
    settings.saturation = $('body').hasClass('saturation-low') ? 'low' : 'normal';
    aplicarCambios();
});

// Toggle Grayscale
$('#toggle-grayscale').on('click', function() {
    $('body').toggleClass('grayscale');
    $(this).attr('aria-pressed', $('body').hasClass('grayscale'));
});

$('#toggle-text-size').on('click', function() {
    // increase font size setting and apply changes
    settings.fontsize = Math.min(3, +(settings.fontsize + 0.1).toFixed(2));
    aplicarCambios();
    // update aria-pressed based on whether font size is larger than default
    $(this).attr('aria-pressed', settings.fontsize > 1);
});

$('#untoggle-text-size').on('click', function() {
    settings.fontsize = Math.max(0.5, +(settings.fontsize - 0.1).toFixed(2));
    aplicarCambios();
    $(this).attr('aria-pressed', settings.fontsize > 1);
});

$('#reset-accessibility').on('click', function() {
    $('body').removeClass('dark-mode clear-mode saturation-high saturation-low grayscale text-size-large');
    $('.accessibility-option').attr('aria-pressed', 'false');
    settings = {
        fontsize: 1,
        lineheight: 1.5,
        wordSpace: 0,
        letterSpace: 0,
        contrast: 'normal'
    };
    aplicarCambios();
});

function aplicarCambios() { 
    // build combined filter from saturation and contrast settings
    var filters = [];
    if (settings.saturation === 'high') filters.push('saturate(150%)');
    else if (settings.saturation === 'low') filters.push('saturate(50%)');
    if (settings.contrast === 'high') filters.push('contrast(150%)');
    else if (settings.contrast === 'low') filters.push('contrast(50%)');
    var filterValue = filters.length ? filters.join(' ') : 'none';

    $('body').css({
        'font-size': settings.fontsize + 'em',
        'line-height': settings.lineheight,
        'word-spacing': settings.wordSpace + 'em',
        'letter-spacing': settings.letterSpace + 'em',
        'filter': filterValue
    });
 }