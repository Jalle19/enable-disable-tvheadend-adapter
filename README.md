enable-disable-tvheadend-adapter
================================

This is a simple script that can be used to programmatically enable/disable an adapter in tvheadend. This can be useful to work around buggy drivers that require the adapter's module to be reloaded. By using this script, the adapter can be freed and the module can be safely unloaded without having to stop and restart tvheadend itself.

## Requirements

The script requires PHP 5.3 with CLI and cURL support. On Debian/Ubuntu-based distributions this amounts to running `sudo apt-get install php5-cli php5-curl`.

## Usage

Open the script file in an editor and modify the adapter and tvheadend configuration, then run the script with `--enable` or `--disable` depending on what you want to do.

Beware that this has not been tested on DVB-S(2) adapters. It will most likely fail due to some missing parameters in the request. tvheadend will often crash if it receives malformed requests, so consider yourself warned.

When the script executes successfully you will see that the adapter has been enabled/disabled from the tvheadend logs.
