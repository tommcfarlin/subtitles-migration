# Subtitle Migration

Migrates old theme subtitles to compatibility with the Subtitles plugin. This is for developers. Review the readme.

## How It Works

**Note:** This is a developer-specific plugin. In the main plugin file, the `$oldMetaKey` value will need to be changed to reflect whatever value is being used for subtitles.

1. Upon activation, this plugin will ask if you'd like to migrate old subtitles to the new subtitles.
2. If you opt to do so, then an SQL query will be made and the page will redirect.

After the plugin as run, you will see either:

* A success message saying that you can deactivate the plugin,
* An error message saying that you need to review the error log.

The error log can be found in the root of your `wp-content` directory under `debug.log`.

## Installation

### Using The WordPress Dashboard

1. Navigate to the 'Add New' Plugin Dashboard
2. Select `subtitles-migration.zip` from your computer
3. Upload
4. Activate the plugin on the WordPress Plugin Dashboard

### Using FTP

1. Extract `subtitles-migration.zip` to your computer
2. Upload the `subtitles-migration` directory to your `wp-content/plugins` directory
3. Activate the plugin on the WordPress Plugins Dashboard

## Notes

The WordPress plugin to which all of the information is migration is [Subtitles](https://wordpress.org/plugins/subtitles/). You can also find the code on [this GitHub page](https://github.com/wecobble/Subtitles).
