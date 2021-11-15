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