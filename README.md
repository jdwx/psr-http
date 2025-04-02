# psr-http

Module providing simple implementations of PHP HTTP PSR interfaces:

* PSR-7: HTTP message interfaces
* PSR-17: HTTP factory interfaces

## Installation

You can require it directly with Composer:

```bash
composer require jdwx/psr-http
```

Or download the source from GitHub: https://github.com/jdwx/psr-http.git

## Requirements

This module requires PHP 8.3 or later.

## Usage

This module doesn't really do much on its own. It provides simple implementations of the PSR HTTP interfaces, which can be useful for testing or for providing a base implementation that can be extended by other modules.

The implementations provided for MessageInterface, RequestInterface, and ServerRequest interface are composed entirely of traits that implement each property individually so that you can grab anything you need for your own implementation.

## Stability

This module is designed to stick as closely as possible to the PSR HTTP interfaces, so it should be fairly stable. Additional functionality may be somewhat more likely to evolve over time as more use cases are encountered.

## History

This module was refactored out of larger modules in early 2025.
