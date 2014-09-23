Opauth-Strava
=============
[Opauth][1] strategy for Strava authentication.

Implemented based on http://strava.github.io/api/v3/oauth/ using OAuth 2.0.

Opauth is a multi-provider authentication framework for PHP.

Getting started
----------------
1. Install Opauth-Strava:
   ```bash
   cd path_to_opauth/Strategy
   git clone git://github.com/csl-web/opauth-strava.git Strava
   ```

2. Create a Strava APIs project at http://www.strava.com/developers
   - Make sure that website and redirect Domain is set

   
3. Configure Opauth-Stava strategy.

4. Direct user to `http://path_to_opauth/strava` to authenticate


Strategy configuration
----------------------

Required parameters:

```php
<?php
'Strava' => array(
	'client_id' => 'YOUR CLIENT ID',
	'client_secret' => 'YOUR CLIENT SECRET'
)
```

Optional parameters:
`scope`, `state`, `access_type`, `approval_prompt`

License
---------
Opauth-Strava is MIT Licensed  
Copyright © 2014 CSL-WEB (http://www.csl-web.com)

[1]: https://github.com/opauth/opauth