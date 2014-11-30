/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.dianzixiaoneng= og.dianzixiaoneng|| {};
og.dianzixiaoneng= {
    add: function () {
        var url = og.getUrl('dianzixiaoneng', 'add_xuke');
        og.openLink(url, {post: {}});
    }
}

