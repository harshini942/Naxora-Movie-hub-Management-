document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('booking_date');
    const suiteSelect = document.getElementById('suite_id');

    if (dateInput && suiteSelect) {
        dateInput.addEventListener('change', checkSlotAvailability);
        suiteSelect.addEventListener('change', checkSlotAvailability);
        checkSlotAvailability();
    }
});

let selectedSlotId = null;

async function checkSlotAvailability() {
    const suiteId = document.getElementById('suite_id').value;
    const date = document.getElementById('booking_date').value;
    const slotsContainer = document.getElementById('slots_container');

    if (!date || !suiteId) return;

    slotsContainer.innerHTML = 'Loading real-time availability...';

    try {
        const response = await fetch(`api/bookings/check_availability.php?suite_id=${suiteId}&date=${date}`);
        const data = await response.json();

        if (data.status === 'success') {
            const bookedSlots = data.booked_slots;
            renderSlotButtons(bookedSlots);
        }
    } catch (error) {
        console.error('Error fetching slot status:', error);
    }
}

function renderSlotButtons(bookedSlots) {
    const slotsContainer = document.getElementById('slots_container');
    slotsContainer.innerHTML = '';

    const allSlots = [
        { id: 1, time: '10:00 AM - 12:00 PM' },
        { id: 2, time: '12:30 PM - 02:30 PM' },
        { id: 3, time: '03:00 PM - 05:00 PM' },
        { id: 4, time: '05:30 PM - 07:30 PM' },
        { id: 5, time: '08:00 PM - 10:00 PM' }
    ];

    allSlots.forEach(slot => {
        const btn = document.createElement('div');
        const isBooked = bookedSlots.includes(slot.id);

        btn.className = `slot-btn ${isBooked ? 'disabled' : ''}`;
        btn.innerText = slot.time;

        if (!isBooked) {
            btn.onclick = () => {
                document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                selectedSlotId = slot.id;
                document.getElementById('selected_slot_id').value = slot.id;
                calculateTotal();
            };
        }

        slotsContainer.appendChild(btn);
    });
}

function calculateTotal() {
    const suiteSelect = document.getElementById('suite_id');
    const selectedOption = suiteSelect.options[suiteSelect.selectedIndex];
    const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
    
    let addonTotal = 0;
    document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        addonTotal += parseFloat(cb.getAttribute('data-price')) || 0;
    });

    const total = (rate * 2) + addonTotal; // Default 2 Hours
    document.getElementById('total_amount_display').innerText = `LKR ${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    document.getElementById('total_amount_input').value = total;
}