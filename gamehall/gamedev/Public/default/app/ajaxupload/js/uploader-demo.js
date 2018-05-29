$(document).ready(function() {
    var errorHandler = function(event, id, fileName, reason) {
        qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
    };

    var uploader = new qq.FineUploader({
        element: $('#basicUploadSuccessExample')[0],
        debug: true,
        request: {
            endpoint: "/upload/save?type=image"
        },
        callbacks: {
            onError: errorHandler
        }
    });

});
