seajs.config({
    //debug: true,
    base: '/App/Public/Media/js/',
    charset: 'utf-8',
    alias: {
        cookie: 'jquery_cookie_cmd.js',
        jquery: 'jquery.js',
        weiboyi: 'weiboyi_all_cmd.js',
        form: 'jquery_form_cmd.js',
        rest: 'rest_cmd.js',
        doT: 'doT_cmd.js',
        seajsText: 'seajs-text.js',
        seajsCss: 'seajs-css.js',
        knockout: 'knockout_cmd.js',
        plupload: 'plupload_cmd.js',
        knockout_plupload: 'knockout/knockout_plupload.js',
        knockout_placeholder: 'knockout/knockout_placeholder.js',
        knockout_mapping: 'knockout/mapping_debug.js',
        knockout_validation: 'knockout/knockout.validation.js',
        plupload_full: 'plupload.full.js',
    }
});
/**
 * 已经加载的模块直接执行，这里是同步阻塞的执行
 * @param id
 * @returns {*}
 */
seajs.execImmediateInCache = function(id) {
    var mod = seajs.cache[seajs.resolve(id)];
    if (mod) {
        return mod.exec();
    }
    return null;
};