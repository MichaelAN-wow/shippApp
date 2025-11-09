<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>
<h1 align="center">Inventory Management System</h1>
<hr>


## Quick Installation

    Clone project
    
### Composer

    composer update
    
    
### For Environment Variable Create
 
    cp .env.example .env
 
    
 ### For Migration table in database [Create database name as ```IMS```]
 
    php artisan migrate

### Seed database
php artisan db:seed --class=UnitsAndConversionsSeeder

### Server ON ```url: http://127.0.0.1:8000/```

    php artisan serve

### Sync Products

    php artisan shopify:sync-products

### Sync Orders

    php artisan shopify:sync-orders

### Storage Link

    php artisan storage:link
