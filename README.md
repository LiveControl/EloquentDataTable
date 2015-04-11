# EloquentDataTable
Eloquent compatible DataTable plugin for server side ajax call handling.

If you are familiar with eloquent and would like to use it in combination with [datatables](https://www.datatables.net/) this is what you are looking for.

## Usage

### Step 1: Install through composer
```composer require livecontrol/eloquent-datatable```

### Step 2: Use it!
```php
$users = new \App\User();
$dataTable = new \LiveControl\EloquentDataTable\DataTable();
return response()->toJson($dataTable->make($users, ['email', 'firstname', 'lastname']));
```

## Examples

- [Basic setup](#basic-example)
- [Combining columns](#combining-columns)

### Basic example
```php
namespace Acme;

use LiveControl\EloquentDataTable\DataTable;

class UserController {
  $users = new \App\User();
  $dataTable = new DataTable();
  return response()->toJson(
    $dataTable->make(
      $users->where('city', '=', 'London'),
      ['email', 'firstname', 'lastname']
    )
  );
}
```
In this case we are making a datatable response with all users who live in London.

### Combining columns
If you want to combine the firstname and lastname into one column, you can wrap them into an array.
```php
namespace Acme;

use LiveControl\EloquentDataTable\DataTable;

class UserController {
  $users = new \App\User();
  $dataTable = new DataTable();
  return response()->toJson(
    $dataTable->make(
      $users,
      [
        'email',
        ['firstname', 'lastname'],
        'city'
      ]
    )
  );
}
```
