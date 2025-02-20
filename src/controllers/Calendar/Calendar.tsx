import { FC, useState } from "react";
import { chakra } from "@chakra-ui/react";
import { MonthSwitch } from "./MonthSwitch/MonthSwitch";
import { CalendarCells } from "./CalendarCells/CalendarCells";
import dayjs from "dayjs";

export const Calendar: FC = () => {
	// 現在の月を状態として管理
	const [currentMonth, setCurrentMonth] = useState(dayjs());

	// 指定された月のカレンダー用データを生成する関数
	const generateCalendarData = (month: dayjs.Dayjs) => {
		const startOfMonth = month.startOf("month");
		const endOfMonth = month.endOf("month");
		const startDate = startOfMonth.startOf("week");
		const endDate = endOfMonth.endOf("week");

		const calendarData = [];
		let date = startDate;

		while (date.isBefore(endDate)) {
			calendarData.push({
				year: date.format("YYYY"),
				month: date.format("M"),
				day: date.format("D"),
			});
			date = date.add(1, "day");
		}

		return calendarData;
	};

	// 現在表示するカレンダーデータ
	const calendarData = generateCalendarData(currentMonth);

	return (
		<chakra.div maxW="700px" p="16px">
			{/* 月切り替えボタン */}
			<MonthSwitch
				currentMonth={currentMonth}
				onMonthChange={setCurrentMonth} // 月変更時の処理を渡す
			/>

			{/* カレンダー表示 */}
			<CalendarCells calendarData={calendarData} />
		</chakra.div>
	);
};
