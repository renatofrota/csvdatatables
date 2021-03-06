# CSV to DataTable Converter

## Description

This plugins creates a shortcode csvdatatables capable of convert CSV files to a DataTables and display anywhere on your WordPress website.

## Installation

1. Upload `csvdatatables` plugin directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `[csvdatatables]` in your pages or widgets

If you have wp-cli, you can install running:

```
wp plugin install https://github.com/renatofrota/csvdatatables/archive/master.zip
```

## Frequently Asked Questions

### How do I select the file that will be displayed

By default, the plugin will load the file `datatables.csv` from main upload directory (by default, `wp-content/uploads`).

Pass another filename with `file` argument:

```
[csvdatatables file=2017/10/yourfile.csv]
```

### What parameters are allowed?

- `file`: the file name (relative to uploads folder, or absolute if starting with `/`)
- `lang`: the language used in pagination and other labels (defaults to `en`, supported `pt`)
- `name`: the name of table element (`#table<name>`), defaults to '-csv' (`#table-csv`)
- `info`: display information about data entries above table header (defaults to true)
- `striplines`: array with a comma separated list of strings, lines containing them will be stripped off
- `stripheaders`: if set true, first line of CSV file will be stripped out (next line will be considered a header unless you pass `headers`)
- `headers`: comma separated list of your custom column headers
- `search`:  comma separated list of strings to search
- `replace`: comma separated list of strings to replace
- `ordercol`: column ID to sort data when page loads (defaults to `0`, i.e.: first column)
- `order`: asc or desc (defaults asc)
- `nosortcols`: set true to disable sorting
- `filtercol`: set the sortable column ID (defaults to 0, i.e.: the first column)
- `filter`: set false to disable searching/filtering
- `paging`: set false to disable pagination

## Screenshots

![A DataTable generated by this plugin](assets/screenshot-1.png)
![The source CSV file](assets/screenshot-2.png)

## Changelog

### 1.0 (2017-10-30)
* Initial release

## Donate

Think on how much $ you're saving and buy me some coffee! :)

> USD

[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R58RLRMM8YM6U)

> BRL

[![Doar](https://www.paypalobjects.com/pt_BR/i/btn/btn_donate_SM.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9JMBDY5QA8X5A)
