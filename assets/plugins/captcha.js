$(document).ready(function(){
    $(document).on('input', '#belcms_captcha_slider', function () {
        let value = parseInt($(this).val());

        $('#belcms_captcha_percent').text(value + '%');
        $('#belcms_captcha_value').val(value);

        if (value < 25) {

            $('#belcms_captcha_percent')
                .css('color', '#00c853')
                .html(value + '% ➜ Déplacez davantage le curseur');

        } else if (value >= 85) {

            $('#belcms_captcha_percent')
                .css('color', '#d50000')
                .html(value + '% ❌ Zone rouge');

        } else {

            $('#belcms_captcha_percent')
                .css('color', '#00c853')
                .html(value + '% ✓ Validation possible');

        }

    });
});