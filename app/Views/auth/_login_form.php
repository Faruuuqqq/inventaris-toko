<?php
/**
 * Login Form Partial
 * This is the form content used inside the login card
 * Uses reusable input and button components
 */
?>

<?php if (session()->has('error')): ?>
    <?= view('components/alert', [
        'type' => 'error',
        'message' => session('error')
    ]) ?>
<?php endif; ?>

<?php if (session()->has('success')): ?>
    <?= view('components/alert', [
        'type' => 'success',
        'message' => session('success')
    ]) ?>
<?php endif; ?>

<form action="<?= base_url('login') ?>" method="post" class="space-y-4">
    
    <?= view('components/input', [
        'name' => 'username',
        'type' => 'text',
        'label' => 'Username',
        'placeholder' => 'Masukkan username',
        'required' => true,
        'error' => validation_show_error('username') ?? ''
    ]) ?>

    <?= view('components/input', [
        'name' => 'password',
        'type' => 'password',
        'label' => 'Password',
        'placeholder' => '••••••••',
        'required' => true,
        'error' => validation_show_error('password') ?? ''
    ]) ?>

    <?= view('components/button', [
        'variant' => 'default',
        'size' => 'default',
        'type' => 'submit',
        'slot' => 'Masuk',
        'class' => 'w-full'
    ]) ?>
    
</form>
