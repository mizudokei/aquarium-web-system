import dayjs from 'dayjs';
import renderTicketTypes from './renderSelectTicketUI';

let selectedTimeSlot;

function renderTimeSlotOptions(selectedDate) {
	$("#time-slot-container").show(); // チケット情報を表示

	selectedDate = dayjs(selectedDate).format('YYYY-MM-DD');
	const timeSlotContainer = $("#time-slot-container");
	timeSlotContainer.empty();
	timeSlotContainer.prop('disabled', false);

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

	const now = dayjs();
	const nowInMinutes = now.hour() * 60 + now.minute();
	const isToday = dayjs().isSame(selectedDate, 'day');

	while (currentTime < endTimeInMinutes) {
		if (!isToday || currentTime >= nowInMinutes) {
			const hour = String(Math.floor(currentTime / 60)).padStart(2, "0");
			const minute = String(currentTime % 60).padStart(2, "0");
			const endHour = String(Math.floor((currentTime + 30) / 60)).padStart(2, "0");
			const endMinute = String((currentTime + 30) % 60).padStart(2, "0");
			const timeLabel = `${hour}:${minute}～${endHour}:${endMinute}`;

			const timeSlotDiv = $(`<div class='time-slot'><span>${timeLabel}</span></div>`);
			timeSlotDiv.on("click", function () {
				$(".time-slot").removeClass("selected");
				$(this).addClass("selected");
				selectedTimeSlot = timeLabel;
				$("#selected-time").text(selectedTimeSlot);
				
				// 「チケット」タブを有効化して選択状態にする
				$("#tab-ticket").removeClass("disabled").prop("disabled", false).trigger("click");

				// チケット選択 UI を表示
				renderTicketTypes();
			});

			timeSlotContainer.append(timeSlotDiv);
		}
		currentTime += 30;
	}
}

export default renderTimeSlotOptions;
