# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [1.0.3] - 2020-06-15

### Changed

* Tests etc. are now ignored for archive exports

## [1.0.2] - 2020-04-30

### Fixed

* `Mapper::stringToCodeUnits()` raised the wrong exception for `Class::method` when a class named `Class` exists but does not have a method named `method`

## [1.0.1] - 2020-04-27

### Fixed

* [#2](https://github.com/sebastianbergmann/code-unit/issues/2): `Mapper::stringToCodeUnits()` breaks when `ClassName<extended>` is used for class that extends built-in class

## [1.0.0] - 2020-03-30

* Initial release

[1.0.3]: https://github.com/sebastianbergmann/code-unit/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/sebastianbergmann/code-unit/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/sebastianbergmann/code-unit/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/sebastianbergmann/code-unit/compare/530c3900e5db9bcb8516da545bef0d62536cedaa...1.0.0
