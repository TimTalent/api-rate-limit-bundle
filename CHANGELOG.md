CHANGELOG
=========

v0.6.0
------

* Symfony 6.4 and 7.x compatibility (Symfony 5.4 still supported)
* API Platform 3 compatibility (`_api_operation_name` is used when `_api_item_operation_name` is missing)
* Require PHP 8.1+, Symfony 5.4+ and API Platform 2.7+
* Use `isMainRequest()` and `TokenInterface::getUserIdentifier()` instead of the APIs removed in Symfony 6
* Add native return types
* Remove phpspec/prophecy usage from the test suite and test against Symfony 5.4, 6.4 and 7.x in CI

v0.5.0
------

* Allow PHP8, switch to Github action (#18)
* Enable rate limiting based on API Operations annotations (#17)

v0.4.0
------

* Symfony 5 compatibility (#14)

v0.3.0
------

* Feature. Configuration per using annotations (#9)
* Feature. Rate limit to specific methods (#12)
* Fix null ApiRateLimit instance in RateLimitHandler (#11)

v0.2.1
------

* Adding Symfony4 support (#5)
* Fix dependencies for Symfony Flex

v0.2.0
------

* Feature. Using PSR/Cache instead of DoctrineCache (#2)
* Feature. Role based rate limit (#3)
* Deprecated the `indragunawan_api_rate_limit.storage` configuration key.
* Deprecated the `indragunawan_api_rate_limit.throttle.period` and `indragunawan_api_rate_limit.throttle.limit` configuration key.

v0.1.0
------

* Initial release
