<?php
header('Content-Type: application/json; charset=utf-8');

// 1. НАСТРОЙКИ (Замените на свои данные)
$admin_email = 'vash_email@example.com'; // Куда присылать уведомления
$site_name = 'Деньги под контролем';

// 2. Получение и очистка данных
$tariff = htmlspecialchars(trim($_POST['tariff'] ?? ''));
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$price = intval($_POST['price'] ?? 0);
$offer_agree = isset($_POST['offer']) ? 1 : 0;
$privacy_agree = isset($_POST['privacy']) ? 1 : 0;

// 3. Валидация
if (empty($name) || empty($email) || !$offer_agree || !$privacy_agree) {
    echo json_encode(['success' => false, 'message' => 'Заполните обязательные поля и примите условия.']);
    exit;
}

// 4. Отправка уведомления на почту
$subject = "Новый заказ: $tariff ($price руб.)";
$message = "
    <h2>Новая заявка на сайте $site_name</h2>
    <p><b>Тариф:</b> $tariff</p>
    <p><b>Сумма:</b> $price руб.</p>
    <p><b>Имя:</b> $name</p>
    <p><b>Email:</b> $email</p>
    <p><b>Телефон:</b> $phone</p>
    <p><b>Согласие с офертой:</b> Да</p>
    <p><b>Согласие с политикой:</b> Да</p>
";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: $site_name <noreply@yoursite.com>" . "\r\n";

@mail($admin_email, $subject, $message, $headers);

// 5. Интеграция с Яндекс Сплит (ЮKassa)
/* 
   ВАЖНО: Яндекс Сплит подключается через ЮKassa. 
   Для автоматического создания платежа нужно использовать API ЮKassa.
   Ниже приведен упрощенный пример. В реальности здесь должен быть cURL запрос к API ЮKassa.
   
   $payment_url = "https://yoomoney.ru/quickpay/confirm?receiver=ВАШ_КОШЕЛЕК&sum=$price&label=$email";
   
   Если у вас пока нет настроенного API, просто верните ссылку на вашу ручную оплату или оставьте пустым.
*/

// Временная заглушка (замените на реальную ссылку ЮKassa/Сплит или логику API)
$payment_url = "https://yoomoney.ru/quickpay/confirm?receiver=410011111111111&sum=$price&label=$email&comment=Оплата+$tariff"; 

echo json_encode([
    'success' => true,
    'payment_url' => $payment_url // Скрипт в JS перенаправит пользователя сюда
]);
?>
