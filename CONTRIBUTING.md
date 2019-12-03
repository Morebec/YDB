## Introduction
Thank you for considering contributing to YDB. We are very grateful for any help you might offer.
There are many ways to contribute:
- Writing tutorials or blog posts 
- Improving the documentation, 
- Providing help to fellow developers,
- Submitting bug reports and feature requests
- Writing improvements and bug fixes which can be incorporated into YDB itself.

This document outlines a few guidelines in order to help developers contribute to the project in a standardized way, making things easier for everyone.

## Conventions

### Coding Standard
A coding standard is a necessary mean to ensure that developers can work on the project's code
in a way that is consistent and familiar for everyone. We have decided to follow the PSR-1, PSR-2 and PSR-4 standards to align ourselves with a great amount of well-known PHP projects.

To enforce these rules, we rely on the [PHP-CS-Fixer Tool](https://cs.symfony.com/).

### Git
#### Commit messages
In order to have a usable git history please follow these guidelines:
- Commit subject line:
    - The subject line should always start with a capital letter.
    - DO NOT end the subject line with a period.
    - Separate the subject line and the body with a blank line.
    - The subject line should start with the imperative mood. (Add XYZ, Removed, XYZ, Fix XYZ).
- Commit body:
    - In the commit body, explain *what* has been done and *why* as opposed to *how* that should already be explained by the code.
- If the commit tackles a specific issue, reference the isse with words like:
    - Closes #123
    - Fixes #123
#### Branching workflow
The branching model we use is as follows (inspired by git flow):
- `master`: used for bringing forth production releases
- `dev`: contains the latest developments in preparation for a next release.
- `feature/xyz`: used for the development of a specific XYZ feature
- `bugfix/xyz`: used for bugfixes
- `release/major.minor.patch`: used for releases.
- `hotfix/xyz`: Used for production hot fixes. Hotfixes arise from the necessity to act immediately upon an undesired state of a live production version.

### Versioning
For versioning, we follow the Semantic Versioning Guideline, which sates that:
> Given a version number MAJOR.MINOR.PATCH, increment the:

>   - MAJOR version when you make incompatible API changes,
>   - MINOR version when you add functionality in a backwards compatible manner, and
>   - PATCH version when you make backwards compatible bug fixes.


### Changelog
The changelog is maintained according to the following guideline: https://keepachangelog.com/en/1.0.0/

### Deprecating code
Sometimes a feature cannot be implemented or changed without breaking backward compatibility, and therefore causing breaking changes. To minize this as much as possible and offer users of the project a
way to either use the old implementation or the new alternative, we rely on the concept of deprecation.
With deprecation we can mark classes or methods as "deprecated" generating warnings and indicating that these implementation details will be removed in the next versions.

To declare a method, class or property as deprecated, we use the `@deprecated` in a PHPDoc block annotation.

A deprecation notice should indicate the version at which it started being deprecated, and when possible, the way it has been replaced.

E.g.: 
```php
/**
 * @deprecated since version 1.3, use Replacement instead.
 */
```

When the replacement for this feature is located in a different namespace than the deprecated class, use its FQCN:

```php
/**
 * @deprecated since version 1.3, use A\B\Replacement instead.
 */
```
Also, to help developers understand the deprecation and update their code, in the deprecated class, method or property, trigger a PHP `E_USER_DEPRECATED` error:

```php
@trigger_error(sprintf('The "%s" class is deprecated since version 1.3, use "%s" instead.', Deprecated::class, Replacement::class), E_USER_DEPRECATED);

```

Next, add the deprecation notice in the `CHANGELOG.md` file:

```md
1.3
-----

* Deprecated the `Deprecated` class, use `Replacement` instead.
```

Finally, bump the minor of the version.

In summary, to deprecated a piece of code:

    1. Add a PHPDoc block for the deprecated class, method or property.
    1. Trigger a PHP `E_USER_DEPRECATED` error.
    1. Document the deprecation to the `CHANGELOG.md` file.
    1. Bump minor of the versio

### Removing deprecated code
The removal of deprecated code should only be done at least on the next major version. 
Once a deprecation has been removed, document it in the `CHANGELOG.md` file:

```md
2.0
---
* Removed the `Deprecated` class, use `Replacement` instead.
```

In summary, to remove a deprecated piece of code:

    1. Remove the PHPDoc block for the deprecated class, method or property.
    1. Remove the PHP `E_USER_DEPRECATED` error trigger.
    1. Document the removal of the deprecation in the `CHANGELOG.md` file.
