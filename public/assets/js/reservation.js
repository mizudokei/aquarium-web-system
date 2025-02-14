// reservation.js
import { getTicketQuantities } from './renderSelectTicketUI.js';

$(document).ready(function() {    
    $("#confirm-reservation").on("click", function () {
        const ticketQuantities = getTicketQuantities();


        // 必要なデータをセッションに保存
        const selectedDate = $("#selected-date").text();
        const selectedTimeSlot = $("#selected-time").text();

        // フォーム送信
        $.ajax({
            type: "POST",
            url: "/?process=set_session", // セッションにデータを保存するPHPファイル
            data: {
                selectedDate: selectedDate,
                selectedTimeSlot: selectedTimeSlot,
                ticketQuantities: JSON.stringify(ticketQuantities)
            },
            success: function(response) {
                console.log("Session set successfully");
                // ボタンを表示
                $("#confirm-reservation").show();
            },
            error: function(response) {
                console.log("Error occurred:", response);
            }
        });
    });
});
