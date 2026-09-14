// Плавный скролл к тарифам
function scrollToTariffs() {
    document.getElementById('tariffs').scrollIntoView({ behavior: 'smooth' });
}

// Управление модальным окном
const modal = document.getElementById('orderModal');
const tariffInput = document.getElementById('tariffInput');
const priceInput = document.getElementById('priceInput');
const modalTariffInfo = document.getElementById('modalTariffInfo');

function openModal(tariff) {
    let tariffName = '';
    let price = '';
    
    if (tariff === 'diagnosis') {
        tariffName = 'Тариф «Диагноз»';
        price = '3900';
    } else if (tariff === 'system') {
        tariffName = 'Тариф «Система»';
        price = '7900';
    }
    
    tariffInput.value = tariff;
    priceInput.value = price;
    modalTariffInfo.textContent = `${tariffName} — ${price} ₽`;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // Блокируем скролл фона
}

function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = '';
    document.getElementById('orderForm').reset();
}

// Закрытие по клику вне окна
window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}

// Обработка формы (AJAX)
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('.btn-submit');
    const originalText = submitBtn.textContent;
    
    submitBtn.textContent = 'Обработка...';
    submitBtn.disabled = true;

    fetch('php/send-order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Если Яндекс Сплит возвращает ссылку на оплату
            if (data.payment_url) {
                window.location.href = data.payment_url;
            } else {
                alert('Заявка принята! Мы свяжемся с вами для оплаты.');
                closeModal();
            }
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        alert('Произошла ошибка при отправке формы.');
        console.error('Error:', error);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});