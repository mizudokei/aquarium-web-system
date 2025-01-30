// /public/assets/getSelectedDate.js
import dayjs from 'dayjs';
import 'dayjs/locale/ja';  // 日本語ロケールをインポート
import renderTimeSlotOptions from './renderTimeSlotUI';

export function displaySelectedDate(selectedDate) {
  const formattedDate = dayjs(selectedDate).locale('ja').format('YYYY年MM月DD日（ddd）'); // ゼロ埋めしてフォーマット
  localStorage.setItem('selectedDate', formattedDate);  // localStorageに保存
  const dateElement = document.getElementById('selected-date');
  if (dateElement) {
    dateElement.textContent = formattedDate;  // HTMLに選択された日付を表示
  }
  renderTimeSlotOptions(selectedDate);  // 日付選択後に時間帯オプションを表示
}

export function loadSelectedDate() {
  localStorage.getItem('selectedDate');
}
