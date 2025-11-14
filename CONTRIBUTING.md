# Contributing

## Pull Requests

- **[PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)** - Check the code style with `composer check-style` and fix it with `composer fix-style`. This project uses PHP CS Fixer with tabs for indentation.
- **Static analysis** - Run `vendor/bin/phpstan analyse --level=5 src --memory-limit 1G` locally before opening a PR so CI doesn’t surprise you with type errors.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.
