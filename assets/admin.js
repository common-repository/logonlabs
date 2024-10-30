jQuery(document).ready(function($){

    $('.copy').on('click', function(){
        var str = $('.callback_url').text();
        const el = document.createElement('textarea');
        el.value = str;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    });
})