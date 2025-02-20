import { FC } from "react";
import { chakra } from "@chakra-ui/react";
import dayjs from "dayjs";

export const MonthSwitch: FC<{
  currentMonth: dayjs.Dayjs; // 現在表示中の月
  onMonthChange: (newMonth: dayjs.Dayjs) => void; // 月変更時のコールバック
}> = ({ currentMonth, onMonthChange }) => {
  const today = dayjs();
  const maxMonth = today.add(2, "month"); // 3ヶ月後の月
  const minMonth = today.startOf("month"); // 現在の月の初日

  // 現在の月より前に移動することを防ぐ
  const showPreviousMonth = () => {
    if (currentMonth.isAfter(minMonth, "month")) {
      onMonthChange(currentMonth.subtract(1, "month"));
    }
  };

  // 現在の月より後に移動することを防ぐ
  const showNextMonth = () => {
    if (currentMonth.isBefore(maxMonth, "month")) {
      onMonthChange(currentMonth.add(1, "month"));
    }
  };

  return (
    <chakra.div
      py="12px"
      px="16px"
      display="flex"
      justifyContent="space-between"
      borderTop="1px solid #D1D1D1"
      borderBottom="1px solid #D1D1D1"
    >
      <chakra.button
        onClick={showPreviousMonth}
        aria-label="前の月"
        isDisabled={currentMonth.isSame(minMonth, "month")}
        opacity={currentMonth.isSame(minMonth, "month") ? 0.5 : 1}
      >
        <img src="assets/icons/arrow_left.svg" alt="" />
      </chakra.button>
      <chakra.p fontSize="14">
        {currentMonth.format("YYYY年M月")}
      </chakra.p>
      <chakra.button
        onClick={showNextMonth}
        aria-label="次の月"
        isDisabled={currentMonth.isSame(maxMonth, "month")}
        opacity={currentMonth.isSame(maxMonth, "month") ? 0.5 : 1}
      >
        <img src="assets/icons/arrow_right.svg" alt="" />
      </chakra.button>
    </chakra.div>
  );
};
