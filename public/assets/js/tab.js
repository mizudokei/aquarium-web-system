$(document).ready(function () {
    $(".tab").on("click", function () {
        if ($(this).hasClass("disabled")) return;

        $(".tab").removeClass("active");
        $(this).addClass("active");

        $(".tab-content").removeClass("active");
        const tabId = $(this).attr("id").replace("tab-", "content-");
        $("#" + tabId).addClass("active");
    });
});