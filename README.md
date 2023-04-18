## Plugin View Loader
#### Plugin View Loader is a WordPress mu-plugin that allows you to load partial templates into your WordPress plugins.

Author: Cezar Popa

Version: 0.1

GitHub Repository: https://github.com/cezarpopa/plugin-view-loader/

### Installation

Clone the repository or download the zip file and extract it.
Copy the plugin-view-loader folder to your WordPress wp-content/mu-plugins directory.
Activate the plugin in your WordPress admin area.

### Usage

To get started, you can use the get_plugin_part() function to load the partial template. Here's an example:

```php
get_plugin_part(
    'your-plugin-name/path-to-view/slug',
    'name',
    ['foo' => 'bar']
); 
```

The first argument is the path to your partial template and must include the plugin name. This should be relative to your plugin directory. 

The second argument is the name of the partial template file (without the .php extension). Finally, the third argument is an array of arguments that you can pass to the template.

### Function Reference

**get_plugin_part()**

```php
/**
* @param string      $slug
* @param string|null $name
* @param array       $args
*
* @return string
  */
  function get_plugin_part(
  string $slug,
  string $name = null,
  array $args = []
  ): string
```
Use this function to load a partial template.

#### Parameters

* slug (string): The path to the partial template, relative to your plugin directory.
* name (string|null): The name of the partial template file (without the .php extension). Optional.
* args (array): An array of arguments to pass to the template. Optional.

##### Returns

* (string): The HTML output of the loaded partial template.

**get_plugin_partial_view()**

```php
/**
* @param array $templateNames
* @param bool  $requireOnce
* @param array $args
*
* @return string
  */
  function get_plugin_partial_path(
  array $templateNames,
  bool $requireOnce = true,
  array $args = []
  ): string
```
Use this function to get the template a string.

#### Parameters

* templateNames (array): An array of possible template names.
* requireOnce (bool): Whether to use require_once when loading the template. Defaults to true.
* args (array): An array of arguments to pass to the template.

##### Returns

* (string): The path to the partial template file. If the file is not found, an empty string ('') is returned.

