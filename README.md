Opauth-Yahoojp
=============
[Opauth][1] strategy for Yahoo! JAPAN(YConnect) v2.

Implemented based on https://developer.yahoo.co.jp/yconnect/v2/ using OAuth 2.0.

Opauth is a multi-provider authentication framework for PHP.

Getting started
----------------
1. Install Opauth-Yahoojp:
   ```bash
   cd path_to_opauth/Strategy
   git clone git@github.com:ritou/opauth-yahoojp.git Yahoojp
   ```

2. Create a server-side application at https://e.developer.yahoo.co.jp/dashboard/
   - Select 'develop new application' button and create new service
   - Make sure that redirect URI is set to actual OAuth 2.0 callback URL, usually `http://path_to_opauth/yahoojp/oauth2callback`

   
3. Configure Opauth-Yahoojp strategy.

4. Direct user to `http://path_to_opauth/yahoojp` to authenticate


Strategy configuration
----------------------

Required parameters:

```php
<?php
'Yahoojp' => array(
	'client_id' => 'YOUR CLIENT ID',
	'client_secret' => 'YOUR CLIENT SECRET'
)
```

Optional parameters:
`scope`,`state`


References
----------
- [YConnect](https://developer.yahoo.co.jp/yconnect/)
- [YConnect v2](https://developer.yahoo.co.jp/yconnect/v2/)
- [YConnect Server-Side Application document](https://developer.yahoo.co.jp/yconnect/server_app/explicit/)
- [Transition from YConnect v1 to v2](https://developer.yahoo.co.jp/yconnect/v1_to_v2.html)

License
---------
Opauth-Yahoojp is MIT Licensed  
Copyright © 2012 Ryo Ito (https://github.com/ritou)

[1]: https://github.com/uzyn/opauth
