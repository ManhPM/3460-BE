<!DOCTYPE html>
<html>

<head>
    <title>Thanh toán với Stripe</title>
</head>

<body>
    <h1>Thanh toán Sách Laravel - $19.99</h1>
    <x-form :action="route('user.createCheckoutSession')" type="post" :validate="true">
        <div class="row justify-content-center">
            <x-button.submit :title="__('Thanh toán qua Stripe')" />
        </div>
    </x-form>
</body>

</html>
