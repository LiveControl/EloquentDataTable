# EloquentDataTable ![Build status](https://travis-ci.org/LiveControl/EloquentDataTable.svg?branch=master)
Eloquent compatible DataTable plugin for server side ajax call handling.

If you are familiar with eloquent and would like to use it in combination with [datatables](https://www.datatables.net/) this is what you are looking for.

## Usage

### Step 1: Install through composer
```composer require livecontrol/eloquent-datatable```

### Step 2: Use it!
```php
$users = new Models\User();
$dataTable = new \LiveControl\EloquentDataTable\DataTable($users, ['email', 'firstname', 'lastname']);
echo json_encode($dataTable->make());
```

## Examples

- [Basic setup](#basic-example)
- [Combining columns](#combining-columns)
- [Using raw column queries](#using-raw-column-queries)
- [Return custom row format](#return-custom-row-format)

### Basic example
```php
namespace Acme;

use LiveControl\EloquentDataTable\DataTable;

class UserController {
  ...
  public function datatable()
  {
    $users = new User();
    $dataTable = new DataTable(
      $users->where('city', '=', 'London'),
      ['email', 'firstname', 'lastname']
    );
    
    return response()->json($dataTable->make());
  }
}
```
In this case we are making a datatable response with all users who live in London.

### Combining columns
If you want to combine the firstname and lastname into one column, you can wrap them into an array.
```php
namespace Acme;

use LiveControl\EloquentDataTable\DataTable;

class UserController {
  ...
  public function datatable()
  {
    $users = new User();
    $dataTable = new DataTable(
      $users
      ['email', ['firstname', 'lastname'], 'city'],
    );
    
    return response()->json($dataTable->make());
  }
}
```
### Using raw column queries
Sometimes you want to use custom sql statements on a column to get specific results,
this can be achieved using the `ExpressionWithName` class.
```php
namespace Acme;

use LiveControl\EloquentDataTable\DataTable;
use LiveControl\EloquentDataTable\ExpressionWithName;

class UserController {
  ...
  public function datatable()
  {
    $users = new User();
    $dataTable = new DataTable(
      $users,
      [
        'email',
        new ExpressionWithName('`id` + 1000', 'idPlus1000'),
        'city'
      ]
    );
    
    return response()->json($dataTable->make());
  }
}
```

### Return custom row format
If you would like to return a custom row format you can do this by adding an anonymous function as an extra argument to the make method.
```php
namespace Acme;

use LiveControl\EloquentDataTable\DataTable;
use LiveControl\EloquentDataTable\ExpressionWithName;

class UserController {
  ...
  public function datatable()
  {
    $users = new User();
    $dataTable = new DataTable($users, ['email', ['firstname', 'lastname'], 'city']);
    
    $dataTable->setFormatRowFunction(function ($user) {
      $row = [];
      $row[] = $user->id;
      $row[] = '<a href="/users/'.$user->id.'">'.$user->firstnameLastname.'</a>';
      $row[] = '<a href="mailto:'.$user->email.'">'.$user->email.'</a>';
      $row[] = $user->city;
      $row[] = '<a href="/users/delete/'.$user->id.'">&times;</a>';
      return $row;
    });
    
    return response()->json($dataTable->make());
  }
}
```
