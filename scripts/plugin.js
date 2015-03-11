function execAjaxRequest(sJson, sUrl, bIsSync, onSuccessCallback) {
    $.ajax({
        type: "POST",
        async: bIsSync ? false : true,
        url: sUrl,
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(sJson),
        //error: OnError,
        success: function (data) {
            if (onSuccessCallback) {
                onSuccessCallback(data);
            }
        }
    });
}

function initMainScreen (siteRoot, $) {
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
}