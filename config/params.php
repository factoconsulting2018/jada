<?php

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    
    // WhatsApp Configuration
    'whatsapp' => [
        'number' => '1234567890', // Número de WhatsApp del administrador (sin + ni espacios)
        'defaultMessage' => 'Hola, me interesa el siguiente producto:',
    ],
    
    // Upload paths
    'uploadPath' => '@webroot/uploads/',
    'uploadUrl' => '@web/uploads/',
    'productImagePath' => '@webroot/uploads/products/',
    'productImageUrl' => '@web/uploads/products/',
    'bannerImagePath' => '@webroot/uploads/banners/',
    'bannerImageUrl' => '@web/uploads/banners/',
    'categoryImagePath' => '@webroot/uploads/categories/',
    'categoryImageUrl' => '@web/uploads/categories/',
    
    // Image settings
    'image' => [
        'maxSize' => 5 * 1024 * 1024, // 5MB
        'allowedTypes' => ['image/jpeg', 'image/png', 'image/webp'],
    ],
];

