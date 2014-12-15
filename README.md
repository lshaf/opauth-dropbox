Opauth-Dropbox
=============
[Opauth][1] strategy for Dropbox authentication.

Implemented based on https://www.dropbox.com/developers/core/docs

Getting started
----------------
1. Install Opauth-Dropbox:
   ```bash
   cd path_to_opauth/Strategy
   git clone https://github.com/lshaf/opauth-dropbox.git Facebook
   ```

2. Create Facebook application at https://www.dropbox.com/developers/apps/

3. Configure Opauth-Dropbox strategy with at least `App key` and `App Secret`.

4. Direct user to `http://path_to_opauth/facebook` to authenticate

Strategy configuration
----------------------

Required parameters:

```php
<?php
'Dropbox' => array(
  'key' => 'YOUR APP KEY',
  'secret' => 'YOUR APP SECRET',
)
```

License
---------
Opauth-Dropbox is MIT Licensed  
Copyright © 2014 L Shaf (http://pictalogi.com/)

[1]: https://github.com/opauth/opauth
