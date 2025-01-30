import dayjs from 'dayjs';
import renderTicketTypes from './renderSelectTicketUI';

let selectedTimeSlot;

function renderTimeSlotOptions(selectedDate) {
    $("#selected-date-info").show(); // チケット情報を表示

    selectedDate = dayjs(selectedDate).format('YYYY-MM-DD');
    const timeSlotSelect = $("#time-slot");
    timeSlotSelect.empty();
    timeSlotSelect.prop('disabled', false);

    // salesDays と workingHours を参照
    const salesDay = salesDays.find(day => day.date === selectedDate);
    if (!salesDay) return;

    const workingHour = workingHours.find(hour => hour.id == salesDay.working_hour_id);
    if (!workingHour) return;

    const startTime = workingHour.start_time.split(":");
    const endTime = workingHour.end_time.split(":");
    const startHour = parseInt(startTime[0]);
    const startMinute = parseInt(startTime[1]);
    const endHour = parseInt(endTime[0]);
    const endMinute = parseInt(endTime[1]);

    let currentTime = startHour * 60 + startMinute;
    const endTimeInMinutes = endHour * 60 + endMinute;

    // 現在の時刻を取得（分単位）
    const now = dayjs();
    const nowInMinutes = now.hour() * 60 + now.minute();

    // 当日かどうかのフラグ
    const isToday = dayjs().isSame(selectedDate, 'day');

    let timeSlots = [];
    while (currentTime < endTimeInMinutes) {
        // タイムスロットの開始時刻が現在時刻より後の場合のみ追加（当日の場合）
        if (!isToday || currentTime >= nowInMinutes) {
            const hour = String(Math.floor(currentTime / 60)).padStart(2, "0");
            const minute = String(currentTime % 60).padStart(2, "0");
            const timeLabel = `${hour}:${minute}～${String(Math.floor((currentTime + 30) / 60)).padStart(2, "0")}:${String((currentTime + 30) % 60).padStart(2, "0")}`;
            timeSlots.push(timeLabel);
        }
        currentTime += 30;
    }

    timeSlots.forEach(timeSlot => {
        timeSlotSelect.append(new Option(timeSlot, timeSlot));
    });
}

$("#time-slot").on("change", function () {
    // 選択された値の取得
    selectedTimeSlot = $(this).val();

    if (selectedTimeSlot) {
        $("#selected-time").text(selectedTimeSlot);
        renderTicketTypes(); // チケット種類の表示
    } else {
        $("#ticket-info").hide();
    }
});

export default renderTimeSlotOptions;
