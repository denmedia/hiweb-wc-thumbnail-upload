/**
 * Created by denmedia on 11.03.2017.
 */

jQuery(document).ready(function ($) {
    $('.wp-list-table tbody tr[id]').each(function () {
        var tr = $(this);

        if (tr.attr('id').match(/^post-/) != null) {
            var post_id = tr.attr('id').replace('post-', '');
            var type = 'post';
        } else {
            var post_id = tr.attr('id').replace('tag-', '');
            var type = 'taxonomy';
        }

        var thumb_hw_upload_zone_id = 'thumb-' + post_id;
        var drop_zone = $('<div class="thumb_hw_upload_zone" id="' + thumb_hw_upload_zone_id + '"></div>');
        tr.find('.thumb').append(drop_zone);
        new hw_upload_zone("#" + thumb_hw_upload_zone_id, {
            url: ajaxurl + '?action=takao_upload',
            headers: {'postid': post_id, 'posttype': type},
            maxFilesize: 20,
            filesizeBase: 1024,
            previewsContainer: false,
            type: 'post',
            dataType: 'json',
            data: {do: 'upload', post_id: post_id},
            dragenter: function () {
                drop_zone.addClass('dragenter');
            },
            dragleave: function () {
                drop_zone.removeClass('dragenter');
            },
            addedfile: function (data) {
                drop_zone.addClass('upload-process');
                drop_zone.removeClass('dragenter');
            },
            complete: function (answer) {
                drop_zone.removeClass('upload-process');
                drop_zone.removeClass('dragenter');
                var response = answer.xhr.response;
                var data = $.parseJSON(response);
                if (typeof data == 'object') {
                    if (data[0] == false) {
                        alert('в ходе загрузки произошла ошибка: ' + data[1]);
                    } else {
                        tr.find('.thumb img').remove();
                        tr.find('.thumb').prepend(data[1]);
                    }
                } else {
                    alert('В ходе загрузки произошла ошибка: 1');
                    console.warn(data);
                }
            }
        });
    });
});