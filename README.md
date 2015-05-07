# EloquentDataTable [![Build status](https://travis-ci.org/LiveControl/EloquentDataTable.svg?branch=master)](https://travis-ci.org/LiveControl/EloquentDataTable)
Eloquent compatible DataTable plugin for server side ajax call handling.

If you are familiar with eloquent and would like to use it in combination with [datatables](https://www.datatables.net/) this is what you are looking for.

## Usage

### Step 1: Install through composer
```composer require livecontrol/eloquent-datatable```

### Step 2: Add DataTables javascript and set it up
For more information check out the [datatables manual](http://datatables.net/manual/index).
Make sure you have [csrf requests](http://laravel.com/docs/master/routing#csrf-protection) working with jquery ajax calls.
```javascript
var table = $('#example').DataTable({
  "processing": true,
  "serverSide": true,
  "ajax": {
    "url": "<url to datatable route>",
    "type": "POST"
  }
});
```

### Step 3: Use it
```php
$users = new Models\User();
$dataTable = new LiveControl\EloquentDataTable\DataTable($users, ['email', 'firstname', 'lastname']);
echo json_encode($dataTable->make());
```

### Optional step 4: Set versions of DataTables plugin.
Just initialize the DataTable object as you would normally and call the setVersionTransformer function as in the following example (for version 1.09 of DataTables):
```php
$dataTable->setVersionTransformer(new LiveControl\EloquentDataTable\VersionTransformers\Version109Transformer());
```
By default the plugin will be loading the transformer which is compatible with DataTables version 1.10.

## Examples

- [Basic setup](#basic-example)
- [Combining columns](#combining-columns)
- [Using raw column queries](#using-raw-column-queries)
- [Return custom row format](#return-custom-row-format)
- [Showing results with relations](#showing-results-with-relations)

### Basic example
```php
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
    
    return $dataTable->make();
  }
}
```
In this case we are making a datatable response with all users who live in London.

### Combining columns
If you want to combine the firstname and lastname into one column, you can wrap them into an array.
```php
use LiveControl\EloquentDataTable\DataTable;

class UserController {
  ...
  public function datatable()
  {
    $users = new User();
    $dataTable = new DataTable(
      $users,
      ['email', ['firstname', 'lastname'], 'city']
    );
    
    return $dataTable->make();
  }
}
```
### Using raw column queries
Sometimes you want to use custom sql statements on a column to get specific results,
this can be achieved using the `ExpressionWithName` class.
```php
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
    
    return $dataTable->make();
  }
}
```

### Return custom row format
If you would like to return a custom row format you can do this by adding an anonymous function as an extra argument to the make method.
```php
use LiveControl\EloquentDataTable\DataTable;

class UserController {
  ...
  public function datatable()
  {
    $users = new User();
    $dataTable = new DataTable($users, ['id', ['firstname', 'lastname'], 'email', 'city']);
    
    $dataTable->setFormatRowFunction(function ($user) {
      return [
        $user->id,
        '<a href="/users/' . $user->id . '">' . $user->firstnameLastname . '</a>',
        '<a href="mailto:' . $user->email . '">' . $user->email . '</a>',
        $user->city,
        '<a href="/users/delete/' . $user->id . '">&times;</a>'
      ];
    });
    
    return $dataTable->make();
  }
}
```


### Showing results with relations
```php
use LiveControl\EloquentDataTable\DataTable;

class UserController {
  ...
  public function datatable()
  {
    $users = new User();
    $dataTable = new DataTable(
    	$users->with('country'),
    	['name', 'country_id', 'email', 'id']
    );
    
    $dataTable->setFormatRowFunction(function ($user) {
    	return [
    		'<a href="/users/'.$user->id.'">'.$user->name.'</a>',
    		$user->country->name,
    		'<a href="mailto:'.$user->email.'">'.$user->email.'</a>',
    		'<a href="/users/delete/'.$user->id.'">&times;</a>'
    	];
    });
    
    return $dataTable->make();
  }
}
```
