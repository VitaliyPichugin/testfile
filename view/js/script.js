jQuery(document).ready(function($) {
    $('#checkUrl').on('click', function (e) {
        e.preventDefault();
        let url = $('#url').val();

        if (isValidateURL(url)) {
            $.ajax({
                method: 'POST',
                url: 'index.php',
                data: {
                    url: url
                },
                beforeSend : function () {
                    $('.table-report').css('display', 'none');
                  $('.preload').append(`<img src='https://cdn-images-1.medium.com/max/1600/0*cWpsf9D3g346Va20.gif' class="img-fluid">`);
                },
                success: function (data) {
                    $('.preload').html('');
                    let content = `<html>` + data;
                    let res = $(content).find('tbody').html();
                    $('tbody').html(res);
                    $('#downloadXls').css('display', 'block');
                    $('.table-report').css('display', 'table');
                },
                error: function (e) {
                    console.log(e);
                }
            });
        } else {
            console.log('error');
        }
    });
});

function isValidateURL(url) {
    return /^((https?|ftp)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})(\/?)$/.test(url);
}

