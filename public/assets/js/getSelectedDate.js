import dayjs from 'dayjs';
import 'dayjs/locale/ja';
import renderTimeSlotOptions from './renderTimeSlotUI';

export function displaySelectedDate(selectedDate) {
	const formattedDate = dayjs(selectedDate).locale('ja').format('YYYY年MM月DD日（ddd）');
	localStorage.setItem('selectedDate', formattedDate);
	const dateElement = document.getElementById('selected-date');
	if (dateElement) {
		dateElement.textContent = formattedDate;
	}

	renderTimeSlotOptions(selectedDate);

	// 「時間選択」タブを有効化＆選択状態にする
	$("#tab-time").removeClass("disabled").trigger("click");
}

export function loadSelectedDate() {
	return localStorage.getItem('selectedDate');
}
