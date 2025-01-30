import { FC, useEffect, useRef, useState } from "react";
import { chakra } from "@chakra-ui/react";
import dayjs from "dayjs";
import { useClickAway } from "react-use";
import axios from "axios";
const { displaySelectedDate } = require("../../../public/assets/js/getSelectedDate.js");

const today = dayjs();
const weekDays = [
	{ keyText: "sunday", weekDay: "日" },
	{ keyText: "monday", weekDay: "月" },
	{ keyText: "tuesday", weekDay: "火" },
	{ keyText: "wednesday", weekDay: "水" },
	{ keyText: "thursday", weekDay: "木" },
	{ keyText: "friday", weekDay: "金" },
	{ keyText: "saturday", weekDay: "土" },
];

type Date = {
	year: string;
	month: string;
	day: string;
};

export const CalendarCells: FC<{
	calendarData: Array<Date>;
}> = ({ calendarData }) => {
	const containerRef = useRef<HTMLTableElement>(null);
	const [isFocusInCalendar, setIsFocusInCalendar] = useState(false);
	const [operationalDays, setOperationalDays] = useState<string[]>([]);

	useClickAway(containerRef, () => setIsFocusInCalendar(false));

	// 営業日データをAPIから取得
	useEffect(() => {
		(async () => {
			try {
				const response = await axios.get("/src/controllers/sales_days.php");
				setOperationalDays(response.data);
			} catch (error) {
				console.error("営業日の取得に失敗しました：", error);
			}
		})();
	}, []);

	// 1週間ごとの2次元配列に変換する
	const calendars = calendarData.reduce(
		(prev, current) => {
			if (prev[prev.length - 1].length < 7) {
				prev[prev.length - 1].push(current);
			} else {
				prev.push([current]);
			}
			return prev;
		},
		[[]] as Array<Array<Date>>
	);

	const onKeyDown = (e: React.KeyboardEvent<HTMLButtonElement>) => {
		if (e.key === "Tab") {
			setTimeout(() => setIsFocusInCalendar(false), 10);
			return;
		}

		e.preventDefault();
		if (
			e.key !== "ArrowLeft" &&
			e.key !== "ArrowRight" &&
			e.key !== "ArrowUp" &&
			e.key !== "ArrowDown" &&
			e.key !== "Enter"
		) {
			return;
		}

		const focusedDate = dayjs((e.target as HTMLElement).dataset.date as string);

		const onChangeFocus = (diffDay: number) => {
			const movedDate = focusedDate.add(diffDay, "day");
			const movedElement = containerRef.current?.querySelector<HTMLButtonElement>(
				`[data-date="${movedDate.format("YYYY-M-D")}"]`
			);
			movedElement?.focus();
		};

		switch (e.key) {
			case "ArrowLeft":
				onChangeFocus(-1);
				break;
			case "ArrowRight":
				onChangeFocus(1);
				break;
			case "ArrowUp":
				onChangeFocus(-7);
				break;
			case "ArrowDown":
				onChangeFocus(7);
				break;
			case "Enter":
				alert(`${focusedDate.format("YYYY年M月D日")}をクリックしました`);
				break;
		}
	};

	return (
		<chakra.table
			width="100%"
			role="grid"
			ref={containerRef}
			onFocus={() => setIsFocusInCalendar(true)}
		>
			<chakra.thead>
				<chakra.tr
					role="row"
					display="grid"
					gridTemplateColumns="repeat(7, 1fr)"
				>
					{weekDays.map(({ keyText, weekDay }) => (
						<WeekDayCell key={keyText}>{weekDay}</WeekDayCell>
					))}
				</chakra.tr>
			</chakra.thead>

			<chakra.tbody minH="275px">
				{calendars.map((week, index) => (
					<chakra.tr key={`${index + 1}週目`} display="flex">
						{week.map(({ year, month, day }) => {
							const date = dayjs(`${year}-${month}-${day}`);
							const isOperationalDay = operationalDays.includes(
								date.format("YYYY-MM-DD")
							);
							const isToday = date.isSame(today, "day");
							const isWeekend = date.day() === 0 || date.day() === 6;
							const isBeforeToday = date.isBefore(today, "day");

							return (
								<DateCell key={`${year}年${month}月${day}日`}>
									<chakra.button
										tabIndex={isFocusInCalendar ? -1 : 0}
										aria-label={`${year}年${month}月${day}日`}
										disabled={isBeforeToday || !isOperationalDay}
										{...(isToday && {
											"aria-current": "date",
											fontWeight: "bold",
										})}
										data-date={`${year}-${month}-${day}`}
										onClick={async () => {
											try {
											const selectedDate = `${year}-${month}-${day}`;
											// PHPに選択された日付を送信
											await axios.post("/src/views/admission_ticket_reservation.php", 
												new URLSearchParams({ selectedDate }),
												{ headers: { 'Content-Type': 'application/x-www-form-urlencoded' }}
											);
											console.log("選択された日付が送信されました：", selectedDate);
					
											// selectedDate.jsを呼び出して選択日を表示
											displaySelectedDate(selectedDate);
											} catch (error) {
											console.error("選択された日付の送信に失敗しました：", error);
											}
										}}										
										onKeyDown={onKeyDown}
										css={{
											...(isBeforeToday || !isOperationalDay ? {
												backgroundColor: "gray.300",
												color: "gray.00",
												opacity: 0.5,
												cursor: "not-allowed",
											} : {}),
											...(isWeekend && {
												color: date.day() === 0 ? "red" : "blue",
											}),
										}}
									>
										{day}
										{isOperationalDay && !isBeforeToday && (
											<chakra.span
												display="flex"
												justifyContent="center"
												alignItems="center"
												position="absolute"
												bottom="20px"
												left="50%"
												transform="translateX(-50%)"
											>
												<img src="../../../public/Vector.svg" alt="" />
											</chakra.span>
										)}
									</chakra.button>
								</DateCell>
							);
						})}
					</chakra.tr>
				))}
			</chakra.tbody>
		</chakra.table>
	);
};

const WeekDayCell = chakra("th", {
	baseStyle: {
		fontSize: "14px",
		fontWeight: "bold",
		height: "38px",
		display: "flex",
		justifyContent: "center",
		alignItems: "center",
		_first: { color: "red" },
		_last: { color: "blue" },
	},
});

const DateCell = chakra("td", {
	baseStyle: {
		height: "90px",
		width: "100%",
		margin: "0 -1px -1px 0",
		fontSize: "14px",
		border: "1px solid #EAEAEA",
		position: "relative",
		"& > button": {
			width: "100%",
			height: "100%",
			display: "flex",
			justifyContent: "center",
			alignItems: "flex-start",
			pt: "12px",
		},
	},
});
