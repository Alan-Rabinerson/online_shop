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

$('#toggle-interlineado').on('click', function() {
    settings.lineheight = Math.min(3, +(settings.lineheight + 0.1).toFixed(2));
    aplicarCambios();
    $(this).attr('aria-pressed', settings.lineheight > 1.5);
});

$('#untoggle-interlineado').on('click', function() {
    settings.lineheight = Math.max(1, +(settings.lineheight - 0.1).toFixed(2));
    aplicarCambios();
    $(this).attr('aria-pressed', settings.lineheight > 1.5);
});

$('#toggle-espacio-palabras').on('click', function() {
    settings.wordSpace = Math.min(3, +(settings.wordSpace + 0.1).toFixed(2));
    aplicarCambios();
    $(this).attr('aria-pressed', settings.wordSpace > 0);
});

$('#untoggle-espacio-palabras').on('click', function() {
    settings.wordSpace = Math.max(0, +(settings.wordSpace - 0.1).toFixed(2));
    aplicarCambios();
    $(this).attr('aria-pressed', settings.wordSpace > 0);
});

$('#toggle-espacio-letras').on('click', function() {
    settings.letterSpace = Math.min(3, +(settings.letterSpace + 0.1).toFixed(2));
    aplicarCambios();
    $(this).attr('aria-pressed', settings.letterSpace > 0);
});

$('#untoggle-espacio-letras').on('click', function() {
    settings.letterSpace = Math.max(0, +(settings.letterSpace - 0.1).toFixed(2));
    aplicarCambios();
    $(this).attr('aria-pressed', settings.letterSpace > 0);
});

$('#toggle-contrast').on('click', function() {
    if (settings.contrast === 'normal') {
        settings.contrast = 'high';
        $('body').addClass('contrast-clear').removeClass('contrast-dark');
    } else {
        settings.contrast = 'normal';
        $('body').removeClass('contrast-clear contrast-dark');
    }
});

$('#close-accessibility').on('click', function() {
    accessibilityMenu.removeClass('active');
});

$('#reset-accessibility').on('click', function() {
    $('body').removeClass('dark-mode clear-mode saturation-high saturation-low grayscale text-size-large contrast-clear contrast-dark');
    $('.accessibility-option').attr('aria-pressed', 'false');
    settings = {
        fontsize: 1,
        lineheight: 1.5,
        wordSpace: 0,
        letterSpace: 0,
        contrast: 'normal',
        saturation: 'normal'
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
        'filter': filterValue,
    });
 }