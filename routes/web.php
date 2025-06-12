<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\AboutController; 
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\WorkshopController; 
use App\Http\Controllers\StoryController; 
use App\Http\Controllers\BlogController; 
use App\Http\Controllers\ContactController; 
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController; 
use App\Http\Controllers\UserDashboardController; // إضافة الكونترولر الجديد
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\StoriesController;
use App\Http\Controllers\Admin\WorkshopsController;
use App\Http\Controllers\Admin\ContactMessagesController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use Illuminate\Support\Facades\Route;

// المسارات العامة للموقع
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/products', [ProductController::class, 'index'])->name('products.index'); 
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show'); 
Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index'); 
Route::get('/workshops/{event}/register', [WorkshopController::class, 'showRegistrationForm'])->name('workshops.register.form'); 
Route::post('/workshops/{event}/register', [WorkshopController::class, 'register'])->name('workshops.register'); 
Route::post('/workshops/register-interest', [WorkshopController::class, 'registerInterest'])->name('workshops.register.interest');
Route::get('/stories', [StoryController::class, 'index'])->name('stories.index'); 
Route::get('/stories/submit', [StoryController::class, 'create'])->name('stories.create'); 
Route::post('/stories/submit', [StoryController::class, 'store'])->name('stories.store'); 
Route::get('/blog', [BlogController::class, 'index'])->name('blog'); 
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contact-us', [ContactController::class, 'create'])->name('contact.create'); 
Route::post('/contact-us', [ContactController::class, 'store'])->name('contact.store'); 

// طرق عربة التسوق
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add-item', [CartController::class, 'addItem'])->name('cart.add-item');
Route::patch('/cart/update-item/{itemId}', [CartController::class, 'updateItem'])->name('cart.update-item');
Route::delete('/cart/remove-item/{itemId}', [CartController::class, 'removeItem'])->name('cart.remove-item');
Route::post('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
Route::get('/cart/mini', [CartController::class, 'miniCart'])->name('cart.mini');

// طرق الدفع
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed/{order}', [CheckoutController::class, 'failed'])->name('checkout.failed');

// مسار dashboard محدث - استخدام الكونترولر الجديد
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        // التحقق من نوع المستخدم وتوجيهه للمكان المناسب
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        
        // توجيه المستخدمين العاديين إلى الكونترولر المناسب
        return app(UserDashboardController::class)->index();
    })->name('dashboard');
});

// مسارات الملف الشخصي
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// مسارات لوحة الإدارة
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // لوحة التحكم الرئيسية
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // إدارة المنتجات
    Route::controller(ProductsController::class)->prefix('products')->name('products.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{product}', 'show')->name('show');
        Route::get('/{product}/edit', 'edit')->name('edit');
        Route::put('/{product}', 'update')->name('update');
        Route::delete('/{product}', 'destroy')->name('destroy');
        Route::patch('/{product}/toggle-status', 'toggleStatus')->name('toggle-status');
    });
    
    // إدارة الطلبات
    Route::controller(OrdersController::class)->prefix('orders')->name('orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{order}', 'show')->name('show');
        Route::patch('/{order}/status', 'updateStatus')->name('update-status');
        Route::patch('/{order}/payment-status', 'updatePaymentStatus')->name('update-payment-status');
    });
    
    // إدارة المستخدمين
    Route::controller(UsersController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{user}', 'show')->name('show');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
        Route::patch('/{user}/toggle-status', 'toggleStatus')->name('toggle-status');
    });
    
    // إدارة القصص
    Route::controller(StoriesController::class)->prefix('stories')->name('stories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{story}', 'show')->name('show');
        Route::patch('/{story}/status', 'updateStatus')->name('update-status');
        Route::delete('/{story}', 'destroy')->name('destroy');
    });
    
    // إدارة الورش
    Route::controller(WorkshopsController::class)->prefix('workshops')->name('workshops.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{workshop}', 'show')->name('show');
        Route::get('/{workshop}/edit', 'edit')->name('edit');
        Route::put('/{workshop}', 'update')->name('update');
        Route::delete('/{workshop}', 'destroy')->name('destroy');
        Route::get('/{workshop}/registrations', 'registrations')->name('registrations');
        Route::patch('/registrations/{registration}/status', 'updateRegistrationStatus')->name('registrations.update-status');
    });
    
    // إدارة رسائل الاتصال
    Route::controller(ContactMessagesController::class)->prefix('contact-messages')->name('contact-messages.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{message}', 'show')->name('show');
        Route::patch('/{message}/mark-as-read', 'markAsRead')->name('mark-as-read');
        Route::patch('/{message}/mark-as-unread', 'markAsUnread')->name('mark-as-unread');
        Route::delete('/{message}', 'destroy')->name('destroy');
        Route::patch('/bulk-mark-as-read', 'bulkMarkAsRead')->name('bulk-mark-as-read');
        Route::delete('/bulk-delete', 'bulkDelete')->name('bulk-delete');
    });
    
    // إدارة المدونة
    Route::controller(AdminBlogController::class)->prefix('blog')->name('blog.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{post}', 'show')->name('show');
        Route::get('/{post}/edit', 'edit')->name('edit');
        Route::put('/{post}', 'update')->name('update');
        Route::delete('/{post}', 'destroy')->name('destroy');
        Route::patch('/{post}/toggle-status', 'toggleStatus')->name('toggle-status');
    });
      Route::controller(AdminController::class)->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', 'settingsIndex')->name('index'); // أو أي دالة عرض للإعدادات
    });
});

require __DIR__.'/auth.php';