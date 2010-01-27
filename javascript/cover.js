// Anzeige von Buchcovern in gso.gbv.de - http://www.gbv.de/wikis/cls/Buchcover
// Matthias Lange <matthias.lange@gbv.de>

// id = 9-78184195953-5
// id = gvk:ppn:601820754

$(function() {
    $('.cover,.smallcover').each(function() {
        var me = $(this);
        var id = me.attr("title");
        if (!id) return;
        me.attr('title','');

        var url = "http://ws.gbv.de/covers/?format=seealso&callback=?&id=" + id;
        $.getJSON(url, function(response) {
            if (!response || !response[2] || !response[3] || !response[3][0]) return;
            var imghref = response[3][0];
            var size = response[2][0].split('x');
                var width = me.is('.smallcover') ? '60' : '120';
                var height = Math.round(size[1]/size[0]*width);
                var type = "image";
                if (response[1]) type = response[1][0];
                me.replaceWith($('<img src="' + imghref + '"' + 
                    'alt="' + type + '"' + 
                    'title="' + type + '"' +
                    'width="' + width + '"' + 'height="' + height + '"' + 
                    'style="width:' + width + ';' + 'height:' + height + ';float:left"' +
                    'border="0">'
                )).show();
                /*img.hover(
                        function(){
                            $(this).attr({'width' : size[0], 'height' : size[1]})
                                .css({'width' : size[0], 'height' : size[1]});
                        }, function(){
                            $(this).attr({'width' : width, 'height': height})
                                .css({'width' : width, 'height' : height});
                        }
                );*/
        });
    });
});