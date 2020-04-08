
var PluploadCustom = function () {
    return {
        tplUploadItem: function (uploader, file) {
            var settings = uploader.settings;
            var path = file.path;
			var filesize = file.size;
			if (window.plupload) {
				filesize = window.plupload.formatSize(filesize);
            }
            var temp = '<li class="plupload_file plupload_file_loading" id="' + file.id + '">';

            if (settings.multi_selection === true) {
				temp += '<input id="' + settings.id + '-' + file.id + '" name="' + settings.input_name + '[' + file.id + ']' + '" value="' + path + '" type="hidden" class="plupload_file_input">';
            }

			if (path === undefined) {
                path = settings.error_image_url;
            }

            temp += '<div class="plupload_file_thumb"><img src="' + path + '"></div>';

            temp += '<div class="plupload_file_status">' +
                    '<div class="plupload_file_progress">' +
                    '<div class="plupload_file_percent">?</div>' +
                    '<div class="progress">' +
                    '<span class="progress-bar"></span>' +
                    '</div>' +
                    '<span class="plupload_file_mark">准备上传</span>' +
                    '</div>' +
                    '</div>';

            temp += '<div class="plupload_file_name"><span>' + file.name + '</span></div>';

			
            temp += '<div class="plupload_file_size">' + filesize + '</div>';
            temp += '<div class="plupload_file_action">' +
                    '<span class="plupload_action_icon">移除</span>' +
                    '</div>';
            return temp += '</li>';
        }
    };
}();