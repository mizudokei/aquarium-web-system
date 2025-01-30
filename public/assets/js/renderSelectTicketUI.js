function renderTicketTypes() {
    $("#ticket-info").show();
    const ticketTypesList = $("#ticket-types-list");
    ticketTypesList.empty();
    ticketTypes.forEach(ticket => {
        const row = `<tr>
                        <td>${ticket.type}</td>
                        <td>¥${ticket.price}</td>
                        <td>
                            <div class="ticket-counter">
                                <button class="decrease" data-id="${ticket.id}">-</button>
                                <input type="text" value="0" id="ticket-quantity-${ticket.id}" readonly>
                                <button class="increase" data-id="${ticket.id}">+</button>
                            </div>
                        </td>
                    </tr>`;
        ticketTypesList.append(row);
    });
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

$("#submit-button").on("click", function () {
    console.log(ticketQuantities);
});

// チケット枚数の増減処理
$(document).off("click", ".increase, .decrease").on("click", ".increase, .decrease", function () {
    const admissionFeeTypeId = $(this).data("id");
    const inputField = $(`#ticket-quantity-${admissionFeeTypeId}`);
    let quantity = parseInt(inputField.val());
    
    if ($(this).hasClass("increase")) {
        quantity++;
    } else if ($(this).hasClass("decrease") && quantity > 0) {
        quantity--;
    }
    
    inputField.val(quantity);
    updateTotal();
    
    // チケット枚数を更新した後、ログに出力
    const ticketQuantities = getTicketQuantities();
    console.log(ticketQuantities);
});


// 合計枚数と金額の更新
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
