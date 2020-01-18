# Ampere
Ampere is a simple and flexible admin panel for Laravel framework.

## Sections
1. Installation
2. Configuration
3. Routing
4. Menu
5. Controller
6. ACL
7. Views
8. Grid
9. Forms

### Installation
Install package
```
composer required exeplor/ampere
```
Add service provider to app.php
```php
'providers' => [
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,
    // ...
    \Ampere\AmpereServiceProvider::class,
    // ...
];
```

Create new ampere space and follow the instructions
```
php artisan ampere:install
```

Run migrations after first install
```
php artisan ampere:migrate
```

### Configuration
```php
return [

    /*
     * Main configuration
     */
    'app' => [

        /*
         * Ampere url path
         */
        'url_prefix' => 'myadminpanel',
    ],

    /*
     * Routing configuration
     */
    'routing' => [

        /*
         * Controllers folder
         */
        'folder' => 'app/Http/Controllers/Admin',

        /*
         * Controller namespace
         */
        'namespace' => 'App\Http\Controllers\Admin',

        /*
         * Space route prefix
         * Route space name
         */
        'prefix' => 'my.custom.name',
        // Example: route('my.custom.name.some.method')

        /*
         * Custom route group
         */
        'group' => [
            // If you have some specific middleware or other config
            // This place for Route::group() arguments
            'domain' => env('APP_DOMAIN')
        ]
    ],

    /*
     * Ampere DB configuration
     */
    'db' => [
        'prefix' => 'amp_'
    ],

    /*
     * Authorization config
     */
    'auth' => [

        /*
         * Captcha settings
         */
        'google_captcha' => [
            'enabled' => env('AMPERE_CAPTCHA_ENABLED', false),
            'site_key' => env('AMPERE_CAPTCHA_SITE_KEY'),
            'secret_key' => env('AMPERE_CAPTCHA_SECRET_KEY')
        ]
    ],

    /*
     * Installation settings
     */
    'install' => [
        /*
         * Ampere public assets folder
         */
        'assets_folder' => 'vendor/admin'
    ],

    /*
     * Views configuration
     */
    'views' => [
        'name' => 'admin'
    ]
];
```

### Routing
Ampere have dynamic route generation. All routes are built automatically depending on the name of the controller and parent folder. Also you can customize route using controller and method annotations.

```php
/**
 * Class HomeController
 * @package App\Http\Controllers\Admin
 */
class HomeController extends Controller 
{
    /**
     * @return void
     */
    public function foo()
    {
        // Route result
        // GET /admin/home/foo
    }
    
    /**
     * @route bar
     */
    public function firstMethod()
    {
        // Result
        // GET /admin/home/bar
    }
    
    /**
     * @route entity/{id}
     */
    public function secondMethod(int $id)
    {
        // Result
        // GET /admin/home/entity/{$id}
    }
    
    /**
     * @route update/{id}
     * @post
     */
    public function thirdMethod(int $id)
    {
        // Result
        // POST /admin/home/update/{$id}
    }
    
    /**
     * @post secondMethod
     */
    public function fourthMethod(int $id)
    {
        // You can inherit route by method name in this controller
        // @post <controller_method_name>
        
        // Result
        // POST /admin/home/entity/{$id}
    }
    
    /**
     * @route some/route
     * @put
     */
    public function fifthMethod()
    {
        // Result
        // PUT /admin/home/some/route
    }
    
    /**
     * @route some/route
     * @delete
     */
    public function sixthMethod()
    {
        // Result
        // DELETE /admin/home/some/route
    }
}
```

Now you can get any route by specific controller method
```php
$route = HomeController::route('secondMethod', [1]);
echo $route->route(); # return "ampere.home.someroutepath"
echo $route->url(); # return "home/entity/1"

if ($route->access()) {
    echo 'This route available for current user'; 
}
```

### Menu
Menu will be generated automatically. You can modify your menu in menu config file:
```
/resources/ampere/<ampere_space_name>/menu.php
```

Use annotations in controller methods for creating menu items.
```php
/**
 * Class HomeController
 * @package App\Http\Controllers\Admin
 */
class HomeController extends Controller
{
    /**
     * @route foo
     * @menu Home
     */
    public function home()
    {
        // Menu items:
        // Home > [link to "home/foo"]
    }
    
    /**
     * @menu Items > First
     */
    public function first()
    {
        // Menu items:
        // Home [link to "home/foo"]
        // Items
        //     First [link to "home/first"]
    }
    
    /**
     * @menu Items > Second
     */
    public function first()
    {
        // Menu items
        // Home [link to "home/foo"]
        // Items (submenu item)
        //     First [link to "home/first"]
        //     Second [link to "home/second"]
    }
}
```

### Controller
In most cases you will need to create CRUD controller. Ampere has special command to automate this task.

```
php artisan am:crud Users/Permissions
```

Any CRUD controller have model. Ampere analyzes the table structure and model structure, creating validation and relationships based on this data. Also this method generate view.

### ACL
From the box you have full ACL for all routes, menu items in all controllers with their method.
You can manage roles and permissions from the controller which created on installation.

### Views
For render view in Ampere space you need to call render() function in you controller.
```php
public function home()
{
    return $this->render('home', ['value' => 'hello']);
}
```

View example
```html
<?php
    /**
     * @var \Ampere\Services\Workshop\Page\Layout $layout Layout configuration
     * @var \Ampere\Services\Workshop\Component $component Components manager
     * @var \Ampere\Services\Workshop\Page\Assets $include Assets manager
     * @var \Ampere\Services\Workshop\Form\Form $form Form builder
     * @var object $data Your arguments here
     */
?>

@php($layout->title('Users'))
@php($component->show('header', [
    'title' => 'Users',
    'subtitle' => 'List of Users',
    'buttons' => [
        'create' => [
            'title' => 'Create new User',
            'route' => \App\Http\Controllers\Admin\UsersController::route('create'),
            'type' => 'primary'
        ]
    ]
]))

<div class="ibox">
    <div class="ibox-body ibox-nopadding">
        Some content
    </div>
</div>

```

You can create new Ampere page using command:
```
php artisan am:page mypage
```
And your page will be created in /resources/views/<ampere_space_name>/pages/mypage.blade.php

### Grid
It's a default Ampere library for data search.
Basic exmaple:
```php
use Ampere\Services\Grid\Grid;

/**
 * Class UsersController
 * @package App\Http\Controllers\Admin
 */
class UsersController extends Controller
{
    /**
     * @menu Users
     * @param Grid $grid
     */
    public function index(Grid $grid)
    {
        $grid
            ->column('id', '#')->strict()->sortable()->asc()
            ->column('name', 'Name')->search()
            ->column('email', 'Email')->search()
            ->column('is_active')->dropdown([
                0 => 'No',
                1 => 'Yes'
            ]);

        $grid->action('icon:flag-checkered')->success()->route(self::route('stats'), 'id');
        $grid->action('icon:pencil-alt')->primary()->route(self::route('edit'), 'id');
        $grid->action('icon:trash-alt', 'delete')->danger()->route(self::route('delete'), 'id');

        $grid->model(\App\Models\User::class)->limit(24)->search();
        return $this->render('users.index', ['mygrid' => $grid]);
    }
}
```

Also you can use relation values to display and search.
```php
$grid
    ->column('id', '#')->sortable()->asc()
    ->column('role.title')->search()
    ->column('owner.email')->search()
```

Render your grid in view
```html
<div class="ibox">
    <div class="ibox-body ibox-nopadding">
        @php($component->grid($data->mygrid))
    </div>
</div>
```

Column description
```php
$column = $grid->column('id', '#');
$column->strict(); // Strict search
$column->search(); // Default match search
$column->sortable(); // Column can by sortable
$column->asc(); // Default sort by ASC
$column->desc(); // Default sort by DESC
$column->date(); // Datetime range filtration

$column->dropdown([
    'first' => 'First value',
    'second' => 'Second value'
]); // Dropdown column type

$column->display(function($rowModel){
    return 'Display custom value ' . $rowModel->someValue;
});
```

Add actions to your grid
```php
$button = $grid->action('Edit');

// Colorize your button
$button->primary(); // Primary color
$button->success();  // Success color
$button->danger();  // Danger color

// Custom route
$button->route(function(\App\Models\User $user){
    return route('some.route.user', $user->id);
});

// Ampere route
$button->route('users.show', 'id'); // second argument

// Similarly
$button->route(SomeController::route('show'), 'id');
```

### Forms
Build form with basic fields:

```html
<!-- Open form and use model if entity exists  -->
{!! $form->open()->model($user) !!}

<!-- Input -->
{!!
    $form
        ->input('name', 'Personal name') 
        ->type('number') // this is a "type" attribute of input
        ->value('my default value') // Filled automatically if model exists end field is not empty
        ->placeholder('Enter your name')
        ->disabled() // Disable field
        ->inline() // Show as inline form component
        ->error('Some error') // Show force error
!!}

<!-- Dropdown -->
{!! $form
        ->select('dropdown', 'Dropdown type')
        ->options([
            'first_key' => 'First value',
            'second_key' => 'Second value'
        ])
        ->multiple() // Can choice many items
        ->tags() // Can choice custom string value. Works with multiple() type
        ->source(UserController::route('search')) // Use lifesearch
        ->placeholder('My placeholder')
        ->error('Some error')
!!}

<!-- Textarea -->
{!!
    $form
        ->textarea('about', 'Information about user')
        ->value('Custom value')
        ->rows(20)
        ->placeholder('My placeholder')
        ->error('Some error')
!!}

<!-- Radio type -->
{!!
    $form
        ->radio('radio_buttons', 'Some radio')
        ->items([
            'new' => 'New article',
            'draft' => 'Draft article',
            'published' => 'Published article'
        ])
        ->value('draft')
!!}

<!-- Checkbox -->
{!!
    $form->checkbox('checkbox_item', 'Status checked')
        ->title('Active status')
        ->checked()
!!}

<!-- Close form -->
{!! $form->close() !!}
```
All components works with validation errors. You can use your own components without builder.
