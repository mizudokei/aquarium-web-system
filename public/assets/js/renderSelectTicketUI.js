// イベントの登録処理
function registerTicketEventHandlers() {
	// 既存のクリックイベントを解除
	$(document).off("click", ".increase, .decrease");

	// クリックイベントを1回だけセット
	$(document).on("click", ".increase, .decrease", function () {
		const admissionFeeTypeId = $(this).data("id");
		const inputField = $(`#ticket-quantity-${admissionFeeTypeId}`);
		let quantity = parseInt(inputField.val()) || 0;

		if ($(this).hasClass("increase")) {
			quantity++;
			console.log("増加");
		} else if ($(this).hasClass("decrease") && quantity > 0) {
			quantity--;
		}

		inputField.val(quantity);
		updateTotal();

		// チケット枚数を更新した後、ログに出力
		const ticketQuantities = getTicketQuantities();
		console.log("増減処理　作動");
		console.log(ticketQuantities);
	});
}

// チケットUIを描画する関数
function renderTicketTypes() {
	$("#ticket-info").show();
	const ticketTypesList = $("#ticket-types-list");
	ticketTypesList.empty();

	ticketTypes.forEach(ticket => {
		const card = `
			<div class="ticket-card">
				<div class="ticket-detail">
					<div class="ticket-name">${ticket.type}</div>
					<div class="ticket-price">¥${ticket.price}</div>
				</div>
				<div class="ticket-counter">
					<button class="cnt-btn decrease" data-id="${ticket.id}">-</button>
					<input type="text" value="0" id="ticket-quantity-${ticket.id}" readonly>
					<button class="cnt-btn increase" data-id="${ticket.id}">+</button>
				</div>
			</div>
		`;
		ticketTypesList.append(card);
	});

	// チケットUIを描画した後にイベントリスナーを登録
	registerTicketEventHandlers();
}


export function getTicketQuantities() {
	const quantities = [];

	ticketTypes.forEach(ticket => {
		const quantity = parseInt($(`#ticket-quantity-${ticket.id}`).val()) || 0;
		quantities.push({
			admissionFeeTypeId: ticket.id,
			ticketType: ticket.type,
			ticketPrice: ticket.price,
			quantity: quantity
		});
	});
	return quantities;
}

function updateTotal() {
	let totalQuantity = 0;
	let totalPrice = 0;

	ticketTypes.forEach(ticket => {
		const quantity = parseInt($(`#ticket-quantity-${ticket.id}`).val());
		totalQuantity += quantity;
		totalPrice += ticket.price * quantity;
	});

	$("#total-quantity").text(totalQuantity);
	$("#total-price").text(totalPrice);
}

export default renderTicketTypes;
