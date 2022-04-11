## Introduction
JsonHandler Extension v1.0.0

This extension allows users to load pages with valid json content and access the properties of the objects stored on the page. It can be used to store and load data for usage on different pages without the need of a database connection and with the possibility that everyone can change the data.

Pages can be loaded via `{{#json:<page>}}` and properties are accessed via `->`, e.g.: `{{#json:data.json->0->name}}`.

## License
This extension is licensed under GNU GPL 2.0 or any later version. See LICENSE for more license information.

## Installation
1. Download all files to $IP/extensions/JsonHandler where $IP is the root of your wiki install
2. Add `wfLoadExtension( 'JsonHandler' );` to your LocalSettings.php
3. Done

## Usage
The following examples assumes you have a page `data.json` that looks like this:
```json
{
    "1": "a",
    "2": {
        "a": "b",
        "c": "d"
    },
    "3": [
        "a",
        "b",
        "c"
    ]
}
```

### Normal usage
| Input                       | Output |
|-----------------------------|--------|
| `{{#json:data.json->1}}`    | a      |
| `{{#json:data.json->2->a}}` | b      |
| `{{#json:data.json->3->2}}` | c      |

### Errors
| Error                                              | Problem                                                                |
|----------------------------------------------------|------------------------------------------------------------------------|
| Can't read page: $1 does not exist.                | The page $1 does not exist                                             |
| No properties set.                                 | No properties are being accessed                                       |
| Error decoding JSON: $1 could not be decoded.      | The page $1 is not valid JSON                                          |
| Could not access property: $1 on $2.               | The property $1 does not exist on the stack trace of $2                |
| Properties don't return string on $1.              | The function tries to return an array or an object for the property $1 |
| Could not load page: Too many expensive functions. | More than `$wgExpensiveParserFunctionLimit` are tried to be loaded     |

### Special operators

#### The `@`-operator
When the property to be accessed is prefixed with the `@`-operator, any error that would occur in this step of accessing a property will be hidden and an empty string will be returned. If an error occured in an earlier step of accessing a property and wasn't hidden, the error will be shown.

| Input                         | Output                                          |
|-------------------------------|-------------------------------------------------|
| `{{#json:data.json->@1}}`     | a                                               |
| `{{#json:data.json->2->@b}}`  |                                                 |
| `{{#json:data.json->@3->@2}}` | c                                               |
| `{{#json:data.json->@4}}`     |                                                 |
| `{{#json:data.json->4->@b}}`  | `Could not access property: 4 on data.json->4.` |

#### The `?`-operator
When a property to be accessed is prefixed with the `?`-operator, it is checked if the data the property is trying to access on is a string. If it already is a string, usually that would result in a `Could not access property: $1 on $2.` error, but here the current value is instead returned. Any other errors however will still be shown. To hide those, a combination with the `@`-operator is possible, but the `@` has to come before the `?`.

| Input                               | Output                                             |
|-------------------------------------|----------------------------------------------------|
| `{{#json:data.json->?1}}`           | a                                                  |
| `{{#json:data.json->?2}}`           | `Properties don't return string on data.json->?2.` |
| `{{#json:data.json->3->2->?5}}`     | c                                                  |
| `{{#json:data.json->3->2->?5->?6}}` | c                                                  |
| `{{#json:data.json->?4}}`           | `Could not access property: 4 on data.json->?4.`   |
| `{{#json:data.json->@?4}}`          |                                                    |

#### The `*`-operator
The `*`-operator is used for debugging applications and can not be used in combination with the other two operators, otherwise their functionality will take priority. The `*`-operator will print the stack trace as well as the data at that point. Be careful with large data sets as they will be printed entirely.

| Input                       | Output                                                            |
|-----------------------------|-------------------------------------------------------------------|
| `{{#json:data.json->*}}`    | `data.json->*: {"1":"a","2":{"a":"b","c":"d"},"3":["a","b","c"]}` |
| `{{#json:data.json->2->*}}` | `data.json->2->*: {"a":"b","c":"d"}`                              |
| `{{#json:data.json->1->*}}` | `data.json->1->*: "a"`                                            |