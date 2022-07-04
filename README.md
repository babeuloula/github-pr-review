# Github PR Review

Stop wasting time on **code review**.
GitHub PR Review is an interface to simplify pull requests management on GitHub.

- **Built for developers**
  Track all pull requests from your teams, or your own pull requests, and do a code review easier.

- **Sort your pull requests**
  You can use both modes, _label_ or _filter_ and sort your pull requests.

- **Increase your productivity**
  In one single page you have all your watched repositories with the related pull requests and your notifications sorted
  by repositories.

## How to use

### Sort by filters

You can search for issues and pull requests globally across all of GitHub, or search for issues and pull requests within
a particular organization.

![Sort by filters](github-pr-review-filters.png)

[More information about filters](https://docs.github.com/en/search-github/searching-on-github/searching-issues-and-pull-requests)

### Sort by labels

You can sort you pull requests on four blocks Review needed, Accepted, Changes requested and WIP.
_Notification view is not possible with this mode_

![Sort by labels](github-pr-review-labels.png)

[More information about labels](https://docs.github.com/en/issues/using-labels-and-milestones-to-track-work/managing-labels)

## Installation

### Requirements

- [Docker](https://docs.docker.com/install/linux/docker-ce/ubuntu) >= 18.04.0
- [Docker compose](https://docs.docker.com/compose/install) >= 1.24.0

### For development and self-hosted

```bash
$ make install
```

Only in dev, you can use **[adminer](http://localhost:8012)** to see you database:
- Server: _mysql_
- Username: _env:MYSQL_USER_
- Password: _env:MYSQL_PASSWORD_
- Database: _env:MYSQL_DATABASE_

Be careful, _HTTP_HOST_ is the URL for dev and _DOMAINS_ is the domains for production for
[evertramos/nginx-proxy-automation](https://github.com/evertramos/nginx-proxy-automation)

### Connect to PHP's shell

```bash
$ make shell
```

## Update

Only in production, you can update the project with:

```bash
$ make update
```

## Check code quality

You can run all check with (outside of docker container):
```bash
$ make check
```

Or you can just run all check individually (into docker container):
```bash
$ make lint
$ make analyse
$ make copy-past
$ make doctrine
$ make security
```

## Contributors

- [ArthurHoaro](https://github.com/ArthurHoaro)
- [cyprille](https://github.com/cyprille)
- [ecourtial](https://github.com/ecourtial)
- [GijsGoudzwaard](https://github.com/GijsGoudzwaard)
- [tleon](https://github.com/tleon)
