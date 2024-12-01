<!DOCTYPE html>
<html>
<head>
    <title>Ціна змінилася</title>
</head>
<body>
<h1>Ціна змінилася!</h1>
<p>Шановний користувач,</p>
<p>Ціна для продукту за URL <strong>{{ $subscription->url }}</strong> змінилася.</p>
<p>Стара ціна: {{ $oldPrice }}</p>
<p>Нова ціна: {{ $subscription->price }}</p>
<p>Дякуємо за використання нашого сервісу.</p>
</body>
</html>
