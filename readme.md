Nette TypeFixer 🆙
==================

[![Downloads this Month](https://img.shields.io/packagist/dm/nette/type-fixer.svg)](https://packagist.org/packages/nette/type-fixer)
[![Build Status](https://travis-ci.org/nette/type-fixer.svg?branch=master)](https://travis-ci.org/nette/type-fixer)
[![Latest Stable Version](https://poser.pugx.org/nette/type-fixer/v/stable)](https://github.com/nette/type-fixer/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/nette/type-fixer/blob/master/license.md)


Introduction
------------

A tool to automatically update type hints in your PHP code.

If you like Nette, **[please make a donation now](https://nette.org/donate)**. Thank you!


Usage
-----

```
typefixer [options] <directory>

Options:
    -i | --ignore <mask>  Directories to ignore
    -f | --fix            Fixes files
```

Example:

```
typefixer --fix /myproject
```


Installation
------------

It requires PHP version 7.1 and supports PHP up to 7.4.

Install it via Composer. This project is not meant to be run as a dependency, so install it as standalone:

```
composer create-project nette/type-fixer
```

Or install it globally via:

```
composer global require nette/type-fixer
```

and make sure your global vendor binaries directory is in [your `$PATH` environment variable](https://getcomposer.org/doc/03-cli.md#global).
