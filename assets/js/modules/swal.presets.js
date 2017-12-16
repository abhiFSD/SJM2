function destructive_swal(title, text, button_text, callback) {
    swal(
        {
            title: title,
            text: text,
            type: "warning",
            confirmButtonColor: "#E64942", // this doesn't work
            confirmButtonText: button_text,
            cancelButtonText: "Cancel",
            showCancelButton: true,
            dangerMode: true,
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(confirm) {
            if (confirm) {
                callback.call()
            }
        }
    );
}

function info_swal(title, text, button_text, callback) {
    swal(
        {
            title: title,
            text: text,
            type: "info",
            confirmButtonText: button_text,
            cancelButtonText: "Cancel",
            showCancelButton: true,
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(confirm) {
            if (confirm) {
                callback.call()
            }
        }
    );
}
