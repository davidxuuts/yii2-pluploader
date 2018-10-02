/**
 * Project: qianfan-appdemo
 * User: liyifei
 * Date: 15-9-29
 * Time: 14:40
 */
var FilesRemoved = function (up, files) {
    plupload.each(files, function (file) {
        if(document.getElementById(file.id)!=null)
            document.getElementById(file.id).remove();
    });
};

var UploadProgress = function (up, file) {
    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
};

var UploadError = function (up, err) {
    if(err != undefined){
        alert("[" + err.code + "]" + err.message)
    }
};

$(document).on('click', '.imglist .imglist_delete', function () {
    $(this).parent().remove();
});
